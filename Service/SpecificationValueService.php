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

use Krystal\Stdlib\ArrayUtils;
use Krystal\Stdlib\VirtualEntity;
use Cms\Service\AbstractManager;
use Shop\Storage\SpecificationValueMapperInterface;
use Shop\Storage\SpecificationCategoryMapperInterface;

final class SpecificationValueService extends AbstractManager
{
    /**
     * Any compliant mapper
     * 
     * @var \Shop\Storage\SpecificationValueMapperInterface
     */
    private $specificationValueMapper;

    /**
     * Any compliant category mapper
     * 
     * @var \Shop\Storage\SpecificationCategoryMapperInterface
     */
    private $specificationCategoryMapper;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\SpecificationValueMapperInterface $specificationValueMapper
     * @param \Shop\Storage\SpecificationCategoryMapperInterface $specificationCategoryMapper
     * @return void
     */
    public function __construct(SpecificationValueMapperInterface $specificationValueMapper, SpecificationCategoryMapperInterface $specificationCategoryMapper)
    {
        $this->specificationValueMapper = $specificationValueMapper;
        $this->specificationCategoryMapper = $specificationCategoryMapper;
    }

    /**
     * Find values by product ID
     * 
     * @param int $id Product ID
     * @param boolean $withTranslations Whether to fetch translations or not
     * @param boolean $extended Whether to fetch all columns
     * @return array
     */
    public function findByProduct($id, $withTranslations = true, $extended = true)
    {
        $items = $this->specificationValueMapper->findByProduct($id, $withTranslations, $extended);
        $categories = $this->specificationCategoryMapper->fetchAll();

        $partitions = ArrayUtils::arrayPartition($items, 'category_id');

        $output = array();

        // Process merging items with categories
        foreach ($partitions as $categoryId => $partition) {
            foreach ($categories as $category) {
                if ($category['id'] == $categoryId) {
                    // Add on demand
                    if (!isset($output[$category['name']])) {
                        $output[$category['name']] = array();
                    }

                    // Finally merge
                    $output[$category['name']] += $partition;
                }
            }
        }

        return $output;
    }
}
