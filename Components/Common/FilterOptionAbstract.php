<?php
namespace Shopware\SwagVariantFilter\Components\Common;

/**
 * Class FilterOption
 *
 * @todo This class should be in hierarchy with Shopware\Bundle\SearchBundle\FacetResult\ValueListItem wich currently is not possible due to backwards compatibility with SW4
 *
 * @package Shopware\SwagVariantFilter\Components\LegacyFilter
 */
abstract class FilterOptionAbstract
{

    /**
     * @var bool
     */
    private $active;

    /**
     * @var string
     */
    private $label;

    /**
     * @var int
     */
    private $id;


    /**
     * @param int $id
     * @param string $label
     * @param bool $active
     */
    public function __construct($id, $label, $active = false)
    {
        $this->label = (string) $label;
        $this->id = (int) $id;
        $this->active = (bool) $active;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}