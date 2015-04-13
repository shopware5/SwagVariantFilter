<?php
namespace Shopware\SwagVariantFilter\Components\LegacyFilter;

/**
 * Class OptionHelper
 *
 * Provides methods on config options.
 *
 * @package Shopware\SwagVariantFilter\Components\LegacyFilter
 */
class OptionHelper
{

    /**
     * @var int
     */
    private $minStock;

    /**
     * @var array
     */
    private $enableCategoryIds = array();


    /**
     * @param \Enlight_Config $config
     */
    public function __construct(\Enlight_Config $config)
    {
        $this->minStock = $config->mininstock;

        $rawCategoryIds = $config->categoryids;
        if (!$rawCategoryIds) {
            return;
        }

        $this->enableCategoryIds = explode(',', $rawCategoryIds);
    }

    /**
     * @return bool
     */
    public function hasMinStock()
    {
        return $this->minStock > 0;
    }

    /**
     * @return int
     */
    public function getMinStock()
    {
        return $this->minStock;
    }

    /**
     * @return bool
     */
    public function hasEnabledCategories()
    {
        return count($this->enableCategoryIds) > 0;
    }

    /**
     * @return array
     */
    public function getEnabledCategoryIds()
    {
        return $this->enableCategoryIds;
    }

}