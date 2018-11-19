<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Collection;

use Krystal\Stdlib\ArrayCollection;

final class SpecificationItemTypeCollection extends ArrayCollection
{
    const TYPE_TEXT = 1;
    const TYPE_DESCRIPTION = 2;
    const TYPE_NUMBER = 3;

    /**
     * {@inheritDoc}
     */
    protected $collection = array(
        self::TYPE_TEXT => 'Text',
        self::TYPE_DESCRIPTION => 'Description',
        self::TYPE_NUMBER => 'Number'
    );
    
    /**
     * Guess method by constant
     * 
     * @param int $const
     * @return string
     */
    public static function guessMethodByConst($const)
    {
        $map = array(
            self::TYPE_TEXT => 'text',
            self::TYPE_DESCRIPTION => 'textarea',
            self::TYPE_NUMBER => 'number'
        );

        return $map[$const];
    }
}
