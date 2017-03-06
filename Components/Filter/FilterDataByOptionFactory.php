<?php
/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Shopware\SwagVariantFilter\Components\Filter;

use \Shopware\SwagVariantFilter\Components\Common\FilterDataFactory as AbstractFactory;

class FilterDataByOptionFactory extends AbstractFactory
{
    /**
     * @param $values
     * @return mixed
     */
    public function getRawData($values)
    {
        if (!is_array($values)) {
            throw new \InvalidArgumentException('Invalid param $values');
        }

        return $this->dbAdapter->getConfigurationOptionsFromOptionIds($values);
    }
}
