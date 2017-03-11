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

use Shop\Storage\CouponMapperInterface;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Text\Math;
use Cms\Service\AbstractManager;

final class CouponManager extends AbstractManager implements CouponManagerInterface
{
    /**
     * Any-compliant coupon mapper
     * 
     * @var \Shop\Storage\CouponMapperInterface
     */
    private $couponMapper;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\CouponMapperInterface $couponMapper
     * @return void
     */
    public function __construct(CouponMapperInterface $couponMapper)
    {
        $this->couponMapper = $couponMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $coupon = new VirtualEntity();
        $coupon->setId($row['id'], VirtualEntity::FILTER_INT)
               ->setCode($row['code'], VirtualEntity::FILTER_TAGS)
               ->setPercentage($row['percentage'], VirtualEntity::FILTER_INT);

        return $coupon;
    }

    /**
     * Find outs the discount price by coupon code
     * 
     * @param string $code Coupon code
     * @param string $price Total price
     * @return string|boolean
     */
    public function getDiscountByCode($code, $price)
    {
        $item = $this->couponMapper->findByCode($code);

        if (!empty($item)) {
            return Math::fromPercentage($price, $item['percentage']);
        } else {
            return false;
        }
    }

    /**
     * Fetches coupon entity by its associated ID
     * 
     * @param string $id
     * @return \Krystal\Stdlib\VirtualEntity
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->couponMapper->fetchById($id));
    }

    /**
     * Fetch all coupons
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->prepareResults($this->couponMapper->fetchAll());
    }

    /**
     * Deletes a coupon by its associated ID
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->couponMapper->deleteById($id);
    }

    /**
     * Returns last coupon ID
     * 
     * @return string
     */
    public function getLastId()
    {
        return $this->couponMapper->getLastId();
    }

    /**
     * Updates a coupon
     * 
     * @param array $input
     * @return boolean
     */
    public function update(array $input)
    {
        return $this->couponMapper->persist($input);
    }

    /**
     * Adds a coupon
     * 
     * @param array $input
     * @return boolean
     */
    public function add(array $input)
    {
        return $this->couponMapper->persist($input);
    }
}
