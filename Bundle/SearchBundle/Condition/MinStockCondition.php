<?php
/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Shopware\SwagVariantFilter\Bundle\SearchBundle\Condition;

use Shopware\Bundle\SearchBundle\ConditionInterface;

/**
 * Class MinStockCondition
 *
 * Optional MinStock condition for variants
 *
 * @package Shopware\SwagVariantFilter\Bundle\SearchBundle\Condition
 */
class MinStockCondition implements ConditionInterface
{

    /**
     * @var int
     */
    private $minStock;

    /**
     * @param $minStock
     */
    public function __construct($minStock)
    {
        $this->minStock = (int) $minStock;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'swag-variant-filter-min-stock';
    }

    /**
     * @return int
     */
    public function getMinStock()
    {
        return $this->minStock;
    }

    public function hasMinStock()
    {
        return (bool) $this->minStock;
    }
}
