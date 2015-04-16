<?php

namespace Shopware\SwagVariantFilter\Components\Common;

/**
 * Interface AccessibilityInterface
 *
 * Are filters enabled for this request?
 *
 * @package Shopware\SwagVariantFilter\Components\Common
 */
interface AccessibilityInterface
{

    /**
     * Match configuration with current request
     *
     * @throws \InvalidArgumentException
     * @param $categoryId
     * @return mixed
     */
    public function match($categoryId);

    /**
     * @throws \BadFunctionCallException
     * @return bool
     */
    public function isValid();
}