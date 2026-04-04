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
        $this->cart->clear(); 
    }
}
