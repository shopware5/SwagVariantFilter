<?php
/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Shopware\SwagVariantFilter\Bundle\SearchBundle\FacetResult;

use Shopware\Bundle\SearchBundle\FacetResult\FacetResultGroup;
use Shopware\Bundle\SearchBundle\FacetResult\ValueListFacetResult;
use Shopware\SwagVariantFilter\Components\Common\RequestAdapter;
use Shopware\SwagVariantFilter\Components\Common\FilterGroup;

/**
 * Class ProductVariantFacetResult
 *
 * Maps the internal FilterGroups to FacetResults
 *
 * @package Shopware\SwagVariantFilter\Bundle\SearchBundle\FacetResult
 */
class ProductVariantFacetResult extends FacetResultGroup
{

    /**
     * @param array $variantGroups
     * @param null|string $label
     */
    public function __construct(array $variantGroups, $label)
    {
        $facetResults = array();

        /** @var  $group FilterGroup */
        foreach ($variantGroups as $group) {
            $facetResults[] = $this->createValueListFacetResult(
                $group->getId(),
                true,
                $group->getLabel(),
                $group->getOptions(),
                RequestAdapter::PARAM_NAME . '_' . $group->getId()
            );
        }


        parent::__construct(
            $facetResults,
            $label,
            'SwagVariantFilter',
            array()
        );
    }

    /**
     * @param $name
     * @param $active
     * @param $label
     * @param $value
     * @param $fieldName
     * @return ValueListFacetResult
     */
    private function createValueListFacetResult($name, $active, $label, $value, $fieldName)
    {
        return new ValueListFacetResult($name, $active, $label, $value, $fieldName);
    }
}
