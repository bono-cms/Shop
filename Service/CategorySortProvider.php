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

use Krystal\Form\Providers\DataSorter;
use Krystal\Http\PersistentStorageInterface;

final class CategorySortProvider extends DataSorter
{
	const SORT_ORDER = 'order';
	const SORT_TITLE = 'title';
	const SORT_PRICE_DESC = 'price_desc';
	const SORT_PRICE_ASC = 'price_asc';
	const SORT_TIMESTAMP_DESC = 'timestamp_desc';
	const SORT_TIMESTAMP_ASC = 'timestamp_asc';

	/**
	 * State initialization
	 * 
	 * @param \Krystal\Http\PersistentStorageInterface $storage
	 * @return void
	 */
	public function __construct(PersistentStorageInterface $storage)
	{
		parent::__construct($storage, 'cat_sort', self::SORT_ORDER, array(
			self::SORT_ORDER => 'By position',
			self::SORT_TITLE => 'By title',
			self::SORT_PRICE_ASC => 'By price - from lower to higher',
			self::SORT_PRICE_DESC => 'By price - from higher to lower',
			self::SORT_TIMESTAMP_DESC => 'By date added - from newest to oldest',
			self::SORT_TIMESTAMP_ASC => 'By date added - from oldest to newest'
		));
	}
}
