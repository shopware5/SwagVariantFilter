<?php
namespace Shopware\SwagVariantFilter\Components\LegacyFilter;

/**
 * Class FilterOption
 *
 * A
 *
 * @package Shopware\SwagVariantFilter\Components\LegacyFilter
 */
class FilterOption
{

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var RequestHelper
     */
    protected $requestHelper;

    /**
     * @param RequestHelper $requestHelper
     * @param $id
     * @param $label
     * @param bool $active
     */
    public function __construct(RequestHelper $requestHelper, $id, $label, $active = false)
    {
        $this->requestHelper = $requestHelper;
        $this->label = $label;
        $this->id = $id;
        $this->active = $active;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getAddUrl()
    {
        return $this->requestHelper->getAddUrl($this->id);
    }

    /**
     * @return string
     */
    public function getRemoveUrl()
    {
        return $this->requestHelper->getRemoveUrl($this->id);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}