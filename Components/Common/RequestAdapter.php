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
 * Class RequestAdapter
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
     * @var bool
     */
    private $isGrouped = false;

    /**
     * @param \Enlight_Controller_Request_Request $request
     */
    public function __construct(\Enlight_Controller_Request_Request $request)
    {
        $params = $request->getParams();
        $ids = array();

        foreach ($params as $paramName => $paramValue) {
            if (strpos($paramName, self::PARAM_NAME) !== 0) {
                continue;
            }

            $parts = explode('_', $paramName);

            if (count($parts) !== 3) {
                $ids = explode('|', $paramValue);
                break;
            }

            $ids[$parts[2]] = explode('|', $paramValue);
        }

        if (!$ids) {
            return;
        }

        if (!$request->has(self::PARAM_NAME)) {
            $this->isGrouped = true;
        }

        $this->requestedVariantIds = $ids;
    }

    /**
     * @return bool
     */
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

    /**
     * @return string
     */
    public function getParamName()
    {
        return self::PARAM_NAME;
    }

    /**
     * @return bool
     */
    public function isMultiDimensional()
    {
        return $this->isGrouped;
    }
}
