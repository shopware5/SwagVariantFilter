<?php

namespace Shopware\SwagVariantFilter\Components\Filter;

use \Shopware\SwagVariantFilter\Components\Common\FilterDataFactory as AbstractFactory;

class FilterDataByOptionFactory extends AbstractFactory
{
    public function getRawData($values)
    {
        if (!is_array($values)) {
            throw new \InvalidArgumentException('Invalid param $values');
        }

        return $this->dbAdapter->getConfigurationOptionsFromOptionIds($values);
    }
}