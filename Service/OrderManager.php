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

use Cms\Service\WebPageManagerInterface;
use Cms\Service\AbstractManager;
use Cms\Service\MailerInterface;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Stdlib\ArrayUtils;
use Krystal\Security\Filter;
use Krystal\Db\Filter\FilterableServiceInterface;
use Shop\Storage\OrderInfoMapperInterface;
use Shop\Storage\OrderProductMapperInterface;
use Shop\Module;

final class OrderManager extends AbstractManager implements OrderManagerInterface, FilterableServiceInterface
{
    /**
     * Any compliant order information mapper
     * 
     * @var \Shop\Storage\OrderMapperInterface
     */
    private $orderInfoMapper;

    /**
     * Any compliant order's product mapper
     * 
     * @var \Shop\Storage\OrderProductMapperInteface
     */
    private $orderProductMapper;

    /**
     * Basket manager
     * 
     * @var \Shop\Service\BasketManagerInterface
     */
    private $basketManager;

    /**
     * Web page service
     * 
     * @var \Cms\Service\WebPageManagerInterface
     */
    private $webPageManager;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\OrderInfoMapperInterface $orderMapper
     * @param \Shop\Storage\OrderProductMapperInterface $orderProductMapper
     * @param \Shop\Service\BasketManagerInterface $basketManager
     * @param \Cms\Service\WebPageManagerInterface $webPageManager
     * @return void
     */
    public function __construct(
        OrderInfoMapperInterface $orderInfoMapper, 
        OrderProductMapperInterface $orderProductMapper, 
        BasketManagerInterface $basketManager,
        WebPageManagerInterface $webPageManager
    ){
        $this->orderInfoMapper = $orderInfoMapper;
        $this->orderProductMapper = $orderProductMapper;
        $this->basketManager = $basketManager;
        $this->webPageManager = $webPageManager;
    }

    /**
     * Filters the raw input
     * 
     * @param array|\ArrayAccess $input Raw input data
     * @param integer $page Current page number
     * @param integer $itemsPerPage Items per page to be displayed
     * @param string $sortingColumn Column name to be sorted
     * @param string $desc Whether to sort in DESC order
     * @return array
     */
    public function filter($input, $page, $itemsPerPage, $sortingColumn, $desc)
    {
        return $this->prepareResults($this->orderInfoMapper->filter($input, $page, $itemsPerPage, $sortingColumn, $desc));
    }

    /**
     * Counts the sum of sold products
     * 
     * @return float
     */
    public function getPriceSumCount()
    {
        return $this->orderProductMapper->getPriceSumCount();
    }

    /**
     * Counts total amount of sold products
     * 
     * @return integer
     */
    public function getQtySumCount()
    {
        return $this->orderProductMapper->getQtySumCount();
    }

    /**
     * Counts amount of unapproved orders
     * 
     * @return integer
     */
    public function countUnapproved()
    {
        return (int) $this->orderInfoMapper->countUnapproved();
    }

    /**
     * Counts all orders
     * 
     * @param boolean $approved Whether to count only approved orders
     * @return integer
     */
    public function countAll($approved)
    {
        return $this->orderInfoMapper->countAll($approved);
    }

    /**
     * Approves an order by its associated id
     * 
     * @param string $id Order's id
     * @return boolean
     */
    public function approveById($id)
    {
        $productIds = $this->orderProductMapper->findProductIdsByOrderId($id);

        foreach ($productIds as $productId) {
            $this->orderProductMapper->decrementProductInStockQtyById($productId);
        }

        return $this->orderInfoMapper->approveById($id);
    }

    /**
     * Update order statuses
     * 
     * @param array $relations
     * @return boolean
     */
    public function updateOrderStatuses(array $relations)
    {
        foreach ($relations as $orderId => $statusId) {
            $this->orderInfoMapper->updateOrderStatus((int) $orderId, (int) $statusId);
        }

        return true;
    }

    /**
     * Removes an order by its associated id
     * 
     * @param string $id Order's id
     * @return boolean
     */
    public function deleteById($id)
    {
        $this->orderInfoMapper->deleteById($id);
        $this->orderProductMapper->deleteAllByOrderId($id);

        return true;
    }

