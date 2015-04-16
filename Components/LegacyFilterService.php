<?php
namespace Shopware\SwagVariantFilter\Components;

use Shopware\SwagVariantFilter\Components\Common\AccessibilityInterface;
use Shopware\SwagVariantFilter\Components\Common\ConfigAdapter;
use Shopware\SwagVariantFilter\Components\Common\ConfiguratorTranslate;
use Shopware\SwagVariantFilter\Components\Common\DatabaseAdapter;
use Shopware\SwagVariantFilter\Components\Common\FilterDataFactory;
use Shopware\SwagVariantFilter\Components\Common\FilterGroupAbstract;
use Shopware\SwagVariantFilter\Components\Common\ServiceAbstract;
use Shopware\SwagVariantFilter\Components\LegacyFilter\FilterDataByCategoryFactory;
use Shopware\SwagVariantFilter\Components\LegacyFilter\FilterGroup;
use Shopware\SwagVariantFilter\Components\LegacyFilter\RequestHelper;

/**
 * Class LegacyFilter
 *
 * WARNING: If you are using Shopware 5+, this is not active!
 *
 * Created for Backwards compatibility with Shopware 4.
 *
 * This class provides the complete Legacy interface, formerly part of Bootstrapping.
 *
 * @package Shopware\SwagVariantFilter\Components
 */
class LegacyFilterService extends ServiceAbstract implements AccessibilityInterface
{

    /**
     * @var DatabaseAdapter
     */
    private $databaseAdapter;

    /**
     * @var RequestHelper
     */
    private $requestHelper;

    /**
     * @var OptionHelper
     */
    private $optionHelper;

    /**
     * @var ConfiguratorTranslate
     */
    private $translate;

    /**
     * @var DatabaseAdapter
     */
    private $dbAdapter;

    /**
     * @var bool
     */
    private $filterAccessible = false;

    /**
     * Generates the filter
     *
     * @param RequestHelper $requestHelper
     * @param ConfigAdapter $optionHelper
     */
    public function __construct(RequestHelper $requestHelper, ConfigAdapter $optionHelper, DatabaseAdapter $dbAdapter, ConfiguratorTranslate $translate)
    {
        $this->requestHelper = $requestHelper;
        $this->optionHelper = $optionHelper;
        $this->translate = $translate;
        $this->dbAdapter = $dbAdapter;
    }

    /**
     * Match configuration with current request
     *
     * @throws \InvalidArgumentException
     * @param $categoryId
     * @return mixed
     */
    public function match($categoryId)
    {
        $this->filterAccessible = false;

        if (!$this->optionHelper->hasEnabledCategories()) {
            $this->filterAccessible = true;
        }

        if (in_array($categoryId, $this->optionHelper->getEnabledCategoryIds())) {
            $this->filterAccessible = true;
        }

        return $this;
    }


    /**
     * @return FilterDataFactory
     */
    protected function getDataFactory()
    {
        return new FilterDataByCategoryFactory($this->dbAdapter);
    }

    /**
     * {@inheritdoc}
     */
    protected function createFilterGroup($id, $label)
    {
        return new FilterGroup($this->requestHelper, $id, $label);
    }

    /**
     * @throws \BadFunctionCallException
     * @return bool
     */
    public function isValid()
    {
        return $this->filterAccessible;
    }

    /**
     * @return ConfiguratorTranslate
     */
    protected function getTranslate()
    {
        return $this->translate;
    }
}