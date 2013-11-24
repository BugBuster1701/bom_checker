<?php 

/**
 * Contao Open Source CMS, Copyright (C) 2005-2013 Leo Feyer
 *
 * Modul BomChecker 
 *
 * @copyright  Glen Langer 2007..2013 <http://www.contao.glen-langer.de> 
 * @author     Glen Langer (BugBuster) 
 * @package    BomChecker 
 * @license    LGPL 
 * @see        https://github.com/BugBuster1701/bom_checker
 */

/**
 * Back end module
 */
$GLOBALS['BE_MOD']['devtools']['bom_checker'] = array
(
	'callback'   => 'BomChecker\BomChecker',
	'icon'       => 'system/modules/bom_checker/assets/bomcheck.png',
);

