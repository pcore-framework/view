<?php

declare(strict_types=1);

namespace PCore\View;

use PCore\View\Contracts\ViewEngineInterface;

/**
 * Class Renderer
 * @package PCore\View
 * @github https://github.com/pcore-framework/view
 */
class Renderer
{

    public function __construct(
        protected ViewEngineInterface $engine,
        protected array               $arguments = []
    )
    {
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function assign(string $name, mixed $value): void
    {
        $this->arguments[$name] = $value;
    }

    /**
     * @param string $template
     * @param array $arguments
     * @return string
     */
    public function render(string $template, array $arguments = []): string
    {
        ob_start();
        echo (string)$this->engine->render($template, array_merge($this->arguments, $arguments));
        return (string)ob_get_clean();
    }

}