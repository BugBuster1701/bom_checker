<?php 

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @link http://www.contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 *
 * PHP version 5
 * @copyright  Glen Langer 2011,2012 
 * @author     BugBuster 
 * @package    BomChecker 
 * @license    LGPL 
 */

/**
 * Back end module
 */
$GLOBALS['BE_MOD']['devtools']['bom_checker'] = array
(
	'callback'   => 'BomChecker\BomChecker',
	'icon'       => 'system/modules/bom_checker/assets/bomcheck.png',
);

