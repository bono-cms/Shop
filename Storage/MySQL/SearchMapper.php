<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Storage\MySQL;

use Search\Storage\MySQL\AbstractSearchProvider;
use Cms\Storage\MySQL\AbstractMapper;
use Krystal\Db\Sql\QueryBuilderInterface;

final class SearchMapper extends AbstractMapper
{
    /**
     * {@inheritDoc}
     */
    public function appendQuery(QueryBuilderInterface $queryBuilder, $placeholder)
    {
        // Columns to be selected
        $columns = array(
            ProductMapper::column('id'),
            ProductTranslationMapper::column('web_page_id'),
            ProductTranslationMapper::column('lang_id'),
            ProductTranslationMapper::column('title'),
            ProductTranslationMapper::column('description'),
            ProductTranslationMapper::column('name')
        );

        $queryBuilder->select($columns)
                     ->from(ProductMapper::getTableName())
                     // Translation relation
                     ->innerJoin(ProductTranslationMapper::getTableName(), array(
                        ProductMapper::column('id') => ProductTranslationMapper::column('id')
                     ))
                     // Filtering conditions
                     ->whereEquals(ProductMapper::column('seo'), '1')
                     ->andWhereEquals(ProductTranslationMapper::column('lang_id'), "'{$this->getLangId()}'")
                     ->rawAnd()
                     ->openBracket()
                     // Search
                     ->like(ProductTranslationMapper::column('name'), $placeholder)
                     ->rawOr()
                     ->like(ProductTranslationMapper::column('description'), $placeholder)
                     ->closeBracket();
    }
}
