<?php
/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Shopware\SwagVariantFilter\Components\LegacyFilter;

use Shopware\SwagVariantFilter\Components\Common\FilterGroupAbstract;

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

    /**
     * FilterGroup constructor.
     *
     * @param RequestHelper $requestHelper
     * @param string $id
     * @param $label
     */
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
