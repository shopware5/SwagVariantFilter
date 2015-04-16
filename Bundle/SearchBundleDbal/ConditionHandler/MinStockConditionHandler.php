<?php


namespace Shopware\SwagVariantFilter\Bundle\SearchBundleDbal\ConditionHandler;


use Shopware\Bundle\SearchBundle\ConditionInterface;
use Shopware\Bundle\SearchBundleDBAL\ConditionHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use Shopware\SwagVariantFilter\Bundle\SearchBundle\Condition\MinStockCondition;

/**
 * Class MinStockConditionHandler
 *
 * Optional minStock handler
 *
 * @package Shopware\SwagVariantFilter\Bundle\SearchBundleDbal\ConditionHandler
 */
class MinStockConditionHandler implements ConditionHandlerInterface
{

    /**
     * Checks if the passed condition can be handled by this class.
     *
     * @param ConditionInterface $condition
     * @return bool
     */
    public function supportsCondition(ConditionInterface $condition)
    {
        return ($condition instanceof MinStockCondition);
    }

    /**
     * Handles the passed condition object.
     * Extends the provided query builder with the specify conditions.
     * Should use the andWhere function, otherwise other conditions would be overwritten.
     *
     * @param MinStockCondition $condition
     * @param QueryBuilder $query
     * @param ShopContextInterface $context
     * @return void
     */
    public function generateCondition(
        ConditionInterface $condition,
        QueryBuilder $query,
        ShopContextInterface $context
    )
    {
        if (!$condition->hasMinStock()) {
            return;
        }

        $query->andWhere('variant.instock > :minStock')
            ->setParameter(':minStock', $condition->getMinStock());
    }
}