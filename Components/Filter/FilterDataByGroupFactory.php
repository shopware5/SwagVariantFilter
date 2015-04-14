<?php

namespace Shopware\SwagVariantFilter\Components\Common;

use \Shopware\SwagVariantFilter\Components\Common\FilterDataFactory as AbstractFactory;

class FilterDataByGroupFactory extends AbstractFactory
{
    /**
     * @todo move!
     * @param $categoryIds
     * @return mixed
     */
    public function fromCategoryIds($categoryIds)
    {
        if (!is_array($categoryIds)) {
            $categoryIds = $this->dbAdapter->getSubcategories($categoryIds);
        }

        return $this->dbAdapter->getConfigurationOptionsFromCategoryIds($categoryIds);
    }


    public function getRawData($values)
    {
        if (!is_array($values)) {
            throw new \InvalidArgumentException('Invalid param $values');
        }

        return $this->dbAdapter->getConfigurationOptionsFromGroupIds($values);
    }
}