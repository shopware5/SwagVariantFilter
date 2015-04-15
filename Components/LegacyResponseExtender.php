<?php
namespace Shopware\SwagVariantFilter\Components;

use Doctrine\DBAL\Driver\PDOStatement;
use Shopware\SwagVariantFilter\Components\Common\DatabaseAdapter;
use Shopware\SwagVariantFilter\Components\Common\FilterGroupAbstract;
use Shopware\SwagVariantFilter\Components\Common\FilterOptionAbstract;
use Shopware\SwagVariantFilter\Components\Common\ConfigAdapter;
use Shopware\SwagVariantFilter\Components\LegacyFilter\RequestHelper;

/**
 * Class LegacyResponseExtender
 *
 * Handle Response Handling and ListingController Extension
 *
 * @package Shopware\SwagVariantFilter\Components
 */
class LegacyResponseExtender
{

    /**
     * @var RequestHelper
     */
    private $requestHelper;

    /**
     * @var ConfigAdapter
     */
    private $configAdapter;

    /**
     * @var array
     */
    private $filterGroups;

    /**
     * @var DatabaseAdapter
     */
    private $databaseAdapter;

    /**
     * Expects list of active category id's
     *
     * @param RequestHelper $requestHelper
     * @param ConfigAdapter $configAdapter
     */
    public function __construct(RequestHelper $requestHelper, ConfigAdapter $configAdapter, DatabaseAdapter $dbAdapter)
    {
        $this->requestHelper = $requestHelper;
        $this->configAdapter = $configAdapter;
        $this->databaseAdapter = $dbAdapter;
    }

