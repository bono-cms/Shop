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
use Krystal\Stdlib\ArrayUtils;
use Krystal\Image\Tool\ImageManagerInterface;
use Krystal\Security\Filter;
use Krystal\Db\Filter\FilterableServiceInterface;
use Menu\Contract\MenuAwareManager;
use Shop\Storage\ProductMapperInterface;
use Shop\Storage\ImageMapperInterface;
use Shop\Storage\CategoryMapperInterface;
use Cms\Service\AbstractManager;
use Cms\Service\HistoryManagerInterface;
use Cms\Service\WebPageManagerInterface;

final class ProductManager extends AbstractManager implements ProductManagerInterface, FilterableServiceInterface, MenuAwareManager
{
    /**
     * Any compliant product mapper
     * 
     * @var \Shop\Storage\ProductMapperInterface
     */
    private $productMapper;

    /**
     * Any compliant product image mapper
     * 
     * @var \Shop\Storage\ImageMapperInterface
     */
    private $imageMapper;

    /**
     * Any compliant category mapper
     * 
     * @var \Shop\Storage\CategoryMapperInterface
     */
    private $categoryMapper;

    /**
     * Product image manager. it can upload, build paths and remove images
     * 
     * @var \Krystal\Image\Tool\ImageManagerInterface
     */
    private $imageManager;

    /**
     * Web page manager for managing slugs
     * 
     * @var \Cms\Service\WebPageManagerInterface
     */
    private $webPageManager;

    /**
     * History manager to keep tracks
     * 
     * @var \Cms\Service\HistoryManagerInterface
     */
    private $historyManager;

    /**
     * Internal service to remove products by their associated ids
     * 
     * @var \Shop\Service\ProductRemoverInterface
     */
    private $productRemover;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\ProductMapperInterface $productMapper
     * @param \Shop\Storage\ImageMapperInterface $imageMapper
     * @param \Shop\Storage\CategoryMapperInterface $categoryMapper
     * @param \Cms\Service\WebPageManagerInterface $webPageManager
     * @param \Krystal\Image\Tool\ImageManagerInterface $imageManager
     * @param \Cms\Service\HistoryManagerInterface $historyManager
     * @return void
     */
    public function __construct(
        ProductMapperInterface $productMapper, 
        ImageMapperInterface $imageMapper, 
        CategoryMapperInterface $categoryMapper,
        WebPageManagerInterface $webPageManager,
        ImageManagerInterface $imageManager,
        HistoryManagerInterface $historyManager,
        ProductRemoverInterface $productRemover
    ){
        $this->productMapper = $productMapper;
        $this->imageMapper = $imageMapper;
        $this->categoryMapper = $categoryMapper;
        $this->webPageManager = $webPageManager;
        $this->imageManager = $imageManager;
        $this->historyManager = $historyManager;
        $this->productRemover = $productRemover;
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
        return $this->prepareResults($this->productMapper->filter($input, $page, $itemsPerPage, $sortingColumn, $desc));
    }

    /**
     * Tracks activity
     * 
     * @param string $message
     * @param string $placeholder
     * @return boolean
     */
    private function track($message, $placeholder)
    {
        return $this->historyManager->write('Shop', $message, $placeholder);
    }

    /**
     * Fetches best sales
     * 
     * @param integer $qty Minimal quantity for a product to be considered as a best sale
     * @param integer $limit
     * @return array
     */
    public function fetchBestSales($qty, $limit)
    {
        $entities = array();
        $ids = $this->productMapper->fetchBestSales($qty, $limit);

        foreach ($ids as $id) {
            $entities[] = $this->fetchById($id);
        }

        return $entities;
    }

    /**
     * Fetches all published products that have stoke price
     * 
     * @param integer $page Current page
     * @param integer $itemsPerPage Per page count
     * @return array
     */
    public function fetchAllPublishedStokesByPage($page, $itemsPerPage)
    {
        return $this->prepareResults($this->productMapper->fetchAllPublishedStokesByPage($page, $itemsPerPage));
    }

