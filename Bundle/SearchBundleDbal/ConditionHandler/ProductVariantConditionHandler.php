<?php
/*
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

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
     * {@inheritdoc}
     */
    public function generateCondition(
        ConditionInterface $condition,
        QueryBuilder $query,
        ShopContextInterface $context
    ) {
        $ids = $condition->getProductVariantIds();

        if (!$ids) {
            return;
        }

        $query
            ->innerJoin(
                'product',
                's_articles_details',
                'variantFilterArticleDetails',
                'variantFilterArticleDetails.articleID = product.id'
            );

        foreach ($ids as $groupId => $variantOptions) {
            $tableAlias = 'variantFilterArticleDetails' . $groupId;
            $paramAlias = ':options' . $groupId;

            $query->innerJoin(
                'variantFilterArticleDetails',
                's_article_configurator_option_relations',
                $tableAlias,
                'variantFilterArticleDetails.id = ' . $tableAlias . '.article_id AND ' . $tableAlias. '.option_id IN (' . $paramAlias . ')'
            )
            ->setParameter($paramAlias, $variantOptions, Connection::PARAM_INT_ARRAY);
        }
    }
}
