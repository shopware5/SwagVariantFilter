<?php
/**
 * Shopware 4.0
 * Copyright © 2012 shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 *
 * @category   Shopware
 * @package    Shopware_Plugins
 * @subpackage Plugin
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    1
 * @author     shopware AG
 * @author     $Author$
 */

/**
 * Shopware SwagVariantFilter Plugin
 *
 * todo@all: Documentation
 */

class Shopware_Plugins_Frontend_SwagVariantFilter_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    /**
     * Installs the plugin
     *
     * Creates and subscribe the events and hooks
     * Creates the Backend Form
     *
     * @return bool
     */
    public function install()
    {
        $this->subscribeEvents();
        $this->createForm();
        $this->createTranslations();
        return true;
    }

    /*
     * Return Plugin-Version
     * @return String
     */
    public function getVersion()
    {
        return '1.0.1';
    }

    /**
     * Get (nice) name for plugin manager list
     * @return string
     */
    public function getLabel()
    {
        return 'Varianten Filter';
    }

    /**
     * @param string $version
     * @return bool
     */
    public function update($version)
    {
        return true;
    }

    /**
     * Creates the backend config form
     *
     */
    protected function createForm()
    {
        $form = $this->Form();

        $form->setElement('text', 'categoryids', array(
            'label' => 'Aktiviert in diesen Kategorien (Komma separiert)',
            'value' => "",
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP
        ));
    }

    /**
     * Creates Translation
     */
    public function createTranslations()
    {
        $form = $this->Form();
        $translations = array(
            'en_GB' => array(
                'categoryids' => 'Enabled in these categories (comma separated)',
            )
        );

        $shopRepository = Shopware()->Models()->getRepository('\Shopware\Models\Shop\Locale');
        foreach($translations as $locale => $snippets) {
            $localeModel = $shopRepository->findOneBy(array(
                'locale' => $locale
            ));
            foreach($snippets as $element => $snippet) {
                if($localeModel === null){
                    continue;
                }
                $elementModel = $form->getElement($element);
                if($elementModel === null) {
                    continue;
                }
                $translationModel = new \Shopware\Models\Config\ElementTranslation();
                $translationModel->setLabel($snippet);
                $translationModel->setLocale($localeModel);
                $elementModel->addTranslation($translationModel);
            }
        }
    }

    /**
     * Subscribe events
     *
     */
    public function subscribeEvents()
    {
        // Hoeren auf Controls: listing
        $this->subscribeEvent('Enlight_Controller_Action_PostDispatch_Frontend_Listing', 'onPostDispatch');
        //Filter
        $this->subscribeEvent('Shopware_Modules_Articles_sGetArticlesByCategory_FilterSql','onGetArticlesByCategoryFilterSql');
    }

    /**
     * @param Enlight_Event_EventArgs $args
     * @return string
     */
    public function onGetArticlesByCategoryFilterSql (Enlight_Event_EventArgs $args)
    {
        $sql = $args->getReturn();
        $optionIdArray = explode  ("|", Shopware()->System()->_GET['oid']);
        $optionIdArray = array_map('intval',$optionIdArray);
        $optionIDs = implode (",", $optionIdArray);
        if ($optionIDs != "")
        {

              $sqlTmp = "
                            SELECT
                                s_article_configurator_options.id,
                                s_article_configurator_options.group_id
                                FROM
                                s_article_configurator_options
                                WHERE id in ($optionIDs) ORDER BY group_id;
                      ";

            $results = Shopware()->Db()->fetchAll($sqlTmp, array());

            $newSQL = "ON aTax.id=a.taxID
            ";

            $groups = Array();
            foreach($results as $row)
            {
                if (!is_array ($groups[$row["group_id"]]))
                {
                    $groups[$row["group_id"]] = Array();
                }
                array_push($groups[$row["group_id"]],$row["id"]);
            }

            $tmpSQL = "";
            foreach($groups as $group => $idArray)
            {
                $tmpSQL = $tmpSQL . "
                    JOIN s_article_configurator_option_relations AS acor$group
                    ON acor$group.article_id = aDetails.id
                    JOIN s_article_configurator_options AS aco$group
                    ON acor$group.option_id in (" . implode (",", $idArray) . ") and aco$group.group_id = $group
                    AND acor$group.option_id=aco$group.id
                    ";
            }
            if ($tmpSQL != "")
            {
                $newSQL .= " AND a.id in (SELECT s_articles.id from s_articles, s_articles_details as aDetails "
                        . $tmpSQL . " where  aDetails.articleID=s_articles.id and aDetails.active=1) ";
            }

            // Match SW 4.1 as well as SW 408 and before
            $sql = preg_replace("#ON aTax.id ?= ?a.taxID#", $newSQL, $sql);
        }
        return $sql;
    }

    /**
     * Event
     * @param Enlight_Event_EventArgs $args
     */
    public function onPostDispatch(Enlight_Event_EventArgs $args)
    {
        $request = $args->getSubject()->Request();
        $response = $args->getSubject()->Response();

        if (!$request->isDispatched()
            || $response->isException()
        ){
            return;
        }

        $config = Shopware()->Plugins()->Frontend()->SwagVariantFilter()->Config();

        if ($config->categoryids != "")
        {
            $currentCategoryId = $request->sCategory;
            $categoryArray = explode  (",", $config->categoryids);
            $found = false;
            foreach ($categoryArray as $categoryId) {
                if ($categoryId == $currentCategoryId)
                {
                  $found = true;
                  break;
                }
            }
            if ($found == false)
            {
              return;
            }
        }

        $idArray = Array();

        $subCategories = Shopware()->Modules()->Categories()->sGetWholeCategoryTree($request->sCategory);
        
        foreach ($subCategories as $entry) {
            array_push($idArray, $entry["id"]);
        }
        array_push($idArray, $request->sCategory);
        $subCategoriesTxt = implode (",", $idArray );

        $additionSQL = "";
        $optionIdVar = Shopware()->System()->_GET['oid'];
        if($optionIdVar != "")
            $optionIdArray = explode("|", $optionIdVar);
        else
            $optionIdVar = "";

        $optionIdArray = array_map('intval',$optionIdArray);
        $optionIDs = implode (",", $optionIdArray);
        if ($optionIDs != "")
        {
            $additionSQL = "
                SELECT DISTINCT
                  s_articles_details.articleID
                  FROM
                  s_articles_details
                  JOIN s_article_configurator_option_relations
                    ON s_article_configurator_option_relations.article_id = s_articles_details.id
                    AND s_article_configurator_option_relations.option_id IN ($optionIDs)
                  JOIN s_articles_categories
                    ON s_articles_details.articleID=s_articles_categories.articleID
                    AND s_articles_categories.categoryID IN ($subCategoriesTxt)
                ";
            $additionSQL = "AND s_articles_categories.articleID IN ($additionSQL)";
        }

        $sql = "
            SELECT DISTINCT
                s_article_configurator_groups.id AS GroupId,
                s_article_configurator_groups.name AS GroupName,
                s_article_configurator_options.id AS OptionId,
                s_article_configurator_options.name AS OptionName

            FROM
                s_article_configurator_groups

            INNER JOIN s_article_configurator_options
            ON s_article_configurator_options.group_id = s_article_configurator_groups.id

            INNER JOIN s_article_configurator_option_relations
            ON s_article_configurator_option_relations.option_id = s_article_configurator_options.id

            INNER JOIN s_articles_details
            ON s_articles_details.id = s_article_configurator_option_relations.article_id

            INNER JOIN s_articles_categories
            ON s_articles_categories.articleID = s_articles_details.articleID
            AND s_articles_categories.categoryID IN ($subCategoriesTxt)
            $additionSQL

            ORDER BY s_article_configurator_groups.id, s_article_configurator_options.name

        ";


        $results = Shopware()->Db()->fetchAll($sql, array());
        $lastGroupId = 0;
        $groupArray = Array();
        $dataArray = Array();
        $optionIdHash = Array();

        foreach ($optionIdArray as $id) {
          $optionIdHash[$id] = true;
        }

        $langCode = Shopware()->Shop()->getId();
        $translator  = new Shopware_Components_Translation();
        foreach($results as $row) {
            if ($row["GroupId"] != $lastGroupId)
            {
              if ($lastGroupId != 0)
              {
                array_push ($groupArray, $dataArray);
              }

              $translation = $translator->read($langCode,'configuratorgroup',$row["GroupId"]);
              if($translation['name'] == "")
              {
                 $translation['name'] = $row['GroupName'];
              }
              $dataArray = Array();
              $dataArray["GroupId"] = $row["GroupId"];
              $dataArray["GroupName"] = $translation['name'];
              $dataArray["SubValueIsActive"] = false;
              $dataArray["LinkRemoveOption"] = $optionIdVar;
              $dataArray["Options"] = array();
              $lastGroupId = $row["GroupId"];
            }
            $optionID = $optionIdVar;
            if ($optionIdHash[$row["OptionId"]] != true)
            {
                if ( $optionIdVar != "")
                {
                    $optionID .= "|";
                }
                $optionID .= $row["OptionId"];
            }
            else
            {
               $dataArray["SubValueIsActive"] = true;
               $optionID = $dataArray["LinkRemoveOption"];
               $optionID = "|" . $optionID . "|";
               $optionID = str_replace("|".$row["OptionId"]."|", "|", $optionID);
               $optionID = trim ($optionID, "|");
               $optionID = rtrim ($optionID, "|");
               $dataArray["LinkRemoveOption"] = $optionID;
            }

            $translation = $translator->read($langCode,'configuratoroption',$row["OptionId"]);
            if($translation['name'] == "")
            {
                $translation['name'] = $row["OptionName"];
            }

            array_push ($dataArray["Options"],
                array(
                    "Id" => $row["OptionId"],
                    "IdForURL" => $optionID,
                    "Name" => $translation['name'],
                    "Active" => $optionIdHash[$row["OptionId"]] == true,
                ));
        }
        if ($lastGroupId != 0)
        {
            array_push ($groupArray, $dataArray);
        }

        $view = $args->getSubject()->View();
        $config = Shopware()->Plugins()->Frontend()->SwagVariantFilter()->Config();
        $view->SwagVariantFilterConfig = $config;
        $view->GroupArray = $groupArray;
        $view->BaseURL = $args->getSubject()->Request()->getBaseUrl() . $args->getSubject()->Request()->getPathInfo();
        $this->Application()->Template()->addTemplateDir ($this->Path() . 'views/');
        $view->extendsTemplate("frontend/index.tpl");
    }
}