    /**
     * {@inheritDoc}
     */
    public function fetchNameByWebPageId($webPageId)
    {
        return $this->productMapper->fetchNameByWebPageId($webPageId);
    }

    /**
     * Returns product's breadcrumbs collection
     * 
     * @param \Shop\Service\ProductEntity $product
     * @return array
     */
    public function getBreadcrumbs(ProductEntity $product)
    {
        $bm = new BreadcrumbMaker($this->categoryMapper, $this->webPageManager);

        return $bm->getWithCategoryId($product->getCategoryId(), array(
            array(
                'name' => $product->getName(),
                'link' => '#'
            )
        ));
    }

    /**
     * Create category pair (id => name)
     * 
     * @param array $categories
     * @return array
     */
    private function createCategoryPair(array $categories)
    {
        $result = array();

        foreach ($categories as $category) {
            $result[(int) $category['id']] = Filter::escape($category['name']);
        }

        return $result;
    }

    /**
     * Create category ids
     * 
     * @param array $categories
     * @return array
     */
    private function createCategoryIds(array $categories)
    {
        $ids = array();

        foreach ($categories as $category) {
            array_push($ids, (int) $category['id']);
        }

        return $ids;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $product)
    {
        // If the $id isn't set, then the product isn't valid
        if (!isset($product['id'])) {
            return new ProductEntity();
        }

        $imageBag = clone $this->imageManager->getImageBag();
        $imageBag->setId($product['id'])
                 ->setCover($product['cover']);

        $entity = new ProductEntity();
        $entity->setImageBag($imageBag)
            ->setId($product['id'], ProductEntity::FILTER_INT)
            ->setLangId($product['lang_id'], ProductEntity::FILTER_INT)
            ->setWebPageId($product['web_page_id'], ProductEntity::FILTER_INT)
            ->setTitle($product['title'], ProductEntity::FILTER_TAGS)
            ->setName($product['name'], ProductEntity::FILTER_TAGS)
            ->setPrice($product['regular_price'], ProductEntity::FILTER_FLOAT)
            ->setStokePrice($product['stoke_price'], ProductEntity::FILTER_FLOAT)
            ->setInStock($product['in_stock'], ProductEntity::FILTER_INT)
            ->setSpecialOffer($product['special_offer'], ProductEntity::FILTER_BOOL)
            ->setDescription($product['description'], ProductEntity::FILTER_SAFE_TAGS)
            ->setPublished($product['published'], ProductEntity::FILTER_BOOL)
            ->setOrder($product['order'], ProductEntity::FILTER_INT)
            ->setSeo($product['seo'], ProductEntity::FILTER_BOOL)
            ->setSlug($this->webPageManager->fetchSlugByWebPageId($product['web_page_id']))
            ->setKeywords($product['keywords'], ProductEntity::FILTER_TAGS)
            ->setMetaDescription($product['meta_description'], ProductEntity::FILTER_TAGS)
            ->setCover($product['cover'], ProductEntity::FILTER_TAGS)
            ->setDate($product['date'])
            ->setPermanentUrl('/module/shop/product/'.$entity->getId())
            ->setUrl($this->webPageManager->surround($entity->getSlug(), $entity->getLangId()))
            ->setViewCount($product['views'], ProductEntity::FILTER_INT);

        // It's only set in valid entities
        if (isset($product['categories'])) {
            // Categories
            $entity->setCategoryIds($this->createCategoryIds($product['categories']))
                   ->setCategories($this->createCategoryPair($product['categories']));
        }

        return $entity;
    }

