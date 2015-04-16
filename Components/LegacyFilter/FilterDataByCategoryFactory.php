<?php


namespace Shopware\SwagVariantFilter\Components\LegacyFilter;


use Shopware\SwagVariantFilter\Components\Common\FilterDataFactory;

/**
 * Class FilterDataByCategoryFactory
 *
 * FilterStrategy, generate data by core category-ids
 *
 * @package Shopware\SwagVariantFilter\Components\LegacyFilter
 */
class FilterDataByCategoryFactory extends FilterDataFactory
{

    public function getRawData($values)
    {
        if (!is_array($values)) {
            $values = $this->dbAdapter->getSubcategories($values);
        }

        return $this->dbAdapter->getConfigurationOptionsFromCategoryIds($values);
    }
}