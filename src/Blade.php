<?php

declare(strict_types=1);

namespace PCore\View;

use PCore\Utils\Traits\AutoFillProperties;
use PCore\View\Contracts\ViewEngineInterface;
use PCore\View\Exceptions\ViewNotExistException;
use function func_get_arg;

/**
 * Class Blade
 * @package PCore\View
 * @github https://github.com/pcore-framework/view
 */
class Blade implements ViewEngineInterface
{

    use AutoFillProperties;

    /**
     * @var string
     */
    protected string $suffix = '.blade.php';

    /**
     * @var string
     */
    protected string $compileDir;

    /**
     * @var string
     */
    protected string $path;

    public function __construct(array $options)
    {
        $this->fillProperties($options);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @return string
     */
    public function getCompileDir(): string
    {
        return $this->compileDir;
    }

    /**
     * @param string $template
     * @param array $arguments
     */
    public function render(string $template, array $arguments = []): void
    {
        $this->renderView($template, $arguments);
    }

    /**
     * @throws ViewNotExistException
     */
    protected function renderView(): void
    {
        extract(func_get_arg(1));
        include (new Compiler($this))->compile(func_get_arg(0));
    }

}