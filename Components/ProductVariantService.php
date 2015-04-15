<?php

namespace Shopware\SwagVariantFilter\Components;


use Shopware\SwagVariantFilter\Components\Common\FilterGroupAbstract;
use Shopware\SwagVariantFilter\Components\Common\ServiceAbstract;
use Shopware\SwagVariantFilter\Components\Filter\FilterGroup;
use Shopware\SwagVariantFilter\Components\Common\FilterDataByGroupFactory;

/**
 * Class ProductVariantService
 * @package Shopware\SwagVariantFilter\Components
 */
class ProductVariantService extends ServiceAbstract
{
    /**
     * @var FilterDataByGroupFactory
     */
    private $factory;

    public function __construct(FilterDataByGroupFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param $id
     * @param $label
     * @return FilterGroupAbstract
     */
    protected function createFilterGroup($id, $label)
    {
        return new FilterGroup($id, $label);
    }

    /**
     * @return FilterDataByGroupFactory
     */
    protected function getDataFactory()
    {
        return $this->factory;
    }

}