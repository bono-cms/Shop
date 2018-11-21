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

use Cms\Storage\MySQL\AbstractMapper;
use Shop\Storage\AttributeValueMapperInterface;

final class AttributeValueMapper extends AbstractMapper implements AttributeValueMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_attribute_values');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return AttributeValueTranslationMapper::getTableName();
    }

    /**
     * Fetch all values filtered by group id
     * 
     * @param string $groupId
     * @return array
     */
    public function fetchAllByCategoryId($groupId)
    {
        return $this->db->select('*')
                        ->from(self::getTableName())
                        ->whereEquals('group_id', $groupId)
                        ->orderBy('id')
                        ->desc()
                        ->queryAll();
    }

    /**
     * Deletes a value by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function deleteById($id)
    {
        return $this->deleteByPk($id);
    }

    /**
     * Fetches value by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function fetchById($id)
    {
        return $this->findByPk($id);
    }

    /**
     * Inserts a value
     * 
     * @param array $input
     * @return boolean
     */
    public function insert(array $input)
    {
        return $this->persist($input);
    }

    /**
     * Updates a value
     * 
     * @param array $input
     * @return boolean
     */
    public function update(array $input)
    {
        return $this->persist($input);
    }
}
