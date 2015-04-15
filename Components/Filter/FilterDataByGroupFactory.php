<?php

namespace Shopware\SwagVariantFilter\Components\Common;

use \Shopware\SwagVariantFilter\Components\Common\FilterDataFactory as AbstractFactory;

class FilterDataByGroupFactory extends AbstractFactory
{
    public function getRawData($values)
    {
        if (!is_array($values)) {
            throw new \InvalidArgumentException('Invalid param $values');
        }

        return $this->dbAdapter->getConfigurationOptionsFromGroupIds($values);
    }
}