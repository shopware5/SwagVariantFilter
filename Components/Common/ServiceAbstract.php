<?php
/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Shopware\SwagVariantFilter\Components\Common;

/**
 * Class ServiceAbstract
 *
 * Collection of common methods for legacy and next FilterService.
 *
 * @package Shopware\SwagVariantFilter\Components
 */
abstract class ServiceAbstract
{
    /**
     * @var array
     */
    private $conditions;

    /**
     * @return FilterDataFactory
     */
    abstract protected function getDataFactory();

    /**
     * @param $values
     * @param array $activeOptionIds
     * @return Shopware\SwagVariantFilter\Components\Common\FilterGroup[]
     */
    public function getFilterConditions($values, array $activeOptionIds = array())
    {
        if (!$this->conditions) {
            $rawConditionData = $this->getDataFactory()->getRawData($values);
            $this->conditions = $this->hydrateConditionData($rawConditionData, $activeOptionIds);
        }

        return $this->conditions;
    }

    /**
     * @param array $rawConditionData
     * @param array $activeOptionIds
     * @return Shopware\SwagVariantFilter\Components\Common\FilterGroup[]
     */
    private function hydrateConditionData(array $rawConditionData, array $activeOptionIds)
    {
        $ret = array();

        foreach ($rawConditionData as $data) {
            $groupName = $data['group_name'];
            $isActive = false;

            if (!isset($ret[$groupName])) {
                $ret[$groupName] = $this->createFilterGroup(
                    $data['group_id'],
                    $this->getTranslate()->getGroupLabel($data['group_id'], $groupName)
                );
            }

            if (in_array($data['option_id'], $activeOptionIds)) {
                $isActive = true;
            }

            $ret[$groupName]->addOption(
                $data['option_id'],
                $this->getTranslate()->getOptionLabel($data['option_id'], $data['option_name']),
                $isActive
            );
        }

        return $ret;
    }

    /**
     * @param $label
     * @return FilterGroupAbstract
     */
    abstract protected function createFilterGroup($id, $label);

    /**
     * @return ConfiguratorTranslate
     */
    abstract protected function getTranslate();
}
