<?php

namespace Shopware\SwagVariantFilter\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\SwagVariantFilter\Components\LegacyFilterService;
use Shopware\SwagVariantFilter\Components\LegacyResponseExtender;

/**
 * Class Legacy
 *
 * WARNING: If you are using Shopware 5+, this is not active!
 *
 * Created for Backwards compatibility with Shopware 4.
 *
 * @package Shopware\SwagVariantFilter\Subscriber
 */
class Legacy implements SubscriberInterface
{

    /**
     * {inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Listing' => 'onDetailListingPostDispatch',
            'Shopware_Modules_Articles_sGetArticlesByCategory_FilterSql' => 'onGetArticlesByCategoryFilterSql',
            'Shopware_Modules_Articles_sGetArticlesByCategory_FilterResult' => 'afterGetArticlesByCategory',
        );
    }

    /**
     * Main Entry handler, determines whether to display a filter
     *
     * 1. Invoke filter
     * 1.1 determine if active
     * 2. Build filter conditions
     * 3. Handle Request
     * 4. Assign view variables
     *
     * @see LegacyFilter for details
     * @param \Enlight_Event_EventArgs $args
     */
    public function onDetailListingPostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var \Shopware_Controllers_Frontend_Listing $controller */
        $controller = $args->getSubject();
        $request = $controller->Request();

        /** @var LegacyFilterService $legacyFilter */
        $legacyFilter = Shopware()->Container()->get('SwagVariantLegacyFilter')->setUp($request->sCategory);
        if (!$legacyFilter->isActive()) {
            return;
        }

        $filterConditions = $legacyFilter->getFilterConditions();

        if (!$filterConditions) {
            return;
        }

        $controller->View()->assign(array(
            'swagVariantFilterConditions' => $filterConditions,
        ));
        $controller->View()->addTemplateDir(dirname(__FILE__) . '/../views/');
        $controller->View()->extendsTemplate('frontend/plugins/SwagVariantFilter/right.tpl');
    }

    /**
     * Filter articles based on selected variant filter
     *
     * @param \Enlight_Event_EventArgs $arguments
     * @return array
     */
    public function afterGetArticlesByCategory(\Enlight_Event_EventArgs $arguments)
    {
        /** @var LegacyFilterService $legacyFilter */
        $legacyFilter = Shopware()->Container()->get('SwagVariantLegacyFilter')
            ->setUp(Shopware()->Front()->Request()->sCategory);

        if (!$legacyFilter->hasActiveOptions()) {
            return;
        }

        /** @var LegacyResponseExtender $legacyResponseExtender */
        $legacyResponseExtender = Shopware()->Container()->get('SwagVariantLegacyResponseExtender');

        return $legacyResponseExtender
            ->fromFilterGroups($legacyFilter->getFilterConditions())
            ->extendViewData($arguments->getReturn());
    }

    /**
     * Modifies the onGetArticlesByCategoryFilterSql to filter Articles based on the selected options
     *
     * 1. determine if is active
     * 2. extend base query
     *
     * @param \Enlight_Event_EventArgs $args
     * @return string
     */
    public function onGetArticlesByCategoryFilterSql(\Enlight_Event_EventArgs $args)
    {
        /** @var LegacyFilterService $legacyFilter */
        $legacyFilter = Shopware()->Container()->get('SwagVariantLegacyFilter')->setUp(Shopware()->Front()->Request()->sCategory);

        $baseQuery = $args->getReturn();

        if (!$legacyFilter->hasActiveOptions()) {
            return $baseQuery;
        }

        $filterItems = $legacyFilter->getFilterConditions();

        /** @var LegacyResponseExtender $legacyResponseExtender */
        $legacyResponseExtender = Shopware()->Container()->get('SwagVariantLegacyResponseExtender');

        return $legacyResponseExtender->fromFilterGroups($filterItems)
            ->extendQuery($baseQuery);
    }
}