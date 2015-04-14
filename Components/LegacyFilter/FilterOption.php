<?php
namespace Shopoware\SwagVariantFilter\Components\LegacyFilter;

use \Shopware\SwagVariantFilter\Components\Common\FilterOptionAbstract as AbstractFilterOption;
use Shopware\SwagVariantFilter\Components\Common\RequestHelper;

/**
 * Class FilterOption
 * @package Shopoware\SwagVariantFilter\Components\LegacyFilter
 */
class FilterOption extends AbstractFilterOption
{

    /**
     * @var RequestHelper
     */
    private $requestHelper;

    /**
     * @param RequestHelper $requestHelper
     * @param string $id
     * @param bool $label
     * @param bool $active
     */
    public function __construct(RequestHelper $requestHelper, $id, $label, $active = false)
    {
        parent::__construct($id, $label, $active);
        $this->requestHelper = $requestHelper;
    }

    /**
     * @return string
     */
    public function getAddUrl()
    {
        return $this->requestHelper->getAddUrl($this->getId());
    }

    /**
     * @return string
     */
    public function getRemoveUrl()
    {
        return $this->requestHelper->getRemoveUrl($this->getId());
    }
}