    /**
     * Remove a collection of orders by their associated id
     * 
     * @param array $ids
     * @return boolean
     */
    public function deleteByIds(array $ids)
    {
        foreach ($ids as $id) {
            if (!$this->deleteById($id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Fetches order entity by its associated ID
     * 
     * @param string $id Order id
     * @return \Krystal\Stdlib\VirtualEntity
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->orderInfoMapper->fetchById($id));
    }

    /**
     * Fetches all orders associated with customer ID
     * 
     * @param string $customerId
     * @return array
     */
    public function fetchAllByCustomerId($customerId)
    {
        return $this->prepareResults($this->orderInfoMapper->fetchAllByCustomerId($customerId));
    }

    /**
     * Creates the summary from products collection
     * 
     * @param array $products
     * @return array
     */
    public function createSummary(array $products)
    {
        // Initial values
        $totalPrice = 0;
        $totalQty = 0;

        foreach ($products as $product) {
            $totalPrice += $product['sub_total_price'];
            $totalQty += $product['qty'];
        }

        return array(
            'totalPrice' => $totalPrice,
            'totalQty' => $totalQty
        );
    }

    /**
     * Prepare attributes to readable view
     * 
     * @param string JSON string
     * @return array
     */
    private function prepareAttributes($attributes)
    {
        $attributes = json_decode($attributes);
        $output = array();

        foreach ($attributes as $groupId => $valueId) {
            $row = $this->orderProductMapper->fetchNames($groupId, $valueId);
            $output[$row['name']] = $row['value'];
        }

        return $output;
    }

    /**
     * Fetches all details by associated order ID
     * 
     * @param string $id Order's ID
     * @param string $customerId Optional filter by customer ID
     * @param string $coverDimensions Cover dimensions for image covers to be returned
     * @return array
     */
    public function fetchAllDetailsByOrderId($id, $customerId = null, $coverDimensions = '75x75')
    {
        $rows = $this->orderProductMapper->fetchAllDetailsByOrderId($id, $customerId);

        foreach ($rows as $index => $row) {
            $item =& $rows[$index];

            // Extra fields
            $item['cover'] = sprintf('%s%s/%s/%s', Module::PARAM_PRODUCTS_IMG_PATH, $row['product_id'], $coverDimensions, $row['cover']);
            $item['url'] = $this->webPageManager->surround($row['slug'], $row['lang_id']);
            $item['exists'] = !empty($item['slug']);

            $item['attributes'] = $this->prepareAttributes($item['attributes']);
        }

        return $rows;
    }

    /**
     * Returns prepared paginator's instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator()
    {
        return $this->orderInfoMapper->getPaginator();
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $order)
    {
        $entity = new VirtualEntity();
        $entity->setId($order['id'], VirtualEntity::FILTER_INT)
               ->setCustomerId($order['customer_id'], VirtualEntity::FILTER_INT)
               ->setStatusId($order['order_status_id'], VirtualEntity::FILTER_INT)
               ->setDate($order['date'], VirtualEntity::FILTER_TAGS)
               ->setName($order['name'], VirtualEntity::FILTER_HTML)
               ->setEmail($order['email'], VirtualEntity::FILTER_HTML)
               ->setPhone($order['phone'], VirtualEntity::FILTER_HTML)
               ->setAddress($order['address'], VirtualEntity::FILTER_HTML)
               ->setComment($order['comment'], VirtualEntity::FILTER_HTML)
               ->setDelivery($order['delivery'], VirtualEntity::FILTER_HTML)
               ->setQty($order['qty'], VirtualEntity::FILTER_INT)
               ->setTotalPrice($order['total_price'], VirtualEntity::FILTER_FLOAT)
               ->setApproved($order['approved'], VirtualEntity::FILTER_BOOL)

               // Discount price
               ->setDiscount($order['discount'], VirtualEntity::FILTER_FLOAT)

               // Order status fields
               ->setStatusName(isset($order['status_name']) ? $order['status_name'] : null, VirtualEntity::FILTER_HTML)
               ->setStatusDescription(isset($order['status_description']) ? $order['status_description'] : null, VirtualEntity::FILTER_HTML)
               ->setHasStatus(!empty($order['order_status_id']));

        return $entity;
    }

    /**
     * Makes an order
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function make(array $input)
    {
        $defaults = array(
            // By default all orders are un-approved
            'approved' => '0',
            'qty' => $this->basketManager->getTotalQuantity(),
            'total_price' => $this->basketManager->getTotalPrice()
        );

        $data = array_merge($input, $defaults);
        $data['date'] = date('Y-m-d', time());

        // First of all, insert, because we need to obtain a last id
        $this->orderInfoMapper->insert(ArrayUtils::arrayWithout($data, array('captcha')));

        // Now obtain last id
        $id = $this->orderInfoMapper->getLastId();

        $products = $this->basketManager->getProducts();

        if ($this->addProducts($id, $products)) {

            // Order is saved. Now clear the basket
            $this->basketManager->clear();
            $this->basketManager->save();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Tracks products
     * 
     * @param string $id Order id
     * @param array $products
     * @return boolean
     */
    private function addProducts($id, array $products)
    {
        foreach ($products as $product) {
            $data = array(
                'order_id' => $id,
                'product_id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'sub_total_price' => $product->getSubTotalPrice(),
                'qty' => $product->getQty(),
                'attributes' => json_encode($product->getAttributes())
            );

            $this->orderProductMapper->insert($data);
        }

        return true;
    }

    /**
     * Fetches latest order entities
     * 
     * @param integer $limit
     * @return array
     */
    public function fetchLatest($limit)
    {
        return $this->prepareResults($this->orderInfoMapper->fetchLatest($limit));
    }

    /**
     * Fetches all entities filtered by pagination
     * 
     * @param integer $page Current page number
     * @param integer $itemsPerPage Per page count
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage)
    {
        return $this->prepareResults($this->orderInfoMapper->fetchAllByPage($page, $itemsPerPage));
    }
}
