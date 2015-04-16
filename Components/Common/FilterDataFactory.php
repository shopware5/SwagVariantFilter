<?php
namespace Shopware\SwagVariantFilter\Components\Common;

use Shopware\SwagVariantFilter\Components\Common\DatabaseAdapter;

/**
 * Class FilterDataFactory
 *
 * select raw data based on differing conditions
 *
 * @package Shopware\SwagVariantFilter\Components\Common
 */
abstract class FilterDataFactory
{

    /**
     * @var DatabaseAdapter
     */
    protected $dbAdapter;

    /**
     * @param DatabaseAdapter $dbAdapter
     */
    public function __construct(DatabaseAdapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    abstract public function getRawData($values);
}