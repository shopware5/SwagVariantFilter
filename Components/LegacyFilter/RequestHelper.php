<?php
/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Shopware\SwagVariantFilter\Components\LegacyFilter;

use Shopware\SwagVariantFilter\Components\Common\RequestAdapter;

/**
 * Class RequestHelper
 *
 * Extended legacy request helper, wich is used as a request inspector and url generator
 *
 * @package Shopware\SwagVariantFilter\Components\LegacyFilter
 */
class RequestHelper extends RequestAdapter
{
    /**
     * @var string
     */
    protected $baseUrl;

    public function __construct(\Enlight_Controller_Request_Request $request)
    {
        parent::__construct($request);
        $this->baseUrl = $request->getBaseUrl() . $request->getPathInfo();
    }

    /**
     * @param $optionId
     * @return string
     */
    public function getAddUrl($optionId)
    {
        return $this->getUrlPRefix() . $this->formatOptions(
            array_merge(
                $this->getRequestedVariantIds(),
                array($optionId)
            )
        );
    }

    /**
     * @param $optionId
     * @return string
     */
    public function getRemoveUrl($optionId)
    {
        $activeOptions = $this->getRequestedVariantIds();

        if (false !== ($index = array_search($optionId, $activeOptions))) {
            unset($activeOptions[$index]);
        }

        return $this->getUrlPRefix() . $this->formatOptions($activeOptions);
    }

    /**
     * @return string
     */
    private function getUrlPRefix()
    {
        return $this->getBaseUrl() . '&' . self::PARAM_NAME . '=';
    }

    public function getBaseUrl()
    {
        return $this->baseUrl . '?p=1';
    }

    /**
     * @param array $options
     * @return string
     */
    private function formatOptions(array $options)
    {
        return implode('|', $options);
    }

    /**
     * @param int $default
     * @return int
     */
    public function getPerPage($default = 12)
    {
        return (int) Shopware()->Front()->Request()->getParam('sPerPage', $default);
    }

    /**
     * @param string $default
     * @return string
     */
    public function getRawActiveOptionIds($default = '')
    {
        return (string) Shopware()->Front()->Request()->getParam(self::PARAM_NAME, $default);
    }
}
