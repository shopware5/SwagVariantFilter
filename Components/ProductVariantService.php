<?php

namespace Shopware\SwagVariantFilter\Components;


use Shopware\SwagVariantFilter\Components\Common\ConfiguratorTranslate;
use Shopware\SwagVariantFilter\Components\Common\FilterGroupAbstract;
use Shopware\SwagVariantFilter\Components\Common\ServiceAbstract;
use Shopware\SwagVariantFilter\Components\Filter\FilterGroup;
use Shopware\SwagVariantFilter\Components\Filter\FilterDataByOptionFactory;

/**
 * Class ProductVariantService
 * @package Shopware\SwagVariantFilter\Components
 */
class ProductVariantService extends ServiceAbstract
{
    /**
     * @var FilterDataByOptionFactory
     */
    private $factory;

    /**
     * @var ConfiguratorTranslate
     */
    private $translate;

    /**
     * @param FilterDataByOptionFactory $factory
     * @param ConfiguratorTranslate $translate
     */
    public function __construct(FilterDataByOptionFactory $factory, ConfiguratorTranslate $translate)
    {
        $this->factory = $factory;
        $this->translate = $translate;
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
     * @return FilterDataByOptionFactory
     */
    protected function getDataFactory()
    {
        return $this->factory;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTranslate() {
        return $this->translate;
    }

}