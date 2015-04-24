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
     * Format result to be flat
     */
    const FORMAT_FLAT = 'flat';

    /**
     * default result
     */
    const FORMAT_GROUPED = 'grouped';


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
     * @param string $format
     * @return array
     */
    public function getProductVariantIds($format = 'grouped')
    {
        switch($format) {
            case self::FORMAT_GROUPED:
                return $this->productVariantIds;
            case self::FORMAT_FLAT:
                $ret = [];
                foreach($this->productVariantIds as $variantIds) {
                    foreach($variantIds as $variantId) {
                        $ret[] = $variantId;
                    }
                }

                return $ret;
        }

        throw new \InvalidArgumentException('Invalid param $format');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'swag-variant-filter-product-variant';
    }
}