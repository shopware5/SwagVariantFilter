<?php
/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Shopware\SwagVariantFilter\Components\LegacyFilter;

use Shopware\SwagVariantFilter\Components\Common\FilterDataFactory;

/**
 * Class FilterDataByCategoryFactory
 *
 * FilterStrategy, generate data by core category-ids
 *
 * @package Shopware\SwagVariantFilter\Components\LegacyFilter
 */
class FilterDataByCategoryFactory extends FilterDataFactory
{

    public function getRawData($values)
    {
        if (!is_array($values)) {
            $values = $this->dbAdapter->getSubcategories($values);
        }

        return $this->dbAdapter->getConfigurationOptionsFromCategoryIds($values);
    }
}
