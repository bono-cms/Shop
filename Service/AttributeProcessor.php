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

use Krystal\Security\Filter;

/**
 * The purpose of this tool is to convert a raw result-set
 * that gets returned from SQL query in CategoryMapper::findAttributesById()
 * into a nice-looking set that will be easy to read and iterate over in templates
 */
final class AttributeProcessor
{
    /**
     * A collection of rows
     * 
     * @var array
     */
    private $rows = array();

    const ARRAY_KEY_GROUP_ID = 'group_id';
    const ARRAY_KEY_GROUP_NAME = 'group_name';
    const ARRAY_KEY_ATTRIBUTES = 'attributes';
    const ARRAY_KEY_VALUE_ID = 'value_id';
    const ARRAY_KEY_VALUE_NAME = 'value_name';

    /**
     * State initialization
     * 
     * @param array $rows
     * @return void
     */
    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    /**
     * Creates attribute group
     * 
     * @return array
     */
    public function process()
    {
        $output = array();

        foreach ($this->rows as $row) {
            foreach ($output as $inner) {
                if ($inner[self::ARRAY_KEY_GROUP_ID] == $row[self::ARRAY_KEY_GROUP_ID]) {
                    continue 2;
                }
            }

            $output[] = array(
                self::ARRAY_KEY_GROUP_ID => (int) $row[self::ARRAY_KEY_GROUP_ID],
                self::ARRAY_KEY_GROUP_NAME => Filter::escape($row[self::ARRAY_KEY_GROUP_NAME]),
                self::ARRAY_KEY_ATTRIBUTES => $this->findAttrsByGroupId($row[self::ARRAY_KEY_GROUP_ID])
            );
        }

        return $output;
    }

    /**
     * Create attribute values by associated group ID
     * 
     * @param string $groupId
     * @return array
     */
    private function findAttrsByGroupId($groupId)
    {
        $output = array();

        foreach ($this->rows as $row) {
            if ($row[self::ARRAY_KEY_GROUP_ID] == $groupId) {
                $output[(int) $row[self::ARRAY_KEY_VALUE_ID]] = Filter::escape($row[self::ARRAY_KEY_VALUE_NAME]);
            }
        }

        return $output;
    }
}
