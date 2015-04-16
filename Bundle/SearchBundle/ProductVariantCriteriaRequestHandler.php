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

        $selectedOptionsRaw = $request->getParam(RequestAdapter::PARAM_NAME, false);

        if (!$selectedOptionsRaw) {
            return;
        }

        $criteria->addCondition(
            new ProductVariantCondition(
                explode('|', $selectedOptionsRaw)
            )
        );
    }
}