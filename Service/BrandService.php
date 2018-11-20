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
use Shop\Storage\BrandMapperInterface;
use Cms\Service\AbstractManager;

final class BrandService extends AbstractManager
{
    /**
     * Any compliant brand mapper
     * 
     * @var \Shop\Storage\BrandMapperInterface
     */
    private $brandMapper;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\BrandMapperInterface $brandMapper
     * @return void
     */
    public function __construct(BrandMapperInterface $brandMapper)
    {
        $this->brandMapper = $brandMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'])
               ->setName($row['name'])
               ->setOrder($row['order']);

        return $entity;
    }

    /**
     * Deletes by Id
     * 
     * @param int $id Brand Id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->brandMapper->deleteByPk($id);
    }

    /**
     * Saves a brand
     * 
     * @param array $input
     * @return boolean
     */
    public function save(array $input)
    {
        return $this->brandMapper->persist($input);
    }

    /**
     * Returns last ID
     * 
     * @return integer
     */
    public function getLastId()
    {
        return $this->brandMapper->getMaxId();
    }

    /**
     * Fetches brand by its ID
     * 
     * @param int $id Brand ID
     * @return array
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->brandMapper->findByPk($id));
    }

    /**
     * Fetch all brands
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->prepareResults($this->brandMapper->fetchAll());
    }
}
