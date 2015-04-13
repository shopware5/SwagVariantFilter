<?php
/**
 * Shopware 4.0
 * Copyright © 2012 shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 *
 * @category   Shopware
 * @package    Shopware_Plugins
 * @subpackage Plugin
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    1
 * @author     shopware AG
 * @author     $Author$
 */

/**
 * Shopware SwagVariantFilter Plugin
 *
 * todo@all: Documentation
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
     * @param Enlight_Event_EventArgs $args
     */
    public function onStartDispatch(Enlight_Event_EventArgs $args)
    {
        $requestHelper = new \Shopware\SwagVariantFilter\Components\LegacyFilter\RequestHelper($args->getRequest());
        $optionHelper = new \Shopware\SwagVariantFilter\Components\LegacyFilter\OptionHelper(Shopware()->Plugins()->Frontend()->SwagVariantFilter()->Config());
        Shopware()->Container()->set(
            'SwagVariantLegacyFilter',
            new Shopware\SwagVariantFilter\Components\LegacyFilterService(
                $requestHelper,
                $optionHelper
            )
        );

        Shopware()->Container()->set(
            'SwagVariantLegacyResponseExtender',
            new \Shopware\SwagVariantFilter\Components\LegacyResponseExtender(
                $requestHelper,
                $optionHelper
            )
        );

        $this->Application()->Events()->addSubscriber(new Shopware\SwagVariantFilter\Subscriber\Legacy());

        $this->registerDebugErrorHandler();
    }

    public function registerDebugErrorHandler()
    {
        $errorMap = array(
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_NOTICE => 'E_NOTICE',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        );

        set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($errorMap) {
            if (strpos($errfile, '.tpl') !== false || strpos($errstr, '.tpl') !== false) {
                return;
            }

            $message = print_r([
                'Type' => $errorMap[$errno],
                'Message' => $errstr,
                'File' => $errfile,
                'Line' => $errline
            ], true);

            if (E_RECOVERABLE_ERROR == $errno) {
                throw new Exception('E_RECOVERABLE_ERROR: <pre>' . $message . '</pre>');
            }


            file_put_contents('/var/www/master/error.log', $message, FILE_APPEND);
        });
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
