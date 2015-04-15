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
     * @var int
     */
    private $fallbackLocaleId;

    /**
     * @param DatabaseAdapter $dbAdapter
     * @param int $localeId
     * @param $fallbackLocaleId
     */
    public function __construct(DatabaseAdapter $dbAdapter, $localeId, $fallbackLocaleId) {
        if(!$localeId) {
            throw new \InvalidArgumentException('Missing required param $localeId');
        }

        if(!$fallbackLocaleId) {
            throw new \InvalidArgumentException('Missing required param $fallbackLocaleId');
        }

        $this->dbAdapter = $dbAdapter;
        $this->localeId = $localeId;
        $this->fallbackLocaleId = $fallbackLocaleId;
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

        // all keys may be multiple times in result set, the first is allways the most important
        $rawTranslateData = $this->dbAdapter->getConfiguratorTranslations($this->localeId, $this->fallbackLocaleId);

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

            //remove duplicates
            if(isset($currentGroup[$translation['objectkey']])) {
                continue;
            }

            $currentGroup[$translation['objectkey']] = $data['name'];
        }
    }
}