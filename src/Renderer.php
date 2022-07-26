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
        protected ViewEngineInterface $engine
    )
    {
    }

    /**
     * @param string $template
     * @param array $arguments
     * @return string
     */
    public function render(string $template, array $arguments = []): string
    {
        ob_start();
        echo (string)$this->engine->render($template, $arguments);
        return (string)ob_get_clean();
    }

}