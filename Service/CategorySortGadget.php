<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Service;

use Krystal\Form\Gadget\DataSorter;
use Krystal\Http\PersistentStorageInterface;

final class CategorySortGadget extends DataSorter
{
    const SORT_ORDER = 'order';
    const SORT_TITLE = 'title';
    const SORT_PRICE_DESC = 'price_desc';
    const SORT_PRICE_ASC = 'price_asc';
    const SORT_DATE_DESC = 'date_desc';
    const SORT_DATE_ASC = 'date_asc';

    /**
     * Create sorting rules by column name
     * 
     * @param string $column
     * @return array
     */
    public static function createSortingRules($column)
    {
        // Defaults
        $desc = false;
        $order = array('id');

        switch ($column) {

            case CategorySortGadget::SORT_ORDER:
                $order = array('order');
            break;

            case CategorySortGadget::SORT_TITLE:
                $order = array('title');
            break;

            case CategorySortGadget::SORT_PRICE_DESC:
                $order = array('regular_price');
                $desc = true;
            break;

            case CategorySortGadget::SORT_PRICE_ASC:
                $order = array('regular_price');
            break;

            case CategorySortGadget::SORT_DATE_DESC:
                $order = array('date', 'id');
                $desc = true;
            break;

            case CategorySortGadget::SORT_DATE_ASC:
                $order = array('date', 'id');
            break;
        }

        return array(
            'columns' => $order,
            'desc' => $desc
        );
    }

    /**
     * State initialization
     * 
     * @param \Krystal\Http\PersistentStorageInterface $storage
     * @return void
     */
    public function __construct(PersistentStorageInterface $storage)
    {
        parent::__construct($storage, 'cat_sort', self::SORT_DATE_DESC, array(
            self::SORT_ORDER => 'By position',
            self::SORT_TITLE => 'By title',
            self::SORT_PRICE_ASC => 'By price - from lower to higher',
            self::SORT_PRICE_DESC => 'By price - from higher to lower',
            self::SORT_DATE_DESC => 'By date added - from newest to oldest',
            self::SORT_DATE_ASC => 'By date added - from oldest to newest'
        ));
    }
}
