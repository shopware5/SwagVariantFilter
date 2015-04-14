<?php

namespace Shopware\SwagVariantFilter\Bundle\SearchBundleDbal\FacetHandler;

use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\FacetInterface;
use Shopware\Bundle\SearchBundle\FacetResultInterface;
use Shopware\Bundle\SearchBundleDBAL\FacetHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\QueryBuilderFactory;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\SwagVariantFilter\Bundle\SearchBundle\Condition\ProductVariantCondition;
use Shopware\SwagVariantFilter\Bundle\SearchBundle\Facet\ProductVariantFacet;
use Shopware\SwagVariantFilter\Bundle\SearchBundle\FacetResult\ProductVariantFacetResult;
use Shopware\SwagVariantFilter\Components\ProductVariantService;

/**
 * Class ProductVariantFacetHandler
 * @package Shopware\SwagVariantFilter\Bundle\SearchBundleDbal\FacetHandler
 */
class ProductVariantFacetHandler implements FacetHandlerInterface
{

    /**
     * @var ProductVariantService
     */
    private $productVariantService;
    /**
     * @var QueryBuilderFactory
     */
    private $queryBuilderFactory;

    private $snippetNamespace;

    /**
     * @param ProductVariantService $productVariantService
     * @param QueryBuilderFactory $queryBuilderFactory
     * @param \Enlight_Components_Snippet_Namespace $snippetNamespace
     */
    public function __construct(
        ProductVariantService $productVariantService,
        QueryBuilderFactory $queryBuilderFactory,
        \Enlight_Components_Snippet_Namespace $snippetNamespace
    )
    {
        $this->productVariantService = $productVariantService;
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->snippetNamespace = $snippetNamespace;
    }

    /**
     * Extends the query criteria query to generate enough data for the servicees
     *
     * @param FacetInterface $facet
     * @param Criteria $criteria
     * @param Struct\ShopContextInterface $context
     * @return null|FacetResultInterface|FacetResultInterface[]
     */
    public function generateFacet(
        FacetInterface $facet,
        Criteria $criteria,
        Struct\ShopContextInterface $context
    )
    {
        $activeOptions = [];

        $productVariantCondition = $criteria->getCondition('swag-variant-filter-product-variant');

        if ($productVariantCondition && $productVariantCondition instanceof ProductVariantCondition) {
            $activeOptions = $productVariantCondition->getProductVariantIds();
        }

        $queryCriteria = clone $criteria;
        $queryCriteria->resetConditions();

        $query = $this->queryBuilderFactory->createQuery($queryCriteria, $context);
        $query->select('configuratorOptions.group_id')
            ->innerJoin(
                'variant',
                's_article_configurator_option_relations',
                'configuratorOptionRelations',
                'configuratorOptionRelations.article_id = variant.id'
            )->innerJoin(
                'configuratorOptionRelations',
                's_article_configurator_options',
                'configuratorOptions',
                'configuratorOptions.id = configuratorOptionRelations.option_id'
            )
            ->groupBy('configuratorOptions.group_id');

        $ids = (array) $query->execute()->fetchAll(\PDO::FETCH_COLUMN);

        return new ProductVariantFacetResult(
            $this->productVariantService->getFilterConditions($ids, $activeOptions),
            $this->snippetNamespace->get('FilterHeadlineVariants', 'Variantfilter')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsFacet(FacetInterface $facet)
    {
        return ($facet instanceof ProductVariantFacet);
    }
}