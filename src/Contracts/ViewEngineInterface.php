<?php

declare(strict_types=1);

namespace PCore\View\Contracts;

/**
 * Interface ViewEngineInterface
 * @package PCore\View\Contracts
 * @github https://github.com/pcore-framework/view
 */
interface ViewEngineInterface
{

    public function render(string $template, array $arguments = []);

}