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
use Shop\Storage\ImageMapperInterface;
use Krystal\Db\Sql\RawSqlFragment;

final class ImageMapper extends AbstractMapper implements ImageMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_product_images');
    }

    /**
     * Fetches image's file name by its associated id
     * 
     * @param string $id Image's id
     * @return string
     */
    public function fetchFileNameById($id)
    {
        return $this->findColumnByPk($id, 'image');
    }

    /**
     * Fetches all images by associated product ID
     * 
     * @param string $productId Product ID
     * @param boolean $published Whether to fetch only published items
     * @param integer|boolean $limit Optional images limit to be returned
     * @return array
     */
    public function fetchAllByProductId($productId, $published, $limit)
    {
        $db = $this->db->select('*')
                       ->from(self::getTableName())
                       ->whereEquals('product_id', $productId);

        if ($published === true) {
            $db->andWhereEquals('published', '1')
               ->orderBy(new RawSqlFragment(sprintf('`order`, CASE WHEN `order` = 0 THEN %s END DESC', self::getFullColumnName('id'))));
        } else {
            $db->orderBy('id')
               ->desc();
        }

        if ($limit !== false) {
            $db->limit($limit);
        }

        return $db->queryAll();
    }

    /**
     * Adds an image
     * 
     * @param string $productId
     * @param string $image Image's file name
     * @param string $order Sort order
     * @param string $published Whether image is published or not by default
     * @return boolean
     */
    public function insert($productId, $image, $order, $published)
    {
        return $this->db->insert(static::getTableName(), array(
            'product_id' => $productId,
            'image' => $image,
            'order' => $order,
            'published' => $published
            
        ))->execute();
    }

    /**
     * Updates image's filename by its associated id
     * 
     * @param string $id Image id
     * @param string $filename Image filename
     * @return boolean
     */
    public function updateFileNameById($id, $filename)
    {
        return $this->updateColumnByPk($id, 'image', $filename);
    }

    /**
     * Updates sort order by image's associated id
     * 
     * @param string $id Image's id
     * @param string $order New sort order
     * @return boolean
     */
    public function updateOrderById($id, $order)
    {
        return $this->updateColumnByPk($id, 'order', $order);
    }

    /**
     * Updates image's published state
     * 
     * @param string $id Image's id
     * @param string $published New state, either 1 or 0
     * @return boolean
     */
    public function updatePublishedById($id, $published)
    {
        return $this->updateColumnByPk($id, 'published', $published);
    }

    /**
     * Delete an image by its associated id
     * 
     * @param string $id
     * @return boolean Depending on success
     */
    public function deleteById($id)
    {
        return $this->deleteByPk($id);
    }

    /**
     * Deletes all images by associated product id
     * 
     * @param string $productId
     * @return boolean
     */
    public function deleteAllByProductId($productId)
    {
        return $this->deleteByColumn('product_id', $productId);
    }
}
