<?php

namespace Shopware\SwagVariantFilter\Subscriber;


use Enlight\Event\SubscriberInterface;
use Shopware\SwagVariantFilter\Components\AccessibilityService;
use Shopware\SwagVariantFilter\Components\Common\ConfigAdapter;
use Shopware\SwagVariantFilter\Components\Common\ConfiguratorTranslate;
use Shopware\SwagVariantFilter\Components\Common\RequestAdapter;
use Shopware\SwagVariantFilter\Components\Common\DatabaseAdapter;
use Shopware\SwagVariantFilter\Components\ProductVariantService;
use Shopware\SwagVariantFilter\Components\Filter\FilterDataByGroupFactory;

/**
 * Class ServiceContainer
 *
 * Creates all services
 *
 * @package Shopware\SwagVariantFilter\Subscriber
 */
class ServiceContainer implements SubscriberInterface
{
    /**
     * @var \Enlight_Config
     */
    private $config;

    /**
     * @var \Enlight_Controller_Request_Request
     */
    private $request;

    /**
     * @var \Shopware\Components\DependencyInjection\Container
     */
    private $container;

    /**
     * @param \Enlight_Config $config
     * @param \Enlight_Controller_Request_Request $request
     */
    public function __construct(\Enlight_Config $config, \Enlight_Controller_Request_Request $request)
    {
        $this->config = $config;
        $this->request = $request;
        $this->container = Shopware()->Container();

    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Bootstrap_InitResource_SwagVariantFilterProductVariantService' => 'getFilterService',
            'Enlight_Bootstrap_InitResource_SwagVariantFilterAccessibilityService' => 'getAccessibilityService',
            'Enlight_Bootstrap_InitResource_SwagVariantFilterRequestAdapter' => 'getRequestAdapter',
            'Enlight_Bootstrap_InitResource_SwagVariantFilterConfigAdapter' => 'getConfigAdapter',
        ];
    }

    /**
     * @return AccessibilityService
     */
    public function getAccessibilityService()
    {
        return new AccessibilityService(
            $this->container->get('SwagVariantFilterConfigAdapter')
        );
    }

    /**
     * @return ProductVariantService
     */
    public function getFilterService()
    {
        $dbAdapter = $this->createDbAdapater();

        return new ProductVariantService(
            $this->createFilterConditionFactory(
                $dbAdapter
            ),
            $this->createTranlate(
                $dbAdapter,
                Shopware()->Shop()->getLocale()->getId(),
                Shopware()->Shop()->getFallback()->getLocale()->getId()
            )
        );
    }

    public function getConfigAdapter()
    {
        return new ConfigAdapter($this->config);
    }

    public function getRequestAdapter()
    {
        return new RequestAdapter($this->request);
    }

    /**
     * @param DatabaseAdapter $adapter
     * @return FilterDataByGroupFactory
     */
    private function createFilterConditionFactory(DatabaseAdapter $adapter)
    {
        return new FilterDataByGroupFactory($adapter);
    }

    /**
     * @return DatabaseAdapter
     */
    private function createDbAdapater()
    {
        return new DatabaseAdapter();
    }

    /**
     * @param DatabaseAdapter $dbAdapter
     * @param $localeId
     * @return ConfiguratorTranslate
     */
    private function createTranlate(DatabaseAdapter $dbAdapter, $localeId, $fallbackLocaleId) {
        return new ConfiguratorTranslate($dbAdapter, $localeId, $fallbackLocaleId);
    }
}