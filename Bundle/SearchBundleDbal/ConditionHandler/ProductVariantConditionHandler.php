<?php
namespace Shopware\SwagVariantFilter\Bundle\SearchBundleDbal\ConditionHandler;


use Doctrine\DBAL\Connection;
use Shopware\Bundle\SearchBundle\ConditionInterface;
use Shopware\Bundle\SearchBundleDBAL\ConditionHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilder;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use Shopware\SwagVariantFilter\Bundle\SearchBundle\Condition\ProductVariantCondition;

/**
 * Class ProductVariantConditionHandler
 *
 * @package Shopware\SwagVariantFilter\Bundle\SearchBundleDbal\ConditionHandler
 */
class ProductVariantConditionHandler implements ConditionHandlerInterface
{

    /**
     * {@inheritdoc}
     */
    public function supportsCondition(ConditionInterface $condition)
    {
        return ($condition instanceof ProductVariantCondition);
    }

    /**
     * Handles the passed condition object.
     * Extends the provided query builder with the specify conditions.
     * Should use the andWhere function, otherwise other conditions would be overwritten.
     *
     * @param ProductVariantCondition $condition
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
        $ids = $condition->getProductVariantIds();

        if (!$ids) {
            return;
        }

        $query->innerJoin(
            'variant',
            's_article_configurator_option_relations',
            'confoptionsrel',
            'variant.id = confoptionsrel.article_id AND confoptionsrel.option_id IN (:optionIds)'
        )
            ->setParameter(':optionIds', $ids, Connection::PARAM_INT_ARRAY);
    }
}