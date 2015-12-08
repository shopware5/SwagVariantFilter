<?php
/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

/**
 * Shopware SwagVariantFilter Plugin
 *
 */
class Shopware_Plugins_Frontend_SwagVariantFilter_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    /**
     * Installs the plugin
     *
     * Creates and subscribe the events and hooks
     * Creates the Backend Form
     *
     * @return bool
     */
    public function install()
    {
        $this->createForm();
        $this->createTranslations();
        $this->subscribeEvent('Enlight_Controller_Front_DispatchLoopStartup', 'onStartDispatch');

        return true;
    }

    /**
     * registers the namespace + services
     */
    public function afterInit()
    {
        $this->Application()->Loader()->registerNamespace('Shopware\SwagVariantFilter', $this->Path());
    }

    /**
     * Init Services & Subscribers
     *
     * @param Enlight_Controller_EventArgs $args
     */
    public function onStartDispatch(Enlight_Controller_EventArgs $args)
    {
        if (!$this->assertVersionGreaterThen('5')) {
            $this->initializeLegacy($args);
            return;
        }

        $this->Application()->Events()->addSubscriber(new Shopware\SwagVariantFilter\Subscriber\Filter());
        $this->Application()->Events()->addSubscriber(new Shopware\SwagVariantFilter\Subscriber\ServiceContainer(
            Shopware()->Plugins()->Frontend()->SwagVariantFilter()->Config(),
            Shopware()->Front()->Request()
        ));
    }

    /**
     * Initialize legacy SW4 handlers
     *
     * @param $args
     */
    private function initializeLegacy(Enlight_Controller_EventArgs $args)
    {
        $requestHelper = new \Shopware\SwagVariantFilter\Components\LegacyFilter\RequestHelper($args->getRequest());
        $this->Application()->Events()->addSubscriber(new \Shopware\SwagVariantFilter\Subscriber\LegacyServiceContainer($requestHelper));
        $this->Application()->Events()->addSubscriber(new Shopware\SwagVariantFilter\Subscriber\Legacy($requestHelper));
    }

    /*
     * Return Plugin-Version
     * @return String
     */
    public function getVersion()
    {
        $info = json_decode(file_get_contents(__DIR__ . '/plugin.json'), true);

        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    /**
     * Get (nice) name for plugin manager list
     * @return string
     */
    public function getLabel()
    {
        return 'Varianten Filter';
    }

    /**
     * @param string $version
     * @return bool
     */
    public function update($version)
    {
        return true;
    }

    /**
     * Creates the backend config form
     *
     */
    protected function createForm()
    {
        $form = $this->Form();

        $form->setElement('text', 'categoryids', array(
            'label' => 'Aktiviert in diesen Kategorien (Komma separiert)',
            'value' => "",
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP
        ));
        $form->setElement('number', 'mininstock', array(
            'label' => 'Artikel im Filterergebnis verbergen, falls Lagerbestand kleiner',
            'description' => '',
            'value' => 1,
            'required' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP
        ));
    }

    /**
     * Creates Translation
     */
    public function createTranslations()
    {
        $form = $this->Form();
        $translations = array(
            'en_GB' => array(
                'categoryids' => 'Enabled in these categories (comma separated)',
            )
        );

        $shopRepository = Shopware()->Models()->getRepository('\Shopware\Models\Shop\Locale');
        foreach ($translations as $locale => $snippets) {
            $localeModel = $shopRepository->findOneBy(array(
                'locale' => $locale
            ));
            foreach ($snippets as $element => $snippet) {
                if ($localeModel === null) {
                    continue;
                }
                $elementModel = $form->getElement($element);
                if ($elementModel === null) {
                    continue;
                }
                $translationModel = new \Shopware\Models\Config\ElementTranslation();
                $translationModel->setLabel($snippet);
                $translationModel->setLocale($localeModel);
                $elementModel->addTranslation($translationModel);
            }
        }
    }
}
