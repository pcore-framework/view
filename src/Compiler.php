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

/**
 * Class Compiler
 * @package PCore\View
 * @github https://github.com/pcore-framework/view
 */
class Compiler
{

    /**
     * @var string|null
     */
    protected ?string $parent;

    /**
     * @var Blade
     */
    protected Blade $blade;

    public function __construct(Blade $blade)
    {
        $this->blade = $blade;
    }

    /**
     * @param $template
     * @return string
     */
    public function compile($template): string
    {
        $compileDir = $this->blade->getCompileDir();
        $compiledFile = $compileDir . md5($template) . '.php';
        if (file_exists($compiledFile) === false) {
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
            '/\{\{((--)?)([\s\S]*?)\\1\}\}/' => [$this, 'compileEchos']
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
     * @return string
     */
    protected function compileEchos(array $matches)
    {
        if ($matches[1] === '') {
            return sprintf('<?php echo htmlspecialchars((string)%s, ENT_QUOTES); ?>', $matches[3]);
        }
    }

}