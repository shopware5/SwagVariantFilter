<?php
namespace Shopware\SwagVariantFilter\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\SwagVariantFilter\Bundle\SearchBundle\Condition\MinStockCondition;
use Shopware\SwagVariantFilter\Bundle\SearchBundle\ProductVariantCriteriaRequestHandler;
use Shopware\SwagVariantFilter\Bundle\SearchBundleDbal\ConditionHandler\MinStockConditionHandler;
use Shopware\SwagVariantFilter\Bundle\SearchBundleDbal\ConditionHandler\ProductVariantConditionHandler;
use Shopware\SwagVariantFilter\Bundle\SearchBundleDbal\FacetHandler\ProductVariantFacetHandler;
use Shopware\SwagVariantFilter\Components\AccessibilityService;
use Shopware\SwagVariantFilter\Components\Common\ConfigAdapter;
use Shopware\SwagVariantFilter\Components\Common\RequestAdapter;

/**
 * Class Filter
 *
 * Filter SetUp for search bundle
 *
 * * VariantFilter Classes
 * * MinStock filter classes
 *
 * @package Shopware\SwagVariantFilter\Subscriber
 */
class Filter implements SubscriberInterface
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch_Frontend_Listing' => 'inspectRequest',
            'Enlight_Controller_Action_PreDispatch_Widgets_Listing' => 'inspectRequest',
            'Shopware_SearchBundle_Collect_Criteria_Request_Handlers' => 'getVariantRequestHandler',
            'Shopware_SearchBundle_Create_Listing_Criteria' => [
                ['addMinStockCondition'],
            ],
            'Shopware_SearchBundleDBAL_Collect_Condition_Handlers' => [
                ['getVariantConditionHandler'],
                ['getMinStockConditionHandler'],
            ],
            'Shopware_SearchBundleDBAL_Collect_Facet_Handlers' => 'getVariantFilterFacetHandler'
        ];
    }

    public function inspectRequest(\Enlight_Event_EventArgs $args)
    {
        /** @var \Enlight_Controller_Request_Request $request */
        $request = $args->getSubject()->Request();

        $this->getAccessibilityService()->match($request->sCategory);
    }

    /**
     *
     * # Test if condition is accessible
     * # Test if request contains filter-data
     * # set minstock from config (-Adapter)
     *
     *
     * @param \Enlight_Event_EventArgs $args
     */
    public function addMinStockCondition(\Enlight_Event_EventArgs $args)
    {
        if (!$this->getAccessibilityService()->isValid()) {
            return;
        }

        if (!$this->getRequestAdapter()->hasVariantIds()) {
            return;
        }

        $args->getCriteria()->addCondition(new MinStockCondition($this->getConfigAdapter()->getMinStock()));
    }

    public function getVariantRequestHandler()
    {
        if (!$this->getAccessibilityService()->isValid()) {
            return;
        }

        return new ProductVariantCriteriaRequestHandler();
    }

    public function getVariantFilterFacetHandler()
    {
        return new ProductVariantFacetHandler(
            Shopware()->Container()->get('SwagVariantFilterProductVariantService'),
            Shopware()->Container()->get('shopware_searchdbal.dbal_query_builder_factory'),
            Shopware()->Snippets()->getNamespace('frontend/swag_variant_filter/main')
        );
    }

    public function getVariantConditionHandler()
    {
        return new ProductVariantConditionHandler();
    }

    public function getMinStockConditionHandler()
    {
        return new MinStockConditionHandler();
    }

    /**
     * @return AccessibilityService
     * @throws \Exception
     */
    private function getAccessibilityService()
    {
        return Shopware()->Container()->get('SwagVariantFilterAccessibilityService');
    }

    /**
     * @return RequestAdapter
     * @throws \Exception
     */
    private function getRequestAdapter()
    {
        return Shopware()->Container()->get('SwagVariantFilterRequestAdapter');
    }

    /**
     * @return ConfigAdapter
     * @throws \Exception
     */
    private function getConfigAdapter()
    {
        return Shopware()->Container()->get('SwagVariantFilterConfigAdapter');
    }
}