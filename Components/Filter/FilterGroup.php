<?php

namespace Shopware\SwagVariantFilter\Components\Common;


use Shopware\SwagVariantFilter\Components\Common\FilterGroupAbstract;
use Shopware\SwagVariantFilter\Components\Common\FilterOption;

class FilterGroup extends FilterGroupAbstract
{

    /**
     * @param int $id
     * @param string $label
     * @param bool $isActive
     * @return FilterOptionAbstract
     */
    protected function createOption($id, $label, $isActive)
    {
        return new FilterOption($id, $label, $isActive);
    }
}