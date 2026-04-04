<?php

/**
 * This file is part of the Bono CMS
 *
 * Copyright (c) No Global State Lab
 */

namespace Shop\Service;

use Krystal\Cart\ShoppingCart;
use Krystal\Security\Filter;
use Krystal\Image\Tool\ImageBag;
use Cms\Service\WebPageManagerInterface;
use Shop\Storage\ProductMapperInterface;

final class BasketManager
{
    /** @var \Shop\Storage\ProductMapperInterface */
    private $productMapper;

    /** @var \Krystal\Image\Tool\ImageBagInterface */
    private $imageBag;

    /** @var \Cms\Service\WebPageManagerInterface */
    private $webPageManager;

    /** @var \Krystal\Cart\ShoppingCart */
    private $cart;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\ProductMapperInterface $productMapper
     * @param \Cms\Service\WebPageManagerInterface $webPageManager
     * @param \Krystal\Image\Tool\ImageBag $imageBag
     * @param \Krystal\Cart\ShoppingCart $cart
     */
    public function __construct(
        ProductMapperInterface $productMapper,
        WebPageManagerInterface $webPageManager,
        ImageBag $imageBag,
        ShoppingCart $cart
    ){
        $this->productMapper = $productMapper;
        $this->webPageManager = $webPageManager;
        $this->imageBag = $imageBag;
        $this->cart = $cart;
    }

    /**
     * Returns total products quantity and total price
     * 
     * @return array
     */
    public function getAllStat()
    {
        $data = $this->cart->toArray();
        $summary = $data['summary'];

        return [
            'totalQuantity' => $summary['totalQuantity'],
            'totalPrice' => $summary['total'] // Mapped from 'total' in Cart component
        ];
    }

    /**
     * Returns statistics for a specific product in the basket
     *
     * @param int|string $id Product ID
     * @return array|bool Returns stats array on success, false if product not in basket
     */
    public function getProductStat($id)
    {
        $row = $this->findProductInRows($this->getProducts(), $id);

        if ($row !== false) {
            return [
                'id' => $row['id'],
                'qty' => $row['qty'],
                'price' => $row['price'],
                'subTotal' => $row['qty'] * $row['price'],
                'name' => $row['name'] ?? '',
                'exists' => true
            ];
        }

        return false;
    }

    /**
     * Returns all product entities stored in the basket
     * 
     * @return array
     */
    public function getProducts()
    {
        $data = $this->cart->toArray();
        $items = $data['items'];
        $entities = [];

        if (empty($items)) {
            return [];
        }

        // Optimization: fetch all products in one query
        $ids = array_column($items, 'productId');
        $rows = $this->productMapper->fetchByIds($ids);

        foreach ($items as $item) {
            $product = $this->findProductInRows($rows, $item['productId']);

            if ($product) {
                $entities[] = $this->createEntity($product, $item);
            } else {
                // Cleanup: product no longer exists in DB
                $this->cart->remove($item['productId'], $item['attributes']);
            }
        }

        return array_reverse($entities);
    }

    /**
     * Adds a specific product variant to the basket
     * 
     * @param string|int $id The main product ID
     * @param string|int $variantId The specific variant/option ID
     * @param int $qty Quantity to be added
     * @param float|int $price Unit price for this specific variant
     * @return boolean Depending on whether the item was successfully added to the cart
     */
    public function addVariant($id, $variantId, $qty, $price)
    {
        return $this->cart->add($id, $qty, ['variant_id' => $variantId], $price);
    }

    /**
     * Adds a product to the basket
     * 
     * @param string $id Product id
     * @param integer $qty Quantity
     * @param array $attributes Optional attributes
     * @return boolean
     */
    public function add($id, $qty, array $attributes = [])
    {
        $product = $this->productMapper->fetchById($id);

        if ($product) {
            $price = $this->getPrice($product);
            return $this->cart->add($id, $qty, $attributes, $price);
        }

        return false;
    }

    /**
     * Recounts quantity for a specific variant
     * 
     * @param string $id Product id
     * @param integer $qty New quantity
     * @param array $attributes Variant attributes
     * @return boolean
     */
    public function recount($id, $qty, array $attributes = [])
    {
        return $this->cart->update($id, $attributes, ['quantity' => $qty]);
    }

    /**
     * Recounts quantity for a specific product variant
     * 
     * @param string|int $id Product id
     * @param string|int $variantId Specific variant id
     * @param int|string $qty New quantity
     * @return boolean
     */
    public function recountVariant($id, $variantId, $qty)
    {
        return $this->recount($id, $qty, ['variant_id' => $variantId]);
    }

    /**
     * Removes a product variant from the basket
     * 
     * @param string $id Product id
     * @param array $attributes Variant attributes
     * @return boolean
     */
    public function remove($id, array $attributes = [])
    {
        return $this->cart->remove($id, $attributes);
    }

    /**
     * Removes a specific product variant from the basket
     * 
     * @param string|int $id The main product ID
     * @param string|int $variantId The specific variant ID to be removed
     * @return boolean True on success, false otherwise
     */
    public function removeVariant($id, $variantId)
    {
        return $this->remove($id, ['variant_id' => $variantId]);
    }

    /**
     * Logic for calculating the current price
     * 
     * @param array $product
     * @return float
     */
    private function getPrice(array $product)
    {
        return (float) ((isset($product['stoke_price']) && $product['stoke_price'] > 0) 
                ? $product['stoke_price'] 
                : $product['regular_price']);
    }

    /**
     * Mapping DB row + Cart data to BasketEntity
     */
    private function createEntity(array $product, array $item)
    {
        $imageBag = clone $this->imageBag;
        $imageBag->setId((int) $product['id'])
                 ->setCover(Filter::escape($product['cover']));

        $entity = new BasketEntity();
        $entity->setId($product['id'], BasketEntity::FILTER_INT)
               ->setVariantId($item['attributes']['variant_id'] ?? null)
               ->setName($product['name'], BasketEntity::FILTER_HTML)
               ->setInStock($product['in_stock'], ProductEntity::FILTER_INT)
               ->setUrl($this->webPageManager->surround($product['slug'], $product['lang_id']))
               ->setImageBag($imageBag)
               ->setQty($item['quantity'])
               ->setPrice($item['price'])
               ->setSubTotalPrice($item['total'])
               ->setAttributes($item['attributes']);

        return $entity;
    }

    /**
     * Finds a product by its ID within a given collection of rows
     *
     * @param array $rows A collection of product arrays to search through
     * @param int|string $id The unique identifier of the product
     * @return array|bool Returns the product row array if found, false otherwise
     */
    private function findProductInRows(array $rows, $id)
    {
        foreach ($rows as $row) {
            if ($row['id'] == $id) {
                return $row;
            }
        }

        return false;
    }

    /**
     * Checks whether the current basket is empty
     *
     * @return bool True if the basket contains no items, false otherwise
     */
    public function isEmpty()
    {
        return $this->cart->isEmpty(); 
    }

    /**
     * Clears all items and metadata from the current basket
     *
     * @return void
     */
    public function clear()
    { 
        return $this->cart->clear();
    }
}
