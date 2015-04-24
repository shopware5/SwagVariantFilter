<?php

namespace Shopware\SwagVariantFilter\Bundle\SearchBundle;


use Enlight_Controller_Request_RequestHttp as Request;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\CriteriaRequestHandlerInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use Shopware\SwagVariantFilter\Bundle\SearchBundle\Condition\ProductVariantCondition;
use Shopware\SwagVariantFilter\Bundle\SearchBundle\Facet\ProductVariantFacet;
use Shopware\SwagVariantFilter\Components\Common\RequestAdapter;

/**
 * Class ProductVariantCriteriaRequestHandler
 * @package Shopware\SwagVariantFilter\Bundle\SearchBundle
 */
class ProductVariantCriteriaRequestHandler implements CriteriaRequestHandlerInterface
{

    /**
     * @param Request $request
     * @param Criteria $criteria
     * @param ShopContextInterface $context
     */
    public function handleRequest(
        Request $request,
        Criteria $criteria,
        ShopContextInterface $context
    )
    {
        $criteria->addFacet(
            new ProductVariantFacet()
        );

        $selectedOptions = $this->getRequestedGroups($request);

        if (!$selectedOptions) {
            return;
        }

        $criteria->addCondition(
            new ProductVariantCondition(
                $selectedOptions
            )
        );
    }

    /**
     * Create a result array from multiple requested names
     *
     * @param Request $request
     * @return array
     */
    private function getRequestedGroups(Request $request)
    {
        $params = $request->getParams();
        $selectedOptions = [];

        foreach($params as $key => $value) {
            if(strpos($key, RequestAdapter::PARAM_NAME) !== 0) {
                continue;
            }

            $parts = explode('_', $key);

            if(count($parts) !== 3) {
                continue;
            }

            $selectedOptions[$parts[2]] = explode('|', $value);
        }

        return $selectedOptions;
    }
}