<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Storage;

interface CouponMapperInterface
{
    /**
     * Deletes a coupon by its associated ID
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Finds a coupon by its associated ID
     * 
     * @param string $id Coupon ID
     * @return array
     */
    public function fetchById($id);

    /**
     * Fetch all coupons
     * 
     * @return array
     */
    public function fetchAll();
}
