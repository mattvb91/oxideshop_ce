<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use stdClass;

/**
 * Admin links details manager.
 * Creates form for submitting new admin links or modifying old ones.
 * Admin Menu: Customer Info -> Links.
 */
class AdminlinksMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{

    /**
     * Sets link information data (or leaves empty), returns name of template
     * file "adminlinks_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oLinks = oxNew("oxlinks", getViewName('oxlinks'));
            $oLinks->loadInLang($this->_iEditLang, $soxId);

            $oOtherLang = $oLinks->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oLinks->loadInLang(key($oOtherLang), $soxId);
            }
            $this->_aViewData["edit"] = $oLinks;

            //Disable editing for derived items
            if ($oLinks->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

            // remove already created languages
            $this->_aViewData["posslang"] = array_diff(oxRegistry::getLang()->getLanguageNames(), $oOtherLang);

            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }

        // generate editor
        $this->_aViewData["editor"] = $this->_generateTextEditor(
            "100%",
            255,
            $oLinks,
            "oxlinks__oxurldesc",
            "links.tpl.css"
        );

        return "adminlinks_main.tpl";
    }

    /**
     * Saves information about link (active, date, URL, description, etc.) to DB.
     *
     * @return mixed
     */
    public function save()
    {
        $soxId = $this->getEditObjectId();
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");
        // checkbox handling
        if (!isset($aParams['oxlinks__oxactive'])) {
            $aParams['oxlinks__oxactive'] = 0;
        }

        // adds space to the end of URL description to keep new added links visible
        // if URL description left empty
        if (isset($aParams['oxlinks__oxurldesc']) && strlen($aParams['oxlinks__oxurldesc']) == 0) {
            $aParams['oxlinks__oxurldesc'] .= " ";
        }

        if (!$aParams['oxlinks__oxinsert']) {
            // sets default (?) date format to output
            // else if possible - changes date format to system compatible
            $sDate = date(oxRegistry::getLang()->translateString("simpleDateFormat"));
            if ($sDate == "simpleDateFormat") {
                $aParams['oxlinks__oxinsert'] = date("Y-m-d");
            } else {
                $aParams['oxlinks__oxinsert'] = $sDate;
            }
        }

        $iEditLanguage = oxRegistry::getConfig()->getRequestParameter("editlanguage");
        $oLinks = oxNew("oxlinks", getViewName('oxlinks'));

        if ($soxId != "-1") {
            //$oLinks->load( $soxId );
            $oLinks->loadInLang($iEditLanguage, $soxId);

            //Disable editing for derived items
            if ($oLinks->isDerived()) {
                return;
            }
        } else {
            $aParams['oxlinks__oxid'] = null;
        }

        //$aParams = $oLinks->ConvertNameArray2Idx( $aParams);

        $oLinks->setLanguage(0);
        $oLinks->assign($aParams);
        $oLinks->setLanguage($iEditLanguage);
        $oLinks->save();

        parent::save();

        // set oxid if inserted
        $this->setEditObjectId($oLinks->getId());
    }

    /**
     * Saves link description in different languages (eg. english).
     *
     * @return null
     */
    public function saveinnlang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");
        // checkbox handling
        if (!isset($aParams['oxlinks__oxactive'])) {
            $aParams['oxlinks__oxactive'] = 0;
        }

        $oLinks = oxNew("oxlinks", getViewName('oxlinks'));
        $iEditLanguage = oxRegistry::getConfig()->getRequestParameter("editlanguage");

        if ($soxId != "-1") {
            $oLinks->loadInLang($iEditLanguage, $soxId);
        } else {
            $aParams['oxlinks__oxid'] = null;
            //$aParams = $oLinks->ConvertNameArray2Idx( $aParams);
        }

        //Disable editing for derived items
        if ($oLinks->isDerived()) {
            return;
        }

        $oLinks->setLanguage(0);
        $oLinks->assign($aParams);

        // apply new language
        $oLinks->setLanguage(oxRegistry::getConfig()->getRequestParameter("new_lang"));
        $oLinks->save();

        // set oxid if inserted
        $this->setEditObjectId($oLinks->getId());
    }
}
