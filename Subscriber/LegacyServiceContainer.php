<?php


namespace Shopware\SwagVariantFilter\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\SwagVariantFilter\Components\Common\ConfigAdapter;
use Shopware\SwagVariantFilter\Components\Common\ConfiguratorTranslate;
use Shopware\SwagVariantFilter\Components\Common\DatabaseAdapter;
use Shopware\SwagVariantFilter\Components\LegacyFilter\RequestHelper;
use Shopware\SwagVariantFilter\Components\LegacyResponseExtender;
use Shopware\SwagVariantFilter\Components\LegacyFilterService;

class LegacyServiceContainer implements SubscriberInterface {

    /**
     * @var RequestHelper
     */
    private $requestHelper;

    /**
     * @var DatabaseAdapter
     */
    private $databaseAdapter;

    /**
     * @var ConfigAdapter
     */
    private $configAdapter;

    /**
     * @var ConfiguratorTranslate
     */
    private $translate;

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
            'Enlight_Bootstrap_InitResource_SwagVariantLegacyFilter' => 'getLegacyFilterService',
            'Enlight_Bootstrap_InitResource_SwagVariantLegacyResponseExtender' => 'getLegacyQueryExtender',
        );
    }

    /**
     * @return LegacyFilterService
     */
    public function getLegacyFilterService() {
        return new LegacyFilterService(
            $this->requestHelper,
            $this->getConfigAdapter(),
            $this->getDatabaseAdapter(),
            $this->getTranslate()
        );
    }

    /**
     * @return LegacyResponseExtender
     */
    public function getLegacyQueryExtender() {
        return new LegacyResponseExtender(
            $this->requestHelper,
            $this->getConfigAdapter(),
            $this->getDatabaseAdapter()
        );
    }

    /**
     * @return ConfigAdapter
     */
    private function getConfigAdapter() {
        if(!$this->configAdapter) {
            $this->configAdapter = new ConfigAdapter(
                Shopware()->Plugins()->Frontend()->SwagVariantFilter()->Config()
            );
        }

        return $this->configAdapter;
    }

    /**
     * @return DatabaseAdapter
     */
    private function getDatabaseAdapter() {
        if(!$this->databaseAdapter) {
            $this->databaseAdapter = new DatabaseAdapter();
        }

        return $this->databaseAdapter;
    }

    /**
     * @return ConfiguratorTranslate
     */
    private function getTranslate() {
        if(!$this->translate) {

            $fallbackLocaleId = -1;
            if(Shopware()->Shop()->getFallback()) {
                $fallbackLocaleId = Shopware()->Shop()->getFallback()->getLocale()->getId();
            }

            $this->translate = new ConfiguratorTranslate(
                $this->getDatabaseAdapter(),
                Shopware()->Shop()->getLocale()->getId(),
                $fallbackLocaleId
            );
        }

        return $this->translate;
    }
}