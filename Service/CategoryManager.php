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

use Cms\Service\AbstractManager;
use Cms\Service\HistoryManagerInterface;
use Cms\Service\WebPageManagerInterface;
use Menu\Contract\MenuAwareManager;
use Menu\Service\MenuWidgetInterface;
use Shop\Storage\CategoryMapperInterface;
use Shop\Storage\ProductMapperInterface;
use Krystal\Image\Tool\ImageManagerInterface;
use Krystal\Security\Filter;
use Krystal\Tree\AdjacencyList\TreeBuilder;
use Krystal\Tree\AdjacencyList\Render\AbstractRenderer;
use Krystal\Tree\AdjacencyList\Render\PhpArray;
use Krystal\Stdlib\ArrayUtils;

final class CategoryManager extends AbstractManager implements CategoryManagerInterface, MenuAwareManager
{
    /**
     * Any compliant category mapper
     * 
     * @var \Shop\Storage\CategoryMapperInterface
     */
    private $categoryMapper;

    /**
     * Any compliant product mapper
     * 
     * @var \Shop\Storage\ProductMapperInterface
     */
    private $productMapper;

    /**
     * Web page manager to manage slugs
     * 
     * @var \Cms\Service\WebPageManager
     */
    private $webPageManager;

    /**
     * Image manager for categories. It can upload, remove and build paths for images
     * 
     * @var \Krystal\Image\Tool\ImageManagerInterface
     */
    private $imageManager;

    /**
     * History manager to keep tracks
     * 
     * @var \Cms\Service\HistoryManagerInterface
     */
    private $historyManager;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\CategoryMapperInterface $categoryMapper
     * @param \Shop\Storage\ProductMapperInterface $productMapper
     * @param \Cms\Service\WebPageManager $webPageManager
     * @param \Krystal\Image\ImageManagerInterface $imageManager
     * @param \Cms\Service\HistoryManagerInterface $historyManager
     * @return void
     */
    public function __construct(
        CategoryMapperInterface $categoryMapper, 
        ProductMapperInterface $productMapper, 
        WebPageManagerInterface $webPageManager, 
        ImageManagerInterface $imageManager,
        HistoryManagerInterface $historyManager
    ){
        $this->categoryMapper = $categoryMapper;
        $this->productMapper = $productMapper;
        $this->webPageManager = $webPageManager;
        $this->imageManager = $imageManager;
        $this->historyManager = $historyManager;
    }

    /**
     * Returns a collection of switching URLs
     * 
     * @param string $id Category ID
     * @return array
     */
    public function getSwitchUrls($id)
    {
        return $this->productMapper->createSwitchUrls($id, 'Shop', 'Shop:Category@indexAction');
    }

    /**
     * Returns a tree with prompt placeholder
     * 
     * @param string $text
     * @return array
     */
    public function getPromtWithCategoriesTree($text)
    {
        $tree = $this->getCategoriesTree();
        ArrayUtils::assocPrepend($tree, 0, $text);

        return $tree;
    }

    /**
     * Creates Tree builder instance
     * 
     * @param \Krystal\Tree\AdjacencyList\Render\AbstractRenderer $walker
     * @return string
     */
    public function renderTree(AbstractRenderer $walker)
    {
        $treeBuilder = new TreeBuilder($this->createTreeData());
        return $treeBuilder->render($walker);
    }

    /**
     * Returns tree instance
     * 
     * @return \Krystal\Tree\AdjacencyList\Tree
     */
    public function getTree()
    {
        $rows = $this->categoryMapper->fetchAll();
        return new TreeBuilder($rows);
    }

    /**
     * Fetches all categories as a tree
     * 
     * @param boolean $extended Whether to return extended tree or not
     * @return array
     */
    public function getCategoriesTree($extended = false)
    {
        if ($extended == true) {
            $rows = $this->categoryMapper->fetchTree();
            $walker = new PhpArray('name', str_repeat('&nbsp;', 4));

            $treeBuilder = new TreeBuilder($rows);
            $treeBuilder->render($walker);

            $raw = $treeBuilder->render($walker);

            $output = array();

            foreach ($raw as $id => $name) {
                foreach ($rows as $index => $row) {
                    if ($row['id'] == $id) {
                        $rows[$index]['name'] = $raw[$row['id']];
                        $rows[$index]['url'] = $this->webPageManager->surround($row['slug'], $row['lang_id']);

                        $output[] = $rows[$index];
                    }
                }
            }
            
            return $output;

        } else {
            return $this->renderTree(new PhpArray('name', str_repeat('&nbsp;', 4)));
        }
    }

    /**
     * Fetches child rows by associated parent id
     * 
     * @param string $parentId
     * @param boolean $top Whether to return by ID or parent ID
     * @return array
     */
    public function fetchChildrenByParentId($parentId, $top = true)
    {
        return $this->prepareResults($this->categoryMapper->fetchChildrenByParentId($parentId, $top));
    }

