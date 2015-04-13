<?php
/**
 * Created by PhpStorm.
 * User: jpietrzyk
 * Date: 10.04.15
 * Time: 12:05
 */

namespace Shopware\SwagVariantFilter\Components\LegacyFilter;

/**
 * Class UrlGenerator
 *
 * Binding Request + url handling
 *
 * @package Shopware\SwagVariantFilter\Components\LegacyFilter
 */
class RequestHelper
{

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var array
     */
    private $activeOptions = array();

    /**
     * @param \Enlight_Controller_Request_Request $request
     */
    public function __construct(\Enlight_Controller_Request_Request $request)
    {
        $this->baseUrl = $request->getBaseUrl() . $request->getPathInfo();

        $activeOptions = $request->getParam('oid');
        if (!$activeOptions) {
            return;
        }

        $this->activeOptions = explode('|', $activeOptions);
    }

    /**
     * @return array
     */
    public function getActiveOptions()
    {
        return $this->activeOptions;
    }

    /**
     * @param $optionId
     * @return string
     */
    public function getAddUrl($optionId)
    {
        return $this->getUrlPRefix() . $this->formatOptions(
            array_merge(
                $this->activeOptions,
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
        $activeOptions = $this->activeOptions;

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
        return $this->getBaseUrl() . '&oid=';
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
        return (int)Shopware()->Front()->Request()->getParam('sPerPage', $default);
    }

    /**
     * @param string $default
     * @return string
     */
    public function getRawActiveOptionIds($default = '')
    {
        return (string)Shopware()->Front()->Request()->getParam('oid', $default);
    }
}