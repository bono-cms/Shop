<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Controller\Admin;

use Krystal\Validation\Validator;
use Cms\Controller\Admin\AbstractConfigController;

final class Config extends AbstractConfigController
{
    /**
     * {@inheritDoc}
     */
    protected function configureValidator(Validator $validator)
    {
        $validator->field('config.default_category_per_page_count', 'Default category per page count')
                  ->required()
                  ->addRule('numeric')
                  ->addRule('greaterthan', null, ['min' => 0]);

        $validator->field('config.currency', 'Currency')
                  ->required()
                  ->addRule('notags');

        $validator->field('config.showcase_count', 'Showcase count')
                  ->required()
                  ->addRule('numeric')
                  ->addRule('greaterthan', null, ['min' => 0]);

        $validator->field('config.category_cover_height', 'Category cover height')
                  ->required()
                  ->addRule('numeric');

        $validator->field('config.category_cover_width', 'Category cover width')
                  ->required()
                  ->addRule('numeric');

        $validator->field('config.cover_width', 'Cover width')
                  ->required()
                  ->addRule('numeric');

        $validator->field('config.cover_height', 'Cover height')
                  ->required()
                  ->addRule('numeric');

        $validator->field('config.cover_height', 'Thumb height')
                  ->required()
                  ->addRule('numeric');

        $validator->field('config.cover_width', 'Thumb width')
                  ->required()
                  ->addRule('numeric');
    }
}