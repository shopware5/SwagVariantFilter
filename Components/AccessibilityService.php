<?php
/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Shopware\SwagVariantFilter\Components;

use Shopware\SwagVariantFilter\Components\Common\ConfigAdapter;
use Shopware\SwagVariantFilter\Components\Common\AccessibilityInterface;

/**
 * Class AccessibilityService
 *
 * @package Shopware\SwagVariantFilter\Components
 */
class AccessibilityService implements AccessibilityInterface
{
    /**
     * @var null|bool
     */
    private $isAccessible;

    /**
     * @var ConfigAdapter
     */
    private $configAdapter;

    /**
     * @param ConfigAdapter $adapter
     */
    public function __construct(ConfigAdapter $adapter)
    {
        $this->configAdapter = $adapter;
    }


    /**
     * {@inheritdoc}
     */
    public function match($categoryId)
    {
        if (!$categoryId) {
            throw new \InvalidArgumentException('Missing required parameter $categoryId');
        }

        $this->isAccessible = false;

        if (!$this->configAdapter->hasEnabledCategories()) {
            $this->isAccessible = true;
            return;
        }

        if (in_array($categoryId, $this->configAdapter->getEnabledCategoryIds())) {
            $this->isAccessible = true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return (bool) $this->isAccessible;
    }
}
