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
 * Palettes
 */
foreach (array('regular', 'root') as $strType)
{
    $GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'ts_include_selection';
    $GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'ts_include_selection_noinherit';

    $GLOBALS['TL_DCA']['tl_page']['palettes'][$strType] = str_replace(
            '{cache_legend:hide}', '{template_selection_legend:hide},ts_include_selection,ts_include_selection_noinherit;{cache_legend:hide}', $GLOBALS['TL_DCA']['tl_page']['palettes'][$strType]
    );
}
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['ts_include_selection']           = 'ts_selection';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['ts_include_selection_noinherit'] = 'ts_selection_noinherit';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['ts_selection'] = array
    (
    'label' => &$GLOBALS['TL_LANG']['tl_page']['templateSelection'],
    'exclude' => true,
    'inputType' => 'multiColumnWizard',
    'eval' => array
        (
        'columnFields' => array
            (
            'ts_client_os' => array
                (
                'label' => &$GLOBALS['TL_LANG']['tl_page']['ts_client_os'],
                'exclude' => true,
                'inputType' => 'select',
                'options_callback' => array(
                    'AgentSelection',
                    'getClientOs'
                ),
                'eval' => array(
                    'style' => 'width:125px',
                    'includeBlankOption' => true
                )
            ),
            'ts_client_browser' => array
                (
                'label' => &$GLOBALS['TL_LANG']['tl_page']['ts_client_browser'],
                'exclude' => true,
                'inputType' => 'select',
                'options_callback' => array('AgentSelection', 'getClientBrowser'),
                'eval' => array(
                    'style' => 'width:125px',
                    'includeBlankOption' => true
                )
            ),
            'ts_client_browser_operation' => array
                (
                'label' => &$GLOBALS['TL_LANG']['tl_page']['ts_client_browser_operation'],
                'inputType' => 'select',
                'options' => array(
                    'lt' => '<',
                    'lte' => '<=',
                    'gte' => '>=',
                    'gt' => '>'
                ),
                'eval' => array(
                    'style' => 'width:70px',
                    'includeBlankOption' => true
                )
            ),
            'ts_client_browser_version' => array
                (
                'label' => &$GLOBALS['TL_LANG']['tl_page']['ts_client_browser_version'],
                'inputType' => 'text',
                'eval' => array(
                    'style' => 'width:55px'
                )
            ),
            'ts_client_is_mobile' => array
                (
                'label' => &$GLOBALS['TL_LANG']['tl_page']['ts_client_is_mobile'],
                'exclude' => true,
                'inputType' => 'select',
                'options' => array(
                    '1' => $GLOBALS['TL_LANG']['MSC']['yes'],
                    '2' => $GLOBALS['TL_LANG']['MSC']['no']
                ),
                'eval' => array(
                    'includeBlankOption' => true
                )
            ),
            'ts_client_is_invert' => array
                (
                'label' => &$GLOBALS['TL_LANG']['tl_page']['ts_client_is_invert'],
                'exclude' => true,
                'inputType' => 'checkbox',
                'eval' => array(
                    'style' => 'width:60px'
                )
            ),
            'ts_extension' => array
                (
                'label' => &$GLOBALS['TL_LANG']['tl_page']['ts_extension'],
                'inputType' => 'text',
                'eval' => array('style' => 'width:75px'),
                'save_callback' => array(
                    array('TemplateSelection', 'checkFirstDot')
                )
            ),
        )
    )
);

// Copy and relabel field
$GLOBALS['TL_DCA']['tl_page']['fields']['ts_selection_noinherit'] = $GLOBALS['TL_DCA']['tl_page']['fields']['ts_selection'];


$GLOBALS['TL_DCA']['tl_page']['fields']['ts_include_selection'] = array
    (
    'label' => &$GLOBALS['TL_LANG']['tl_page']['ts_include_selection'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array('submitOnChange' => true)
);

$GLOBALS['TL_DCA']['tl_page']['fields']['ts_include_selection_noinherit'] = array
    (
    'label' => &$GLOBALS['TL_LANG']['tl_page']['ts_include_selection_noinherit'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array('submitOnChange' => true)
);
?>