    /**
     * Prepares product's photos
     * 
     * @param array $images
     * @return array|boolean
     */
    private function preparePhotos($id, $images)
    {
        if (!empty($images)) {
            $entities = array();

            foreach ($images as $image) {
                $imageBag = clone $this->imageManager->getImageBag();
                $imageBag->setId($id)
                         ->setCover($image['image']);

                $entity = new VirtualEntity();
                $entity->setImageBag($imageBag)
                    ->setId($image['id'])
                    ->setProductId($image['product_id'])
                    ->setImage($image['image'])
                    ->setOrder((int) $image['order'])
                    ->setPublished((bool) $image['published']);

                array_push($entities, $entity);
            }

            return $entities;
        } else {
            return false;
        }
    }

    /**
     * Fetches all product's photo entities by its associated id
     * 
     * @param string $id Product id
     * @return array
     */
    public function fetchAllImagesById($id)
    {
        $images = $this->imageMapper->fetchAllByProductId($id);
        return $this->preparePhotos($id, $images);
    }

    /**
     * Fetches all published product's photo entities by its associated id
     * 
     * @param string $id Product id
     * @return array
     */
    public function fetchAllPublishedImagesById($id)
    {
        $images = $this->imageMapper->fetchAllByProductId($id);
        return $this->preparePhotos($id, $images);
    }

    /**
     * Increments view count by product's id
     * 
     * @param string $id Product id
     * @return boolean
     */
    public function incrementViewCount($id)
    {
        return $this->productMapper->incrementViewCount($id);
    }

