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
use Shop\Storage\AttributeGroupMapperInterface;

final class AttributeGroupMapper extends AbstractMapper implements AttributeGroupMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_attribute_groups');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return AttributeGroupTranslationMapper::getTableName();
    }

    /**
     * Fetch all groups
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->db->select('*')
                        ->from(self::getTableName())
                        ->orderBy('id')
                        ->desc()
                        ->queryAll();
    }

    /**
     * Deletes a group by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function deleteById($id)
    {
        return $this->deleteByPk($id);
    }

    /**
     * Fetches category by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function fetchById($id)
    {
        return $this->findByPk($id);
    }

    /**
     * Inserts a group
     * 
     * @param array $input
     * @return boolean
     */
    public function insert(array $input)
    {
        return $this->persist($input);
    }

    /**
     * Updates a group
     * 
     * @param array $input
     * @return boolean
     */
    public function update(array $input)
    {
        return $this->persist($input);
    }
}
