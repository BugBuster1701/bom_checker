<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Bom_checker
 * @link    http://www.contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'BugBuster\BomChecker',
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
