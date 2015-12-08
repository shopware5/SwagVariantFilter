<?php
/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Shopware\SwagVariantFilter\Components\LegacyFilter;

use Shopware\SwagVariantFilter\Components\Common\FilterOptionAbstract;
use Shopware\SwagVariantFilter\Components\LegacyFilter\RequestHelper;

/**
 * Class FilterOption
 * @package Shopoware\SwagVariantFilter\Components\LegacyFilter
 */
class FilterOption extends FilterOptionAbstract
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
