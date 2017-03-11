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
use Shop\Storage\CouponMapperInterface;

final class CouponMapper extends AbstractMapper implements CouponMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_shop_coupons');
    }

    /**
     * Deletes a coupon by its associated ID
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->deleteByPk($id);
    }

    /**
     * Finds a coupon by its associated cod
     * 
     * @param string $code
     * @return array
     */
    public function findByCode($code)
    {
        return $this->db->select('*')
                        ->from(self::getTableName())
                        ->whereEquals('code', $code)
                        ->query();
    }

    /**
     * Finds a coupon by its associated ID
     * 
     * @param string $id Coupon ID
     * @return array
     */
    public function fetchById($id)
    {
        return $this->findByPk($id);
    }

    /**
     * Fetch all coupons
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->db->select('*')
                        ->from(self::getTableName())
                        ->orderBy($this->getPk())
                        ->desc()
                        ->queryAll();
    }
}