    /**
     * @param array $filterGroups
     * @return $this
     */
    public function fromFilterGroups(array $filterGroups)
    {
        $this->filterGroups = $filterGroups;

        return $this;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function requireFilterGroups()
    {
        if (!$this->filterGroups) {
            throw new \Exception('Missing required property "$filterGroups"');
        }

        return $this->filterGroups;
    }

    /**
     * Extend ListingController ResultQuery
     *
     * @param $baseQuery
     * @return mixed
     * @throws \Exception
     */
    public function extendQuery($baseQuery)
    {
        $tmpSQL = '';

        /** @var FilterGroupAbstract $group */
        foreach ($this->requireFilterGroups() as $group) {
            if (!$group->hasActiveOptions()) {
                continue;
            }

            $groupId = $group->getId();
            $optionIds = array();

            /** @var FilterOptionAbstract $option */
            foreach ($group->getOptions() as $option) {
                if (!$option->isActive()) {
                    continue;
                }

                $optionIds[] = $option->getId();
            }

            if (!count($optionIds)) {
                throw new \Exception('Fatal error, group has active item, although no item is marked active');
            }

            $tmpSQL = $tmpSQL . "
                JOIN s_article_configurator_option_relations AS acor$groupId
                ON acor$groupId.article_id = aDetails.id
                JOIN s_article_configurator_options AS aco$groupId
                ON acor$groupId.option_id IN (" . implode(",", $optionIds) . ") AND aco$groupId.group_id = $groupId
                AND acor$groupId.option_id = aco$groupId.id
                ";
        }

        $newSQL = "ON aTax.id = a.taxID";

        $newSQL .= " AND a.id IN (SELECT s_articles.id from s_articles, s_articles_details AS aDetails "
            . $tmpSQL . " WHERE  aDetails.articleID = s_articles.id and aDetails.active = 1) ";

        $whereSQL = " AND a.id NOT IN (SELECT s_articles.id from s_articles, s_articles_details AS aDetails "
            . $tmpSQL . " WHERE  aDetails.articleID = s_articles.id and aDetails.active = 1 AND aDetails.instock < {$this->configAdapter->getMinStock()}) ";

        // Match SW 4.1 as well as SW 408 and before
        $sql = preg_replace("#ON aTax.id ?= ?a.taxID#", $newSQL, $baseQuery);

        // append WHERE condition: filter articles which do not have sufficient  instock
        $search = "/ WHERE ag.articleID IS NULL\s*AND a.active=1/";
        if (preg_match($search, $sql)) {
            $replace = "WHERE ag.articleID IS NULL AND a.active=1" . $whereSQL;
            $sql = preg_replace($search, $replace, $sql);
        }

        return $sql;
    }

    /**
     * Extend ListingController Result
     *
     * @param $result
     * @return mixed
     */
    public function extendViewData($result)
    {

        $request = Shopware()->Front()->Request();

        $activePerPage = $this->requestHelper->getPerPage();

        foreach ($result["sPerPage"] as &$singlePerPage) {
            $singlePerPage["link"] .= "&oid=" . $this->requestHelper->getRawActiveOptionIds();
            if ($singlePerPage["markup"]) {
                $activePerPage = $singlePerPage["value"];
            }
        }

        $pages = $result["sPages"];
        $result["sNumberArticles"] = $this->getTotalCount($request);
        $result["sNumberPages"] = ceil($result["sNumberArticles"] / $activePerPage);
        $numbersArray = array();

        for ($i = 1; $i <= $result["sNumberPages"]; $i++) {
            if (!isset($pages['numbers'])) {
                continue;
            }

            foreach ($pages["numbers"] as $page) {
                $numbersArray[$i] = array(
                    "markup" => $pages["numbers"][$i]["markup"],
                    "value" => $pages["numbers"][$i]["value"],
                    "link" => $pages["numbers"][$i]["link"] . "&oid=" . $this->requestHelper->getRawActiveOptionIds()
                );
            }
        }
        $pages["numbers"] = $numbersArray;


        if (!empty($pages["previous"])) {
            $pages["previous"] .= "&oid=" . $this->requestHelper->getRawActiveOptionIds();
        }
        if (!empty($pages["next"])) {
            $pages["next"] .= "&oid=" . $this->requestHelper->getRawActiveOptionIds();
        }

        $result["sPages"] = $pages;

        $result["categoryParams"]["oid"] = $this->requestHelper->getRawActiveOptionIds();

        return $result;
    }

    /**
     * Query total number of currently selected articles
     *
     * @param $request
     * @return string
     */
    private function getTotalCount(\Enlight_Controller_Request_Request $request)
    {
        $groupIds = array();

        /** @var FilterGroupAbstract $group */
        foreach ($this->filterGroups as $group) {
            $groupIds[] = $group->getId();
        }

        $subCategories = $this->databaseAdapter->getSubcategories($request->sCategory);

        // Append condition - filter articles which do not have sufficient instock
        $stockQueryBuilder = Shopware()->Models()->getDBALQueryBuilder();
        $stockQueryBuilder->select('iart.id')
            ->from('s_articles', 'iart')
            ->join('iart', 's_articles_details', 'iaDetails', 'iaDetails.articleid = iart.id')
            ->join('iaDetails', 's_article_configurator_option_relations', 'iacor', 'iacor.article_id = iaDetails.id')
            ->join('iacor', 's_article_configurator_options', 'iaco', $stockQueryBuilder->expr()->in('iacor.option_id',
                    $this->requestHelper->getRequestedVariantIds()) . ' AND ' .
                $stockQueryBuilder->expr()->in('iaco.group_id', $groupIds) .
                ' AND iacor.option_id = iaco.id')
            ->where('iaDetails.active = 1 AND iaDetails.instock < ' . $this->configAdapter->getMinStock() . '');


        $builder = Shopware()->Models()->getDBALQueryBuilder();
        /** @var PDOStatement $stmt */
        $stmt = $builder->select('COUNT(DISTINCT ad.articleID)')
            ->from('s_article_configurator_option_relations', 'acor')
            ->join('acor', 's_articles_details', 'ad', 'acor.article_id = ad.id AND ad.articleID NOT IN (' . $stockQueryBuilder->getSQL() . ')')
            ->join('ad', 's_articles_categories', 'ac', 'ad.articleID=ac.articleID AND ac.categoryID IN (:subCategories)')
            ->where('option_id IN (:optionIds)')
            ->setParameter(':optionIds', $this->requestHelper->getRequestedVariantIds())
            ->setParameter(':subCategories', $subCategories)
            ->execute();

        return (int) $stmt->fetch(\PDO::FETCH_COLUMN, 0);

    }
}