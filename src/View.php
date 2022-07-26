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
        $engine = 'PCore\View\/' . $config['engine'];
        $options = $config['options'];
        $this->engine = new $engine($options);
    }

}