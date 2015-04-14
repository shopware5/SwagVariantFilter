<?php
namespace Shopware\SwagVariantFilter\Components;

use Shopware\SwagVariantFilter\Components\Common\DatabaseAdapter;
use Shopware\SwagVariantFilter\Components\Common\FilterGroupAbstract;
use Shopware\SwagVariantFilter\Components\Common\OptionHelper;
use Shopware\SwagVariantFilter\Components\Common\RequestHelper;

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
class LegacyFilterService
{

    /**
     * @var array
     */
    private $conditions;

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
     * @var int
     */
    private $requestedCategoryIds;

    /**
     * Expects list of active category id's
     *
     * @param RequestHelper $requestHelper
     * @param OptionHelper $optionHelper
     */
    public function __construct(RequestHelper $requestHelper, OptionHelper $optionHelper)
    {
        $this->requestHelper = $requestHelper;
        $this->optionHelper = $optionHelper;
    }

    /**
     * @todo have a better idea, maybe split objects?
     * @param $requestedCategoryId
     * @return $this
     */
    public function setUp($requestedCategoryIds)
    {
        if (!$requestedCategoryIds) {
            throw new \InvalidArgumentException('Missing required param "$requestedCategoryId"');
        }

        $this->requestedCategoryIds = $requestedCategoryIds;

        return $this;
    }

    /**
     * @return DatabaseAdapter
     */
    protected function getDatabaseAdpater()
    {
        if (!$this->databaseAdapter) {
            $this->databaseAdapter = new DatabaseAdapter();
        }

        return $this->databaseAdapter;
    }


    /**
     * Determine if filter should be processed and diplayed for current page
     *
     * Defaults to true if nothing is set
     * @return bool
     */
    public function isActive()
    {
        if (!$this->optionHelper->hasEnabledCategories()) {
            return true;
        }

        foreach ($this->optionHelper->getEnabledCategoryIds() as $categoryId) {
            if ($categoryId == $this->requestedCategoryId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Shopware\SwagVariantFilter\Components\LegacyFilter\FilterItem[]
     */
    public function getFilterConditions()
    {
        if (!$this->conditions) {
            $categories = $this->getDatabaseAdpater()->getSubcategories($this->requestedCategoryId);
            $rawConditionData = $this->getDatabaseAdpater()->getConfigurationOptions($categories);
            $this->conditions = $this->hydrateConditionData($rawConditionData);
        }

        return $this->conditions;
    }

    /**
     * @param $rawConditionData
     * @return hopware\SwagVariantFilter\Components\LegacyFilter\FilterItem[]
     */
    private function hydrateConditionData($rawConditionData)
    {
        $ret = array();

        foreach ($rawConditionData as $data) {
            $groupName = $data['group_name'];
            $isActive = false;

            if (!isset($ret[$groupName])) {
                $ret[$groupName] = $this->createCondition($data['group_id'], $groupName);
            }

            if (in_array($data['option_id'], $this->requestHelper->getActiveOptions())) {
                $isActive = true;
            }

            $ret[$groupName]->addOption($data['option_id'], $data['option_name'], $isActive);
        }

        return $ret;
    }

    /**
     * @param $label
     * @return FilterGroupAbstract
     */
    private function createCondition($id, $label)
    {
        return new FilterGroupAbstract($this->requestHelper, $id, $label);
    }

    /**
     * @return bool
     */
    public function hasActiveOptions()
    {
        return count($this->requestHelper->getActiveOptions()) > 0;
    }


}