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

$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('TemplateSelection\TemplateSelection', 'changeTemplate');

?>