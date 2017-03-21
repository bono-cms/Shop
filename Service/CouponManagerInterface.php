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

interface CouponManagerInterface
{
    /**
     * Returns applied discount price
     * 
     * @return string
     */
    public function getAppliedDiscount();

    /**
     * Determines whether discount has been applied
     * 
     * @return boolean
     */
    public function isApplied();

    /**
     * Applies a discount price by coupon code
     * 
     * @param string $code Coupon code
     * @param string $price Total price
     * @return string|boolean
     */
    public function applyDiscountByCode($code, $price);

    /**
     * Find outs the discount price by coupon code
     * 
     * @param string $code Coupon code
     * @param string $price Total price
     * @return string|boolean
     */
    public function getDiscountByCode($code, $price);

    /**
     * Fetches coupon entity by its associated ID
     * 
     * @param string $id
     * @return \Krystal\Stdlib\VirtualEntity
     */
    public function fetchById($id);

    /**
     * Fetch all coupons
     * 
     * @return array
     */
    public function fetchAll();
    
    /**
     * Deletes a coupon by its associated ID
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Returns last coupon ID
     * 
     * @return string
     */
    public function getLastId();

    /**
     * Updates a coupon
     * 
     * @param array $input
     * @return boolean
     */
    public function update(array $input);

    /**
     * Adds a coupon
     * 
     * @param array $input
     * @return boolean
     */
    public function add(array $input);
}
