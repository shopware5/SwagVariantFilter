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
 * @author     Markus Wenke
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
        return '1.0.0';
    }

    /**
     * Get (nice) name for plugin manager list
     * @return string
     */
    public function getLabel()
    {
        return 'Variantenfilter';
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
                'categoryids' => 'Enabled in this categories (comma separated)',
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
     * FilterSQLEvent
     *
     */
    public function onGetArticlesByCategoryFilterSql (Enlight_Event_EventArgs $args)
    {
        $sql = $args->getReturn();
        $OptionIdArray = explode  ("|", Shopware()->System()->_GET['oid']);
        $sOptionIDs = implode (",", $OptionIdArray);
        if ($sOptionIDs != "")
        {

              $SQLTmp = "
                            SELECT
                                s_article_configurator_options.id,
                                s_article_configurator_options.group_id
                                FROM
                                s_article_configurator_options
                                WHERE id in ($sOptionIDs) ORDER BY group_id;
";

            $results = Shopware()->Db()->fetchAll($SQLTmp, array());

            $NewSQL = "ON aTax.id=a.taxID
            ";

            $Groups = Array();
            foreach($results as $Row)
            {
                if (!is_array ($Groups[$Row["group_id"]]))
                {
                    $Groups[$Row["group_id"]] = Array();
                }
                array_push($Groups[$Row["group_id"]],$Row["id"]);
            }

            $TmpSQL = "";
            foreach($Groups as $Group => $IDArray)
            {
                $TmpSQL = $TmpSQL . "
                    JOIN s_article_configurator_option_relations AS acor$Group
                    ON acor$Group.article_id = aDetails.id
                    JOIN s_article_configurator_options AS aco$Group
                    ON acor$Group.option_id in (" . implode (",", $IDArray) . ") and aco$Group.group_id = $Group
                    AND acor$Group.option_id=aco$Group.id
                    ";
            }
            if ($TmpSQL != "")
            {
                $NewSQL .= " AND a.id in (SELECT s_articles.id from s_articles, s_articles_details as aDetails "
                        . $TmpSQL . " where  aDetails.articleID=s_articles.id) ";
            }
            $sql = str_replace("ON aTax.id=a.taxID", $NewSQL, $sql);
        }

        return $sql;
    }

    /**
     * Lang helper function
     * @param $optionId
     * @param $fallback
     * @return mixed
     */
    private function getOptionTranslation($optionId, $fallback)
    {
        $sql= "SELECT objectdata
               FROM s_core_translations
               WHERE objecttype = ?
               AND objectkey = ?
               AND objectlanguage = ?";

        $data = Shopware()->Db()->fetchOne($sql, array('configuratoroption', $optionId, Shopware()->Shop()->getId()));
        if ($data) {
            return unserialize($data);
        } else {
            return $fallback;
        }
    }

    /**
     * Lang helper function
     * @param $groupId
     * @param $fallback
     * @return mixed
     *
     */
    private function getGroupTranslation($groupId, $fallback)
    {
        $sql= "SELECT objectdata
               FROM s_core_translations
               WHERE objecttype = ?
               AND objectkey = ?
               AND objectlanguage = ?";
        $data = Shopware()->Db()->fetchOne($sql, array('configuratorgroup', $groupId, Shopware()->Shop()->getId()));

        if ($data) {
            return unserialize($data);
        } else {
            return $fallback;
        }
    }


    /**
     * Event
     *
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
            $CurrentCategoryId = $request->sCategory;
            $CategoryArray = explode  (",", $config->categoryids);
            $bFound = false;
            foreach ($CategoryArray as $CategoryId) {
                if ($CategoryId == $CurrentCategoryId)
                {
                  $bFound = true;
                  break;
                }
            }
            if ($bFound == false)
            {
              return;
            }
        }

        $aTmpArray = Array();

        $subCategories = Shopware()->Modules()->Categories()->sGetWholeCategoryTree($request->sCategory);
        foreach ($subCategories as $Tmp) {
            array_push($aTmpArray, $Tmp["id"]);
        }
        array_push($aTmpArray, $request->sCategory);
        $subCategoriesTxt = implode (",", $aTmpArray );

        $AdditionSQL = "";
        $OptionIdArray = explode  ("|", Shopware()->System()->_GET['oid']);
        $sOptionIDs = implode (",", $OptionIdArray);
        if ($sOptionIDs != "")
        {
            $AdditionSQL = "
                SELECT DISTINCT
                  s_articles_details.articleID
                  FROM
                  s_articles_details
                  JOIN s_article_configurator_option_relations
                    ON s_article_configurator_option_relations.article_id = s_articles_details.id
                    AND s_article_configurator_option_relations.option_id IN ($sOptionIDs)
                  JOIN s_articles_categories
                    ON s_articles_details.articleID=s_articles_categories.articleID
                    AND s_articles_categories.categoryID IN ($subCategoriesTxt)
                ";
            $AdditionSQL = "AND s_articles_categories.articleID IN ($AdditionSQL)";
        }

        $sql = "
                 SELECT DISTINCT
                   s_article_configurator_groups.id AS GroupId,
                   s_article_configurator_groups.name AS GroupName,
                   s_article_configurator_options.id AS OptionId,
                   s_article_configurator_options.name AS OptionName
                  FROM
                    s_article_configurator_groups,
                    s_article_configurator_options,
                    s_article_configurator_option_relations,
                    s_articles_categories,
                    s_articles_details
                  WHERE s_article_configurator_groups.id = s_article_configurator_options.group_id
                    AND s_article_configurator_options.id = s_article_configurator_option_relations.option_id
                    AND s_article_configurator_option_relations.article_id = s_articles_details.id
                    AND s_articles_details.articleID=s_articles_categories.articleID
                    AND s_articles_categories.categoryID IN ($subCategoriesTxt)
                    $AdditionSQL
                    ORDER BY s_article_configurator_groups.id, s_article_configurator_options.name
               ";

        $results = Shopware()->Db()->fetchAll($sql, array());
        $LastGroupId = 0;
        $GroupArray = Array();
        $TmpArray = Array();
        $OptionIdHash = Array();
        $OptionIdVar = Shopware()->System()->_GET['oid'];
        $OptionIdArray = explode ("|", $OptionIdVar );
        foreach ($OptionIdArray as $Id) {
          $OptionIdHash[$Id] = true;
        }
        foreach($results as $Row) {
            if ($Row["GroupId"] != $LastGroupId)
            {
              if ($LastGroupId != 0)
              {
                array_push ($GroupArray, $TmpArray);
              }
              $translation = $this->getGroupTranslation($Row["GroupId"], array('name' => $Row["GroupName"]));
              $TmpArray = Array();
              $TmpArray["GroupId"] = $Row["GroupId"];
              $TmpArray["GroupName"] = $translation['name'];
              $TmpArray["SubValueIsActive"] = false;
              $TmpArray["LinkRemoveOption"] = $OptionIdVar;
              $TmpArray["Options"] = array();
              $LastGroupId = $Row["GroupId"];
            }
            $sTmp = $OptionIdVar;
            if ($OptionIdHash[$Row["OptionId"]] != true)
            {
                if ( $OptionIdVar != "")
                {
                    $sTmp .= "|";
                }
                $sTmp .= $Row["OptionId"];
            }
            else
            {
               $TmpArray["SubValueIsActive"] = true;
               $sTmp = $TmpArray["LinkRemoveOption"];
               $sTmp = "|" . $sTmp . "|";
               $sTmp = str_replace("|".$Row["OptionId"]."|", "|", $sTmp);
               $sTmp = trim ($sTmp, "|");
               $sTmp = rtrim ($sTmp, "|");
               $TmpArray["LinkRemoveOption"] = $sTmp;
            }
            $translation = $this->getOptionTranslation($Row["OptionId"], array('name' => $Row["OptionName"]));

            array_push ($TmpArray["Options"],
                array(
                    "Id" => $Row["OptionId"],
                    "IdForURL" => $sTmp,
                    "Name" => $translation['name'],
                    "Active" => $OptionIdHash[$Row["OptionId"]] == true,
                ));
        }
        if ($LastGroupId != 0)
        {
            array_push ($GroupArray, $TmpArray);
        }

        $view = $args->getSubject()->View();
        $config = Shopware()->Plugins()->Frontend()->SwagVariantFilter()->Config();
        $view->SwagVariantFilterConfig = $config;
        $view->GroupArray = $GroupArray;
        $view->BaseURL =  $args->getSubject()->Request()->getBasePath() . $args->getSubject()->Request()->getPathInfo();
        $this->Application()->Template()->addTemplateDir ($this->Path() . 'views/');
        $view->extendsTemplate("frontend/index.tpl");
    }
}