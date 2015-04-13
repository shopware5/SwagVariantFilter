<?php

namespace Shopware\SwagVariantFilter\Components\LegacyFilter;

/**
 * Class FilterItem
 *
 * Representation of a single filter group, containing many options
 *
 * @package Shopware\SwagVariantFilter\LegacyFilter
 */
class FilterGroup
{

    /**
     * @var string
     */
    private $label;

    /**
     * @var int
     */
    private $id;

    /**
     * @var array
     */
    private $options = array();

    private $hasActiveOption = false;

    /**
     * @var RequestHelper
     */
    private $requestHelper;

    /**
     * @param RequestHelper $requestHelper
     * @param int $id
     * @param string $label
     */
    public function __construct(RequestHelper $requestHelper, $id, $label)
    {
        $this->requestHelper = $requestHelper;
        $this->id = (int)$id;
        $this->label = (string)$label;
    }

    /**
     * @param $id
     * @param $value
     * @param bool $isValid
     * @return $this
     */
    public function addOption($id, $value, $isValid = false)
    {
        $this->options[$id] = new FilterOption($this->requestHelper, $id, $value, $isValid);

        if ($isValid) {
            $this->hasActiveOption = true;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function hasActiveOptions()
    {
        return $this->hasActiveOption;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->requestHelper->getBaseUrl();
    }


}