<?php

/**
 * Contao Open Source CMS
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package    templateSelection
 * @copyright  MEN AT WORK 2016
 * @author     Andreas Isaak <andy.jared@googlemail.com>
 * @author     David Maack <david.maack@arcor.de>
 * @author     Patrick Kahl <kahl.patrick@googlemail.com>
 * @license    LGPL-3.0+
 */

/**
 * Class templateSelection
 */
class TemplateSelection extends Frontend
{

    protected static $arrThemeCache = array();
    protected static $arrStrFileCache = array();

    /**
     * Function for change the template
     * 
     * @param array $template
     * @return array 
     */
    public function changeTemplate(&$template)
    {

        if (TL_MODE == 'BE')
        {
            return;
        }

        global $objPage;

        $arrTemplateSelection = (self::$arrThemeCache[$objPage->id]) ? self::$arrThemeCache[$objPage->id] : false;

        if ((!$arrTemplateSelection) && ($objPage!=null))
        {

            $arrTmp = $this->inheritSelection($objPage);
            if ($objPage->ts_include_selection_noinherit)
            {
                $arrTmp = array_merge($arrTmp, deserialize($objPage->ts_selection_noinherit, true));
            }

            if (count($arrTmp) > 0)
            {
                self::$arrThemeCache[$objPage->id] = $arrTmp;
            }
            else
            {
                // get the page details
                $objCurrentPage = $this->getPageDetails($objPage->id);

                //get the theme
                $objTheme = $this->Database->prepare("SELECT l.*, t.templates, t.templateSelection FROM tl_layout l LEFT JOIN tl_theme t ON l.pid=t.id WHERE l.id=? OR l.fallback=1 ORDER BY l.id=? DESC")
                        ->limit(1)
                        ->execute($objPage->id, $objPage->id);

                //store templateSelections in cache
                $arrTemplateSelection = deserialize($objTheme->templateSelection);
                self::$arrThemeCache[$objPage->id] = $arrTemplateSelection;
            }
        }

        //check for cached results
        $strFormat = (self::$arrStrFileCache[$objPage->id . '-' . $template->getName()]) ? (self::$arrStrFileCache[$objPage->id . '-' . $template->getName()]) : false;

        //setFormat if format was allready cached
        if ($strFormat)
        {
            $template->setFormat($strFormat);
            return;
        }

        //get agent
        $objUa = $this->Environment->agent;
        if (!is_array($arrTemplateSelection))
            return;

        $blnGlobalPermisson = false;
        foreach ($arrTemplateSelection as $arrSelector)
        {

            $arrSelector['ts_client_os'] = ($arrSelector['ts_client_os'] != '') ? array('value' => $arrSelector['ts_client_os'], 'config' => $GLOBALS['TL_CONFIG']['os'][$arrSelector['ts_client_os']]) : false;
            $arrSelector['ts_client_browser']   = ($arrSelector['ts_client_browser'] != '') ? $GLOBALS['TL_CONFIG']['browser'][$arrSelector['ts_client_browser']] : false;
            $arrSelector['ts_client_is_mobile'] = (($arrSelector['ts_client_is_mobile'] != '') ? (($arrSelector['ts_client_is_mobile'] == 1) ? true : false) : 'empty');

            $blnPermisson = true;
            foreach ($arrSelector as $strConfig => $mixedConfig)
            {
                switch ($strConfig)
                {
                    case 'ts_client_os':
                        $blnPermisson = ($blnPermisson && AgentSelection::checkOsPermission($mixedConfig, $objUa));
                        break;

                    case 'ts_client_browser':
                        $blnPermisson = ($blnPermisson && ($mixedConfig['browser'] == $objUa->browser || $mixedConfig['browser'] == '')) ? true : false;
                        break;

                    case 'ts_client_browser_version':
                        $blnPermisson = ($blnPermisson && AgentSelection::checkBrowserVerPermission($mixedConfig, $objUa, $arrSelector['ts_client_browser_operation']));
                        break;

                    case 'ts_client_is_mobile':
                        if (strlen($mixedConfig) < 2)
                        {
                            $blnPermisson = ($blnPermisson && $mixedConfig == $objUa->mobile) ? true : false;
                        }
                        break;

                    case 'ts_client_is_invert':
                        if ($mixedConfig)
                        {
                            $blnPermisson = ($blnPermisson) ? false : true;
                        }
                        break;
                }
            }

            if ($blnPermisson)
            {
                $this->extendTemplate($template, $arrSelector['ts_extension']);
                return;
            }
        }
    }

    /**
     * Extend the template subfolders
     * 
     * @param array $template
     * @param array $ext 
     */
    private function extendTemplate(&$template, $ext)
    {
        global $objPage;

        $strTemplateGroup = str_replace(array('../', 'templates/'), '', $objPage->templateGroup);

        if ($strTemplateGroup != '')
        {
            $strFile = $strTemplateGroup . '/' . $template->getName();
            //check folder
            if (file_exists(TL_ROOT . '/templates/' . $strFile . '.' . $ext))
            {
                //setFormat
                $template->setFormat($ext);
                //store format in cache
                self::$arrStrFileCache[$this->Environment->__get('request') . '-' . $template->getName()] = $ext;

                return;
            }
        }

        //check folder
        if (file_exists(TL_ROOT . '/templates/' . $template->getName() . '.' . $ext))
        {
            //setFormat
            $template->setFormat($ext);
            //store format in cache
            self::$arrStrFileCache[$this->Environment->__get('request') . '-' . $template->getName()] = $ext;
        }
    }

    /**
     * Check and delete the first dot
     * 
     * @param string $strValue
     * @param DataContainer $dc
     * @return string 
     */
    public function checkFirstDot($strValue)
    {
        return strncmp($strValue, '.', 1) == 0 ? substr($strValue, 1) : $strValue;
    }

    /**
     * Inherit selections from pages.
     *
     * @param Database_Result $objPage
     */
    public function inheritSelection(Database_Result $objPage)
    {

        if ($objPage->ts_include_selection)
        {
            $arrTemp = deserialize($objPage->ts_selection, true);
        }
        else
        {
            $arrTemp = array();
        }

        if ($objPage->pid > 0)
        {
            $objParentPage = $this->Database
                    ->prepare("SELECT * FROM tl_page WHERE id=?")
                    ->execute($objPage->pid);

            if ($objParentPage->next())
            {
                $arrTemp = array_merge($arrTemp, $this->inheritSelection($objParentPage));
            }
        }
        return $arrTemp;
    }

}

?>