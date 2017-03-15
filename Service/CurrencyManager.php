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
use Shop\Storage\CurrencyMapperInterface;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Stdlib\ArrayUtils;

final class CurrencyManager extends AbstractManager implements CurrencyManagerInterface
{
    /**
     * Any compliant currency mapper
     * 
     * @var \Shop\Storage\CurrencyMapperInterface
     */
    private $currencyMapper;

    /**
     * State initialization
     * 
     * @param \Shop\Storage\CurrencyMapperInterface $currencyMapper
     * @return void
     */
    public function __construct(CurrencyMapperInterface $currencyMapper)
    {
        $this->currencyMapper = $currencyMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $row)
    {
        $entity = new VirtualEntity();
        $entity->setId($row['id'], VirtualEntity::FILTER_INT)
               ->setCode($row['code'], VirtualEntity::FILTER_TAGS)
               ->setValue($row['value'], VirtualEntity::FILTER_FLOAT);

        return $entity;
    }

    /**
     * Returns last currency ID
     * 
     * @return string
     */
    public function getLastId()
    {
        return $this->currencyMapper->getLastId();
    }

    /**
     * Deletes a currency by its associated ID
     * 
     * @param string $id Currency ID
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->currencyMapper->deleteById($id);
    }

    /**
     * Adds a currency
     * 
     * @param array $input
     * @return boolean
     */
    public function add(array $input)
    {
        return $this->currencyMapper->persist($input);
    }

    /**
     * Updates a currency
     * 
     * @param array $input
     * @return boolean
     */
    public function update(array $input)
    {
        return $this->currencyMapper->persist($input);
    }

    /**
     * Fetch currency entity by its associated ID
     * 
     * @param string $id
     * @return array
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->currencyMapper->fetchById($id));
    }

    /**
     * Fetch currencies as a list
     * 
     * @return array
     */
    public function fetchList()
    {
        return ArrayUtils::arrayList($this->currencyMapper->fetchAll(), 'code', 'value');
    }

    /**
     * Fetch all currency entities
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->prepareResults($this->currencyMapper->fetchAll());
    }
}
