<?php

namespace Shopware\SwagVariantFilter\Components\Common;

/**
 * Class FilterGroupAbstract
 *
 * Representation of a single filter group, containing many options
 *
 * @package Shopware\SwagVariantFilter\LegacyFilter
 */
abstract class FilterGroupAbstract
{

    /**
     * @var string
     */
    private $label;

    /**
     * @var int
     */
    private $id;

    /**
     * @var array
     */
    private $options = array();

    private $hasActiveOption = false;

    /**
     * @param int $id
     * @param string $label
     */
    public function __construct($id, $label)
    {
        $this->id = (int) $id;
        $this->label = (string) $label;
    }

    /**
     * @param $id
     * @param $label
     * @param bool $isActive
     * @return $this
     */
    public function addOption($id, $label, $isActive = false)
    {
        $this->options[$id] = $this->createOption(
            (int) $id,
            (string) $label,
            (bool) $isActive
        );

        if ($isActive) {
            $this->hasActiveOption = true;
        }

        return $this;
    }

    /**
     * @param int $id
     * @param string $label
     * @param bool $isActive
     * @return FilterOptionAbstract
     */
    abstract protected function createOption($id, $label, $isActive);

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function hasActiveOptions()
    {
        return $this->hasActiveOption;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}