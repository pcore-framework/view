<?php

declare(strict_types=1);

namespace PCore\View;

use PCore\View\Contracts\ViewEngineInterface;

/**
 * Class View
 * @package PCore\View
 * @github https://github.com/pcore-framework/view
 */
class View
{

    /**
     * @var ViewEngineInterface|mixed
     */
    protected ViewEngineInterface $engine;

    public function __construct(array $config)
    {
        $this->engine = new $config['engine']($config['options']);
    }

    /**
     * @param string $template
     * @param array $arguments
     * @return string
     */
    public function render(string $template, array $arguments = []): string
    {
        return $this->getRenderer()->render($template, $arguments);
    }

    /**
     * @return Renderer
     */
    public function getRenderer(): Renderer
    {
        return new Renderer($this->engine);
    }

}