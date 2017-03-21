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
use Krystal\Session\SessionBagInterface;
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
     * Session bag service
     * 
     * @var \Krystal\Session\SessionBagInterface 
     */
    private $sessionBag;

    const STORAGE_DISCOUNT_KEY = 'discount_applied';

    /**
     * State initialization
     * 
     * @param \Shop\Storage\CouponMapperInterface $couponMapper
     * @param \Krystal\Session\SessionBagInterface $sessionBag
     * @return void
     */
    public function __construct(CouponMapperInterface $couponMapper, SessionBagInterface $sessionBag)
    {
        $this->couponMapper = $couponMapper;
        $this->sessionBag = $sessionBag;
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
     * Returns applied discount price
     * 
     * @return string
     */
    public function getAppliedDiscount()
    {
        if ($this->isApplied()) {
            return $this->sessionBag->get(self::STORAGE_DISCOUNT_KEY);
        } else {
            return 0;
        }
    }

    /**
     * Determines whether discount has been applied
     * 
     * @return boolean
     */
    public function isApplied()
    {
        return $this->sessionBag->has(self::STORAGE_DISCOUNT_KEY);
    }

    /**
     * Applies a discount price by coupon code
     * 
     * @param string $code Coupon code
     * @param string $price Total price
     * @return string|boolean
     */
    public function applyDiscountByCode($code, $price)
    {
        $discount = $this->getDiscountByCode($code, $price);

        // Stop returning false, if wrong code supplied
        if ($discount === false) {
            return false;
        } else {
            $this->sessionBag->set(self::STORAGE_DISCOUNT_KEY, $discount);
            return $discount;
        }
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
