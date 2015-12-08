<?php
/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

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

    private $requestAdapater;

    /**
     * @param RequestAdapter $requestAdapter
     */
    public function __construct(
        RequestAdapter $requestAdapter
    ) {
        $this->requestAdapater = $requestAdapter;
    }

    /**
     * @param Request $request
     * @param Criteria $criteria
     * @param ShopContextInterface $context
     */
    public function handleRequest(
        Request $request,
        Criteria $criteria,
        ShopContextInterface $context
    ) {
        $criteria->addFacet(
            new ProductVariantFacet()
        );

        if (!$this->requestAdapater->hasVariantIds()) {
            return;
        }

        if (!$this->requestAdapater->isMultiDimensional()) {
            return;
        }

        $selectedOptions = $this->requestAdapater->getRequestedVariantIds();

        if (!$selectedOptions) {
            return;
        }

        $criteria->addCondition(
            new ProductVariantCondition(
                $selectedOptions
            )
        );
    }
}
