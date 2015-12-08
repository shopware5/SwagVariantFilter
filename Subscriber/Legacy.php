<?php
/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Shopware\SwagVariantFilter\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\SwagVariantFilter\Components\LegacyFilter\RequestHelper;
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
     * @var RequestHelper
     */
    private $requestHelper;

    /**
     * @param RequestHelper $requestHelper
     */
    public function __construct(RequestHelper $requestHelper)
    {
        $this->requestHelper = $requestHelper;
    }

    /**
     * {@inheritdoc}
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

        $categoryId = $request->sCategory;

        /** @var LegacyFilterService $legacyFilter */
        $legacyFilter = Shopware()->Container()->get('SwagVariantLegacyFilter');
        $legacyFilter->match($categoryId);
        if (!$legacyFilter->isValid()) {
            return;
        }

        $filterConditions = $legacyFilter->getFilterConditions($request->sCategory, $this->requestHelper->getRequestedVariantIds());

        if (!$filterConditions) {
            return;
        }

        $controller->View()->assign(array(
            'swagVariantFilterConditions' => $filterConditions,
        ));
        $controller->View()->addTemplateDir(__DIR__ . '/../Views/');
        $controller->View()->extendsTemplate('frontend/plugins/swag_variant_filter/right.tpl');
    }

    /**
     * Filter articles based on selected variant filter
     *
     * @param \Enlight_Event_EventArgs $arguments
     * @return array|null
     */
    public function afterGetArticlesByCategory(\Enlight_Event_EventArgs $arguments)
    {
        /** @var LegacyFilterService $legacyFilter */
        $legacyFilter = Shopware()->Container()->get('SwagVariantLegacyFilter');

        if (!$this->requestHelper->hasVariantIds()) {
            return;
        }

        /** @var LegacyResponseExtender $legacyResponseExtender */
        $legacyResponseExtender = Shopware()->Container()->get('SwagVariantLegacyResponseExtender');

        return $legacyResponseExtender
            ->fromFilterGroups(
                $legacyFilter->getFilterConditions($this->requestHelper->getRequestedVariantIds())
            )
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
        /** @var int $categoryId */
        $categoryId = Shopware()->Front()->Request()->sCategory;

        $baseQuery = $args->getReturn();

        if (!$this->requestHelper->hasVariantIds()) {
            return $baseQuery;
        }

        $filterItems = Shopware()->Container()
            ->get('SwagVariantLegacyFilter')
            ->getFilterConditions(
                $categoryId,
                $this->requestHelper->getRequestedVariantIds()
            );

        /** @var LegacyResponseExtender $legacyResponseExtender */
        $legacyResponseExtender = Shopware()->Container()->get('SwagVariantLegacyResponseExtender');

        return $legacyResponseExtender
            ->fromFilterGroups($filterItems)
            ->extendQuery($baseQuery);
    }
}
