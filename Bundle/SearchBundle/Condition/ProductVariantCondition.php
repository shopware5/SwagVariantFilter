<?php

namespace Shopware\SwagVariantFilter\Bundle\SearchBundle\Condition;


use Shopware\Bundle\SearchBundle\ConditionInterface;

/**
 * Class ProductVariantCondition
 *
 * Add VariantConditions to SearchBundle
 *
 * @package SwagVariantFilter\Bundle\SearchBundle\Condition
 */
class ProductVariantCondition implements ConditionInterface
{

    /**
     * @var array
     */
    private $productVariantIds;

    /**
     * @param array $productVariantIds
     */
    public function __construct(array $productVariantIds = array())
    {
        $this->productVariantIds = $productVariantIds;
    }

    /**
     * @return array
     */
    public function getProductVariantIds()
    {
        return $this->productVariantIds;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'swag-variant-filter-product-variant';
    }
}