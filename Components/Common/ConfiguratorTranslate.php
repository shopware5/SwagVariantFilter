<?php


namespace Shopware\SwagVariantFilter\Components\Common;

/**
 * Class ConfiguratorTranslate
 *
 * Lazy loading translator
 *
 * @package Shopware\SwagVariantFilter\Components\Common
 */
class ConfiguratorTranslate {

    const KEY = 'name';

    /*
     * @var DatabaseAdapter
     */
    private $dbAdapter;

    /**
     * @var null|array
     */
    private $translations;

    /**
     * @var int
     */
    private $localeId;

    /**
     * @param DatabaseAdapter $dbAdapter
     * @param int $localeId
     */
    public function __construct(DatabaseAdapter $dbAdapter, $localeId) {
        $this->dbAdapter = $dbAdapter;
        $this->localeId = $localeId;
    }

    /**
     * @param int $id
     * @param string $default
     * @return string
     */
    public function getOptionLabel($id, $default = '') {
        $optionTranslations = $this->getOptionTranslations();

        if(!isset($optionTranslations[$id]) || !$optionTranslations[$id]) {
            return $default;
        }

        return $optionTranslations[$id];
    }

    /**
     * @param int $id
     * @param string $default
     * @return string
     */
    public function getGroupLabel($id, $default = '') {
        $groupTranslations = $this->getGroupTranslations();

        if(!isset($groupTranslations[$id]) || !$groupTranslations[$id]) {
            return $default;
        }

        return $groupTranslations[$id];
    }

    /**
     * @return array
     */
    private function getGroupTranslations() {
        $ret = $this->getTranslations();
        return $ret['groups'];
    }

    /**
     * @return array
     */
    private function getOptionTranslations() {
        $ret = $this->getTranslations();
        return $ret['options'];
    }

    /**
     * @return array
     */
    private function getTranslations() {
        if(!$this->translations) {
            $this->translations = array(
                'groups' => array(),
                'options' => array(),
            );
            $this->loadTranslations();
        }

        return $this->translations;
    }

    /**
     * @throws \Exception
     */
    private function loadTranslations() {
        $rawTranslateData = $this->dbAdapter->getConfiguratorTranslations($this->localeId);

        foreach($rawTranslateData as $translation) {
            switch($translation['objecttype']) {
                case 'configuratorgroup':
                    $currentGroup = &$this->translations['groups'];
                    break;
                case 'configuratoroption':
                    $currentGroup = &$this->translations['options'];
                    break;
                default:
                    throw new \Exception('Invalid objectttype in resultset "' . $translation['objecttype'] . '"');
            }

            if(!$translation['objectdata']) {
                continue;
            }

            $data = unserialize($translation['objectdata']);

            if(!isset($data['name']) || !$data['name']) {
                continue;
            }

            $currentGroup[$translation['objectkey']] = $data['name'];
        }
    }
}