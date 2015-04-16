<?php

namespace Shopware\SwagVariantFilter\Components\Common;

/**
 * Class UrlGenerator
 *
 * Binding Request + url handling
 *
 * @package Shopware\SwagVariantFilter\Components\LegacyFilter
 */
class RequestAdapter
{
    /**
     * The name used as a request variable
     */
    const PARAM_NAME = 'swag_variant';


    /**
     * @var array
     */
    private $requestedVariantIds = array();

    /**
     * @param \Enlight_Controller_Request_Request $request
     */
    public function __construct(\Enlight_Controller_Request_Request $request)
    {
        $optionsRaw = $request->getParam(self::PARAM_NAME);
        if (!$optionsRaw) {
            return;
        }

        $this->requestedVariantIds = explode('|', $optionsRaw);
    }

    public function hasVariantIds()
    {
        return count($this->requestedVariantIds) > 0;
    }

    /**
     * @return array
     */
    public function getRequestedVariantIds()
    {
        return $this->requestedVariantIds;
    }

    public function getParamName()
    {
        return self::PARAM_NAME;
    }


}