<?php

namespace Shopware\SwagVariantFilter\Bundle\SearchBundle\Facet;


use Shopware\Bundle\SearchBundle\FacetInterface;

/**
 * Class ProductVariantFacet
 *
 * @package Shopware\SwagVariantFilter\Bundle\SearchBundle\Facet
 */
class ProductVariantFacet implements FacetInterface
{

    /**
     * Defines the unique name for the facet for re identification.
     * @return string
     */
    public function getName()
    {
        return 'swag-variant-filter-product-variant-facet';
    }
}