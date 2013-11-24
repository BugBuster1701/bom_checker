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
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'BugBuster',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Modules
	'BugBuster\BomChecker\BomChecker' => 'system/modules/bom_checker/modules/BomChecker.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_bomchecker_be' => 'system/modules/bom_checker/templates',
));
