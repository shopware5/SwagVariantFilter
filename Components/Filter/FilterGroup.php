<?php
/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Shopware\SwagVariantFilter\Components\Filter;

use Shopware\SwagVariantFilter\Components\Common\FilterGroupAbstract;

class FilterGroup extends FilterGroupAbstract
{

    /**
     * @param int $id
     * @param string $label
     * @param bool $isActive
     * @return FilterOption
     */
    protected function createOption($id, $label, $isActive)
    {
        return new FilterOption($id, $label, $isActive);
    }
}
