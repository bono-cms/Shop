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

use Krystal\Stdlib\VirtualEntity;
use Cms\Service\AbstractManager;
use Shop\Storage\SpecificationItemMapperInterface;

final class SpecificationItemService extends AbstractManager
{
    /**
     * Any compliant specification item mapper
     * 
     * @var \Shop\Storage\SpecificationItemMapperInterface
     */
    private $specificationItemMapper;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\SpecificationItemMapperInterface $specificationItemMapper
     * @return void
     */
    public function __construct(SpecificationItemMapperInterface $specificationItemMapper)
    {
        $this->specificationItemMapper = $specificationItemMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setCategoryId($row['category_id'])
               ->setLangId($row['lang_id'])
               ->setOrder($row['order'])
               ->setFront($row['front'], VirtualEntity::FILTER_BOOL)
               ->setType($row['type'], VirtualEntity::FILTER_INT)
               ->setName($row['name'])
               ->setHint($row['hint']);

        return $entity;
    }

    /**
     * Returns last ID
     * 
     * @return int
     */
    public function getLastId()
    {
        return $this->specificationItemMapper->getLastId();
    }

    /**
     * Deletes an item by its ID
     * 
     * @param int $id Item ID
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->specificationItemMapper->deleteByPk($id);
    }

    /**
     * Fetch all items
     * 
     * @param int $categoryId Optional category ID filter
     * @return array
     */
    public function fetchAll($categoryId = null)
    {
        return $this->prepareResults($this->specificationItemMapper->fetchAll($categoryId));
    }

    /**
     * Fetch item by its ID
     * 
     * @param int $id Item ID
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return mixed
     */
    public function fetchById($id, $withTranslations)
    {
        if ($withTranslations === true) {
            return $this->prepareResults($this->specificationItemMapper->fetchById($id, true));
        } else {
            return $this->prepareResult($this->specificationItemMapper->fetchById($id, false));
        }
    }

    /**
     * Saves an item
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->specificationItemMapper->saveEntity($input['item'], $input['translation']);
    }
}
