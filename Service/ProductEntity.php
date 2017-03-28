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
use Krystal\Text\Math;
use DateTime;

final class ProductEntity extends VirtualEntity
{
    /**
     * Checks whether product has been added to wishlist
     * 
     * @return boolean
     */
    public function inWishlist()
    {
        return $this->getWishlistProductId() == $this->getId();
    }

    /**
     * Determines whether product is new (added several days ago)
     * 
     * @return boolean
     */
    public function isNew()
    {
        $dateTime = new DateTime($this->getDate());
        $dateInterval = $dateTime->diff(new DateTime());

        return $dateInterval->format('%a') <= 3;
    }

    /**
     * Returns product stoke price
     * 
     * @param boolean $format Whether to format the outputting number
     * @return mixed
     */
    public function getStokePrice($format = false)
    {
        return $this->createPrice('stokeprice', $format);
    }

    /**
     * Returns product price
     * 
     * @param boolean $format Whether to format the outputting number
     * @return mixed
     */
    public function getPrice($format = false)
    {
        return $this->createPrice('price', $format);
    }

    /**
     * Returns converted price
     * 
     * @param string $code Currency code
     * @param boolean $format Whether to format the outputting number
     * @return string
     */
    public function getConvertedPrice($code, $format = true)
    {
        return $this->createConvertedPrice($this->getPrice(), $code, $format);
    }

    /**
     * Returns converted stoke price
     * 
     * @param string $code Currency code
     * @param boolean $format Whether to format the outputting number
     * @return string
     */
    public function getConvertedStokePrice($code, $format = true)
    {
        return $this->createConvertedPrice($this->getStokePrice(), $code, $format);
    }

    /**
     * Creates a price
     * 
     * @param string $property Virtual property
     * @param boolean $format Whether to format the outputting number
     * @return mixed
     */
    private function createPrice($property, $format)
    {
        $property = $this->container[$property];

        if ($format === true) {
            $property = number_format($property);
        }

        return $property;
    }

    /**
     * Creates converted price
     * 
     * @param string $target Target price
     * @param string $code Currency code
     * @param boolean $format Whether to format the outputting number
     * @return string
     */
    private function createConvertedPrice($target, $code, $format)
    {
        foreach ($this->getCurrencies() as $currency) {
            if ($currency['code'] == $code) {
                $price = round($target * $currency['value'], 2);

                if ($format === true) {
                    $price = number_format($price);
                }

                return $price;
            }
        }

        return null;
    }

    /**
     * Returns image URL
     * 
     * @param string $size
     * @return string
     */
    public function getImageUrl($size)
    {
        // The image bag is only available on valid IDs
        if ($this->getId()) {
            return $this->getImageBag()->getUrl($size);
        }
    }

    /**
     * Checks whether product has static attributes
     * 
     * @return boolean
     */
    public function hasStaticAttributes()
    {
        return (bool) $this->getStaticAttributes();
    }

    /**
     * Checks whether product has dynamic attributes
     * 
     * @return boolean
     */
    public function hasDynamicAttributes()
    {
        return (bool) $this->getDynamicAttributes();
    }

    /**
     * Checks whether a product has attached similar products
     * 
     * @return boolean
     */
    public function hasRecommendedProducts()
    {
        return (bool) $this->getRecommendedProducts();
    }

    /**
     * Checks whether a product has attached similar products
     * 
     * @return boolean
     */
    public function hasSimilarProducts()
    {
        return (bool) $this->getSimilarProducts();
    }

    /**
     * Returns sale percentage
     * 
     * @return integer
     */
    public function getSalePercentage()
    {
        if ($this->hasStokePrice()) {
            // Calculate relative percentage
            $percentage = Math::percentage($this->getPrice(), $this->getStokePrice());

            // Normalize the percentage
            $percentage = abs(floor($percentage));

            return 100 - $percentage;
        } else {
            return 0;
        }
    }

    /**
     * Checks whether a product is not of out stoke
     * 
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->getInStock() > 0;
    }

    /**
     * Checks whether this product is marked as a special offer
     * 
     * @return boolean
     */
    public function isSpecialOffer()
    {
        return $this->getSpecialOffer();
    }

    /**
     * Checks whether product has its stoke price
     * 
     * @return boolean
     */
    public function hasStokePrice()
    {
        return $this->getStokePrice() > 0;
    }

    /**
     * Tells whether product is in stoke's state
     * 
     * @return boolean
     */
    public function inStoke()
    {
        return $this->hasStokePrice();
    }
}
