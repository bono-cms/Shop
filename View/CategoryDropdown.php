<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\View;

use Krystal\Tree\AdjacencyList\Render\Dropdown;

final class CategoryDropdown extends Dropdown
{
    /**
     * State initialization
     * 
     * @param array $options
     * @return void
     */
    public function __construct(array $options)
    {
        parent::__construct('name', $options);
    }
}