    /**
     * Updates prices by their associated ids and values
     * 
     * @param array $pair
     * @return boolean
     */
    public function updatePrices(array $pair)
    {
        foreach ($pair as $id => $price) {
            if (!$this->productMapper->updatePriceById($id, $price)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Updates published state by their associated ids
     * 
     * @param array $pair
     * @return boolean
     */
    public function updatePublished(array $pair)
    {
        foreach ($pair as $id => $published) {
            if (!$this->productMapper->updatePublishedById($id, $published)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Update SEO state by their associated ids
     * 
     * @param array $pair
     * @return boolean
     */
    public function updateSeo(array $pair)
    {
        foreach ($pair as $id => $seo) {
            if (!$this->productMapper->updateSeoById($id, $seo)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Removes all product's associated data by its associated id
     * 
     * @param string $id Product id
     * @return boolean
     */
    private function removeAllById($id)
    {
        return $this->productRemover->removeAllById($id);
    }

    /**
     * Removes products by their associated ids
     * 
     * @param array $ids
     * @return boolean
     */
    public function deleteByIds(array $ids)
    {
        foreach ($ids as $id) {
            if (!$this->removeAllById($id)) {
                return false;
            }
        }

        $this->track('Batch removal of %s products', count($ids));
        return true;
    }

    /**
     * Deletes a product by its associated id
     * 
     * @param string $id Product's id
     * @return boolean
     */
    public function deleteById($id)
    {
        $name = Filter::escape($this->productMapper->fetchNameById($id));

        if ($this->removeAllById($id)) {
            $this->track('Product "%s" has been removed', $name);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns prepared paginator's instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator()
    {
        return $this->productMapper->getPaginator();
    }

    /**
     * Returns last product's id
     * 
     * @return integer
     */
    public function getLastId()
    {
        return $this->productMapper->getLastId();
    }

    /**
     * Prepares raw input data before sending to the mapper
     * 
     * @param array $input Raw input data
     * @return array
     */
    private function prepareInput(array $input)
    {
        // Request's data
        $product =& $input['data']['product'];
        $files =& $input['files'];

        if (empty($product['slug'])) {
            $product['slug'] = $product['name'];
        }

        // If a cover has been selected, then we need to override its base name right now
        if (!empty($files['file'])) {
            $this->filterFileInput($files['file']);
            $product['cover'] = $files['file'][0]->getName();
        }

        // Empty title is taken from the name
        if (empty($product['title'])) {
            $product['title'] = $product['name'];
        }

        // Make it now look like a slug
        $product['slug'] = $this->webPageManager->sluggify($product['slug']);

        return $input;
    }

    /**
     * Adds a product
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function add(array $input)
    {
        $input = $this->prepareInput($input);

        // Short-cuts
        $product =& $input['data']['product'];

        // Initial view count
        $product['views'] = 0;
        // For cross-database compatibility, the date must be generated here, not in the mapper
        $product['date'] = date('Y-m-d', time());

        $files =& $input['files']['file'];

        // Insert should be first, because we need to provide an id
        $this->productMapper->insert(ArrayUtils::arrayWithout($product, array('slug')));

        // After insert, now we can get an id
        $id = $this->getLastId();

        // Do we have images?
        if (!empty($files)) {
            // So let's first try to upload them
            if ($this->imageManager->upload($id, $files)) {
                // And write their base names into storage
                foreach ($files as $file) {
                    $this->imageMapper->insert($id, $file->getName(), 0, true);
                }

            } else {
                trigger_error('Failed to upload product additional image', E_USER_NOTICE);
            }
        }

        $this->track('Product "%s" has been added', $product['name']);

        // Add a web page now
        return $this->webPageManager->add($id, $product['slug'], 'Shop (Products)', 'Shop:Product@indexAction', $this->productMapper);
    }

    /**
     * Updates a product
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function update(array $input)
    {
        $input = $this->prepareInput($input);

        // Product data
        $product =& $input['data']['product'];

        // Current product id we're dealing with
        $productId = $product['id'];

        // An array of new appended images from a user
        $appendedImages = $input['files']['file'];

        if (!empty($input['files'])) {
            // Array of changed images, representing an id => FileBag instance
            $changedImages = $this->getChangedImages($input['files']);

            // Do we have any changed image?
            if (!empty($changedImages)) {
                foreach ($changedImages as $imageId => $fileBag) {
                    // First of all we need to remove old image
                    if ($this->imageManager->delete($productId, $this->imageMapper->fetchFileNameById($imageId))) {

                        $this->filterFileInput($fileBag);
                        $this->imageManager->upload($productId, $fileBag);
                        $this->imageMapper->updateFileNameById($imageId, $fileBag[0]->getName());
                    }
                }
                // PHP hasn't block scope, so we have to remove it manually
                unset($fileBag);
            }
        }

        // New user appended images
        if (!empty($appendedImages)) {
            // Upload all appended files firstly
            if ($this->imageManager->upload($productId, $appendedImages)) {
                // Then save them
                foreach ($appendedImages as $fileBag) {
                    $this->imageMapper->insert($productId, $fileBag->getName(), 1, 1);
                }
            }
        }

        $photos =& $input['data']['photos'];

        // Do we have images to delete?
        if (isset($photos['toDelete'])) {
            // Grab photo ids we're gonna remove
            $ids = array_keys($photos['toDelete']);

            foreach ($ids as $imageId) {
                // Try to remove on file-system first
                if ($this->imageManager->delete($productId, $this->imageMapper->fetchFileNameById($imageId))){
                    // If successful, then remove from a storage as well
                    $this->imageMapper->deleteById($imageId);
                }
            }
        }

        if (isset($photos['published'])) {
            // Update photos published state
            foreach ($photos['published'] as $id => $published) {
                $this->imageMapper->updatePublishedById($id, $published);
            }
        }

        if (isset($photos['order'])) {
            // Update photos order
            foreach ($photos['order'] as $id => $order) {
                $this->imageMapper->updateOrderById($id, $order);
            }
        }

        // Update a cover now
        if (isset($photos['cover'])) {
            $product['cover'] = $photos['cover'];
        }

        $this->track('Product "%s" has been updated', $product['name']);
        $this->webPageManager->update($product['web_page_id'], $product['slug']);

        return $this->productMapper->update(ArrayUtils::arrayWithout($product, array('slug')));
    }

    /**
     * Returns an array of changed images in edit form
     * 
     * @param array $files
     * @return array
     */
    private function getChangedImages(array $files)
    {
        $result = array();

        foreach ($files as $dataType => $value) {
            if (!empty($value) && strpos($dataType, 'image_') !== false) {
                // Grab a changed image id
                $id = str_replace('image_', '', $dataType);
                $result[$id] = $value;
            }
        }

        return $result;
    }

    /**
     * Fetches all published product entities with maximal view counts
     * 
     * @param integer $limit Fetching limit
     * @param string $categoryId Optionally can be filtered by category id
     * @return array
     */
    public function fetchAllPublishedWithMaxViewCount($limit, $categoryId = null)
    {
        return $this->prepareResults($this->productMapper->fetchAllPublishedWithMaxViewCount($limit, $categoryId));
    }

    /**
     * Returns minimal product's price associated with provided category id
     * It's aware only of published products
     * 
     * @param string $categoryId
     * @return float
     */
    public function getMinCategoryPriceCount($categoryId)
    {
        return (float) $this->productMapper->getMinCategoryPriceCount($categoryId);
    }

    /**
     * Fetches all published product entities associated with given category id
     * 
     * @param string $categoryId
     * @param integer $page Current page
     * @param integer $itemsPerPage Per page count
     * @param string $sort Sorting constant
     * @return array
     */
    public function fetchAllPublishedByCategoryIdAndPage($categoryId, $page, $itemsPerPage, $sort)
    {
        return $this->prepareResults($this->productMapper->fetchAllPublishedByCategoryIdAndPage($categoryId, $page, $itemsPerPage, $sort));
    }

    /**
     * Fetches all product entities filtered by pagination
     * 
     * @param integer $page
     * @param integer $itemsPerPage Per page count
     * @param string $categoryId Optional category id filter
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage, $categoryId)
    {
        return $this->prepareResults($this->productMapper->fetchAllByPage($page, $itemsPerPage, $categoryId));
    }

    /**
     * Fetches all published product entities associated with given category id
     * 
     * @param string $categoryId
     * @return array
     */
    public function fetchAllPublishedByCategoryId($categoryId)
    {
        return $this->prepareResults($this->productMapper->fetchAllPublishedByCategoryId($categoryId));
    }

    /**
     * Fetches product's entity by its associated id
     * 
     * @param string $id
     * @return \Krystal\Stdlib\VirtualEntity|boolean
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->productMapper->fetchById($id));
    }

    /**
     * Fetches basic product info by its associated id
     * 
     * @param string $id Product id
     * @return \Shop\Service\ProductEntity|boolean
     */
    public function fetchBasicById($id)
    {
        $product = $this->productMapper->fetchBasicById($id);

        // If not empty, then valid $id supplied
        if (!empty($product)) {
            $imageBag = clone $this->imageManager->getImageBag();
            $imageBag->setId($id)
                     ->setCover($product['cover']);

            $entity = new ProductEntity();
            $entity->setId($id)
                   ->setImageBag($imageBag)
                   ->setCover($product['cover'], ProductEntity::FILTER_TAGS)
                   ->setName($product['name'], ProductEntity::FILTER_TAGS)
                   ->setRegularPrice($product['regular_price'], ProductEntity::FILTER_FLOAT)
                   ->setStokePrice($product['stoke_price'], ProductEntity::FILTER_FLOAT);

            return $entity;
        } else {
            return false;
        }
    }

    /**
     * Counts all available products
     * 
     * @return integer
     */
    public function countAll()
    {
        return $this->productMapper->countAll();
    }

    /**
     * Fetches latest product entities
     * 
     * @param integer $limit Limit for fetching
     * @param integer $categoryId Optionally can be filtered by category id
     * @return array
     */
    public function fetchLatestPublished($limit, $categoryId = null)
    {
        return $this->prepareResults($this->productMapper->fetchLatestPublished($limit, $categoryId));
    }
}
