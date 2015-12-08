<?php
/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

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
