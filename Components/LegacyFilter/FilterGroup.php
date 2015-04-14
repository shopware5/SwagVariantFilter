<?php
namespace Shopware\SwagVariantFilter\Components\LegacyFilter;

use Shopoware\SwagVariantFilter\Components\LegacyFilter\FilterOption;
use \Shopware\SwagVariantFilter\Components\Common\FilterGroupAbstract;
use Shopware\SwagVariantFilter\Components\Common\RequestHelper;

/**
 * Class FilterGroup
 * @package Shopware\SwagVariantFilter\Components\LegacyFilter
 */
class FilterGroup extends FilterGroupAbstract
{

    /**
     * @var RequestHelper
     */
    private $requestHelper;

    public function __construct(RequestHelper $requestHelper, $id, $label)
    {
        parent::__construct($id, $label);
        $this->requestHelper = $requestHelper;
    }

    /**
     * {@inheritdoc}
     */
    protected function createOption($id, $label, $isActive)
    {
        return new FilterOption($this->requestHelper, $id, $label, $isActive);
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->requestHelper->getBaseUrl();
    }
}