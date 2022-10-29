<?php

declare(strict_types=1);

namespace PCore\View;

use PCore\View\Exceptions\ViewNotExistException;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function md5;
use function mkdir;
use function sprintf;
use function str_replace;
use function trim;

/**
 * Class Compiler
 * @package PCore\View
 * @github https://github.com/pcore-framework/view
 */
class Compiler
{

    /**
     * @var array
     */
    protected array $sections = [];

    /**
     * @var string|null
     */
    protected ?string $parent;

    public function __construct(
        protected Blade $blade
    )
    {
    }

    /**
     * @param $template
     * @return string
     */
    public function compile(string $template): string
    {
        $compileDir = $this->blade->getCompileDir();
        $compiledFile = $compileDir . md5($template) . '.php';
        if ($this->blade->isCache() === false || file_exists($compiledFile) === false) {
            !is_dir($compileDir) && mkdir($compileDir, 0755, true);
            $content = $this->compileView($template);
            while (isset($this->parent)) {
                $parent = $this->parent;
                $this->parent = null;
                $content = $this->compileView($parent);
            }
            file_put_contents($compiledFile, $content, LOCK_EX);
        }
        return $compiledFile;
    }

    /**
     * @param string $file
     * @return string
     */
    protected function compileView(string $file): string
    {
        return preg_replace_callback_array([
            '/\{\{((--)?)([\s\S]*?)\\1\}\}/' => [$this, 'compileEchos'],
            '/\{!!([\s\S]*?)!!\}/' => [$this, 'compileRaw'],
            '/@(.*?)\((.*)?\)/' => [$this, 'compileFunc'],
            '/@(section|switch)\((.*?)\)([\s\S]*?)@end\\1/' => [$this, 'compileParcel'],
            '/@(php|else|endphp|endforeach|endfor|endif|endunless|endempty|endisset)/' => [$this, 'compileDirective']
        ], $this->readFile($this->getRealPath($file)));
    }

    /**
     * @param string $template
     * @return bool|string
     */
    protected function readFile(string $template): bool|string
    {
        if (file_exists($template) && (false !== ($content = file_get_contents($template)))) {
            return $content;
        }
        throw new ViewNotExistException($template . ' не существует');
    }

    /**
     * @param string $template
     * @return string
     */
    protected function getRealPath(string $template): string
    {
        return sprintf(
            '%s%s%s',
            $this->blade->getPath(),
            str_replace('.', '/', $template),
            $this->blade->getSuffix()
        );
    }

    /**
     * @param array $matches
     * @return string
     */
    protected function compileEchos(array $matches): string
    {
        if ($matches[1] === '') {
            return sprintf('<?php echo htmlspecialchars((string)%s, ENT_QUOTES); ?>', $matches[3]);
        }
    }

    /**
     * @param array $matches
     * @return string
     */
    protected function compileRaw(array $matches): string
    {
        return sprintf('<?php echo %s; ?>', $matches[1]);
    }

    /**
     * @param string $value
     * @return string
     */
    protected function trim(string $value): string
    {
        return trim($value, '\'" ');
    }

    /**
     * @param array $matches
     * @return string
     */
    protected function compileParcel(array $matches): string
    {
        [$directive, $condition, $segment] = array_slice($matches, 1);
        switch ($directive) {
            case 'section':
                $this->sections[$this->trim($condition)] = $segment;
                break;
            case 'switch':
                $segment = preg_replace(
                    ['/@case\((.*)\)/', '/@default/'],
                    ['<?php case \\1: ?>', '<?php default: ?>'],
                    $segment
                );
                return sprintf('<?php switch(%s): ?>%s<?php endswitch; ?>', $condition, trim($segment));
        }
    }


    /**
     * @param array $matches
     * @return string
     */
    protected function compileDirective(array $matches): string
    {
        return match ($directive = $matches[1]) {
            'php' => '<?php ',
            'endphp' => '?>',
            'else' => '<?php else: ?>',
            'endisset', 'endunless', 'endempty' => '<?php endif; ?>',
            default => sprintf('<?php %s; ?>', $directive)
        };
    }

    /**
     * @param array $matches
     * @return mixed|string|void
     */
    protected function compileFunc(array $matches)
    {
        [$func, $arguments] = [$matches[1], $this->trim($matches[2])];
        switch ($func) {
            case 'include':
                return $this->compileView($arguments);
            case 'extends':
                $this->parent = $arguments;
                break;
            case 'if':
            case 'elseif':
                return sprintf('<?php %s (%s): ?>', $func, $arguments);
            case 'unless':
                return sprintf('<?php if (!(%s)): ?>', $arguments);
            case 'empty':
            case 'isset':
                return sprintf('<?php if (%s(%s)): ?>', $func, $arguments);
            case 'for':
            case 'foreach':
                return sprintf('<?php %s(%s): ?>', $func, $arguments);
            case 'yield':
                $value = array_map([$this, 'trim'], explode(',', $arguments, 2));
                return $this->sections[$value[0]] ?? ($value[1] ?? '');
            default:
                return $matches[0];
        }
    }

}