    /**
     * {@inheritDoc}
     */
    public function fetchNameByWebPageId($webPageId)
    {
        return $this->categoryMapper->fetchNameByWebPageId($webPageId);
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
     * Returns category's breadcrumbs
     * 
     * @param \Shop\Service\CategoryEntity $category
     * @return array
     */
    public function getBreadcrumbs(CategoryEntity $category)
    {
        $bm = new BreadcrumbMaker($this->categoryMapper, $this->webPageManager);
        return $bm->getBreadcrumbsById($category->getId());
    }

    /**
     * Fetches all categories
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->categoryMapper->fetchAll();
    }

    /**
     * Counts all available categories
     * 
     * @return integer
     */
    public function countAll()
    {
        return $this->categoryMapper->countAll();
    }

    /**
     * Returns last category's id
     * 
     * @return integer
     */
    public function getLastId()
    {
        return $this->categoryMapper->getLastId();
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $category)
    {
        $imageBag = clone $this->imageManager->getImageBag();
        $imageBag->setId((int) $category['id'])
                 ->setCover($category['cover']);

        $entity = new CategoryEntity();
        $entity->setId($category['id'], CategoryEntity::FILTER_INT)
            ->setImageBag($imageBag)
            ->setParentId($category['parent_id'], CategoryEntity::FILTER_INT)
            ->setLangId($category['lang_id'], CategoryEntity::FILTER_INT)
            ->setWebPageId($category['web_page_id'], CategoryEntity::FILTER_INT)
            ->setDescription($category['description'], CategoryEntity::FILTER_SAFE_TAGS)
            ->setOrder($category['order'], CategoryEntity::FILTER_INT)
            ->setSeo($category['seo'], CategoryEntity::FILTER_BOOL)
            ->setSlug($this->webPageManager->fetchSlugByWebPageId($category['web_page_id']), CategoryEntity::FILTER_TAGS)
            ->setPermanentUrl('/module/shop/category/'.$entity->getId())
            ->setUrl($this->webPageManager->surround($entity->getSlug(), $entity->getLangId()))
            ->setCover($category['cover'], CategoryEntity::FILTER_TAGS)

            // Product count
            ->setProductCount(isset($category['product_count']) ? $category['product_count'] : null)

            // Meta data
            ->setTitle($category['title'], CategoryEntity::FILTER_HTML)
            ->setName($category['name'], CategoryEntity::FILTER_HTML)
            ->setKeywords($category['keywords'], CategoryEntity::FILTER_HTML)
            ->setMetaDescription($category['meta_description'], CategoryEntity::FILTER_HTML);

        return $entity;
    }

    /**
     * Updates a category
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function update(array $input)
    {
        $category =& $input['data']['category'];

        // Allow to remove a cover, only it case it exists and checkbox was checked
        if (isset($category['remove_cover']) && !empty($category['cover'])) {
            // Remove a cover, but not a dir itself
            $this->imageManager->delete($category['id']);
            $category['cover'] = '';

        } else {
            if (!empty($input['files']['file'])) {
                $file =& $input['files']['file'];

                // If we have a previous cover's image, then we need to remove it
                if (!empty($category['cover'])) {
                    if (!$this->imageManager->delete($category['id'], $category['cover'])){
                        // If failed, then exit this method immediately
                        return false;
                    }
                }

                // And now upload a new one
                $this->filterFileInput($file);
                $category['cover'] = $file[0]->getName();

                $this->imageManager->upload($category['id'], $file);
            }
        }

        $this->categoryMapper->update($input['data']);
        //$this->track('Category "%s" has been updated', $category['name']);
        return true;
    }

    /**
     * Adds a category
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function add(array $input)
    {
        $category =& $input['data']['category'];

        // Cover is always empty by default
        $category['cover'] = '';

        // If we have a cover, then we need to upload it
        if (!empty($input['files']['file'])) {
            $file =& $input['files']['file'];

            // Now filter original file's name
            $this->filterFileInput($file);

            // Override empty cover's value now
            $category['cover'] = $file[0]->getName();
        }

        if ($this->categoryMapper->insert($input['data'])) {
            $id = $this->getLastId();

            // If we have a cover, then we need to upload it
            if (!empty($input['files']['file'])) {
                $this->imageManager->upload($id, $input['files']['file']);
            }

            //$this->track('Added category "%s"', $category['name']);
            return true;
        }
    }

    /**
     * Removes a category by its associated id
     * 
     * @param string $id Category id
     * @return boolean
     */
    public function deleteById($id)
    {
        $this->removeCategoryById($id);
        $this->removeChildNodes($id);

        return true;
    }

    /**
     * Creates tree data to be supplied for the tree builder
     * 
     * @return array
     */
    private function createTreeData()
    {
        $result = array();
        $entities = $this->prepareResults($this->fetchAll());

        foreach ($entities as $entity) {
            $result[] = array(
                'name' => $entity->getName(),
                'id' => $entity->getId(),
                'parent_id' => $entity->getParentId(),
                'url' => $entity->getUrl()
            );
        }

        return $result;
    }

    /**
     * Removes a category by its associated id (Including images if present)
     * 
     * @param string $id
     * @return boolean
     */
    private function removeCategoryById($id)
    {
        $this->categoryMapper->deleteById($id);
        $this->imageManager->delete($id);

        return true;
    }

    /**
     * Removes all child nodes
     * 
     * @param string $parentId Parent category's id
     * @return boolean
     */
    private function removeChildNodes($parentId)
    {
        $treeBuilder = new TreeBuilder($this->categoryMapper->fetchAll());
        $ids = $treeBuilder->findChildNodeIds($parentId);

        // If there's at least one child id, then start working next
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $this->removeCategoryById($id);
            }
        }

        return true;
    }

    /**
     * Fetches category's entity by its associated id
     * 
     * @param string $id Category id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return \Shop\Service\CategoryEntity|boolean
     */
    public function fetchById($id, $withTranslations)
    {
        if ($withTranslations == true) {
            return $this->prepareResults($this->categoryMapper->fetchById($id, true));
        } else {
            return $this->prepareResult($this->categoryMapper->fetchById($id, false));
        }
    }
}
