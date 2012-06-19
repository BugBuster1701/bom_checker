<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * PHP version 5
 * @copyright  Glen Langer 2011,2012 
 * @author     BugBuster 
 * @package    BomChecker 
 * @license    LGPL 
 */

/**
 * Class BomChecker
 * 
 * @copyright  Glen Langer 2011,2012 
 * @author     BugBuster 
 * @package    BomChecker
 */
class BomChecker extends BackendModule
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_bomchecker_be';
	
	/**
	 * Current version of the class.
	 */
	const BomChecker_VERSION = '1.2.0';
	
	/**
	 * BOM8
	 */
	const STR_BOM8  = "\xEF\xBB\xBF";
	/**
	 * BOM16 LE
	 */
	const STR_BOM16LE = "\xFF\xFE";
	/**
	 * BOM16 BE
	 */
	const STR_BOM16BE = "\xFE\xFF";
	
	/**
	 * Special Check Directories
	 */
	protected $arrSpecialDirectories = array();
	
	/**
	 * BOM Files
	 *
	 * @var array
	 */
	private $_foundbom = array();
	
	/**
	 * Result for CheckFilesForBOM
	 * Default: 0
	 * Found: 1
	 * Nothing found: 2
	 * SPL not active: 3
	 *
	 * @var integer
	 */
	private $_checkbom = 0;
	
	/**
	 * Selected module
	 *
	 * @var string
	 */
	private $_module = '';
	
	/**
	 * Module saved in session
	 *
	 * @var string
	 * @access private
	 */
	private $_session   = '';
	
	/**
	 * Name of session name
	 */
	const BOMCHECK_SESSION       = 'bomcheckmodule';

	
	/**
	 * Compile the current element
	 */
	protected function compile()
	{
		$this->Template->referer     = $this->getReferer(ENCODE_AMPERSANDS);
		$this->Template->backTitle   = specialchars($GLOBALS['TL_LANG']['MSC']['backBT']);
		$this->Template->Title       = $GLOBALS['TL_LANG']['BomChecker']['title'];
		$this->Template->action      = ampersand($this->Environment->request, true);
		$this->Template->selectAll   = $GLOBALS['TL_LANG']['MSC']['selectAll'];
		
		$this->Template->dirlabel    = $GLOBALS['TL_LANG']['BomChecker']['directories'];
		$this->Template->dirHelp     = $GLOBALS['TL_LANG']['BomChecker']['directory_help'];
		$this->Template->dirSubmit   = specialchars($GLOBALS['TL_LANG']['BomChecker']['submitBT']);
		
		$this->Template->modules     = $GLOBALS['TL_LANG']['BomChecker']['modules'];
		$this->Template->modulesHelp = $GLOBALS['TL_LANG']['BomChecker']['module_help'];
		$this->Template->check_bom   = 0; //overwrite with test result
		$this->Template->theme       = $this->getTheme();
		$this->getSession(); // module from session
		
		$this->Template->DirectorySelection = $this->getSpecials();
		
		if ($this->Input->post('check_dirs') ==1)
		{
			$bomdirs = deserialize($this->Input->post('bomdirs'));
			if (!is_array($bomdirs))
			{
				$this->reload();
			}
			foreach ($bomdirs as $bomdir)
			{
				//check this!
				$this->_checkbom = $this->CheckFilesForBOM(TL_ROOT . '/' . $this->arrSpecialDirectories[$bomdir]);
			}
			$this->Template->check_dir_found = $this->_foundbom;
			$this->Template->check_bom = ($this->_checkbom === true) ? '2' : '1';
		}
		
		if ($this->Input->post('check_module') ==1)
		{
			$this->_module = $this->Input->post('list_modules');
			$this->setSession(); // module in session
			//check this!
			$this->_checkbom = $this->CheckFilesForBOM(TL_ROOT . '/system/modules/' . $this->_module);
			$this->Template->check_module_found = $this->_foundbom;
			$this->Template->check_bom = ($this->_checkbom === true) ? '2' : '1';
		}
		if (!extension_loaded('SPL')) 
		{
			$this->Template->check_bom = 3; 
		}
		$this->Template->ModuleSelection    = $this->getModules();
		
	}
	
	/**
	 * Get active modules as options list
	 *
	 * @return string	options list
	 * @access protected
	 */
	protected function getModules()
	{
		foreach ($this->Config->getActiveModules() as $strModule)
		{
	 		$selected="";
	 		if ($this->_module == $strModule)
	 			$selected=' selected="selected"';
		
			$strAllModules .= sprintf('<option value="%s" %s>%s</option>', $strModule, $selected,$strModule);
		}

		// Show form
		return $strAllModules;
	}
	
	/**
	 * Get special directories
	 *
	 * @return array	Directories
	 * @access protected
	 */
	protected function getSpecials()
	{
	    $this->arrSpecialDirectories['core_plugins']   = 'plugins';
		$this->arrSpecialDirectories['core_templates'] = 'templates';
		$this->arrSpecialDirectories['core_config']    = 'system/config';
		$this->arrSpecialDirectories['core_drivers']   = 'system/drivers';
		$this->arrSpecialDirectories['all_modules']    = 'system/modules';
	
		foreach ($this->arrSpecialDirectories as $k=>$v)
		{
			$arrBomDirs[] = array
			(
				'id' => 'bomdirs_' . $k,
				'value' => $k,
				'name' => $v
			);
		}
		// $GLOBALS['BOMCHECK_DIR'][] = array('files' => 'tl_files');
		/* todo pruefen ob inhalt
		foreach ($GLOBALS['BOMCHECK_DIR'] as $k=>$v)
		{
			foreach ($v as $kk => $vv) {
				
			
				$arrBomDirs[] = array
				(
					'id' => 'bomdirs_' . $k,
					'value' => $kk,
					'name' => $vv
				);
			}
		}*/
		return $arrBomDirs;
	}
	
	/**
	 * Check files for BOM
	 *
	 * @param string $directory	Directory
	 * @return bool	
	 */
	protected function CheckFilesForBOM($directory)
	{
		if (!extension_loaded('SPL')) 
		{
			return true;
		}
		// todo: check ob dir vorhanden
		
		$rit = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory), RecursiveIteratorIterator::CHILD_FIRST);
		try 
		{
			foreach ($rit as $file) 
			{
				if ($file->isFile()) 
				{
					$path_parts = pathinfo($file->getRealPath());
					//print "Check: ".$rit->getFilename() . "\n";
					if (array_key_exists('extension',$path_parts)) 
					{
						if ('php'   == strtolower($path_parts['extension']) 
						 || 'tpl'   == strtolower($path_parts['extension'])
						 || 'xhtml' == strtolower($path_parts['extension'])
						 || 'html5' == strtolower($path_parts['extension'])
						 || 'css'   == strtolower($path_parts['extension'])
						   ) 
						{
							$object = new SplFileObject($file->getRealPath());
							
							$line = $object->getCurrentLine();
							
							if ( ($file->getSize()>2) && (substr( $line, 0, 3 ) == self::STR_BOM8) ) 
							{
								$this->_foundbom[] = "UTF-8 BOM&nbsp;: ".str_replace(TL_ROOT,'',$file->getRealPath());
							} 
							elseif ( ($file->getSize()>1) && (substr( $line, 0, 2 ) == self::STR_BOM16LE) ) 
							{
								$this->_foundbom[] = "UTF-16 BOM: ".str_replace(TL_ROOT,'',$file->getRealPath());
							} 
							elseif ( ($file->getSize()>1) && (substr( $line, 0, 2 ) == self::STR_BOM16BE) ) 
							{
								$this->_foundbom[] = "UTF-16 BOM: ".str_replace(TL_ROOT,'',$file->getRealPath());
							}
						} // if extension php/tpl
					} // if extension
				} // is file
			} // foreach
			if(count($this->_foundbom)) 
			{
				return false;
			}
			return true;
		} 
		catch (Exception $e) 
		{
			die ('Exception caught: '. $e->getMessage());
		}
	} // CheckFilesForBOM
	
	/**
	 * Get session and set _module
	 *
	 * @return void
	 * @access protected
	 */
	protected function getSession()
	{
		$this->_session = $this->Session->get( self::BOMCHECK_SESSION );
		$arrSession = '';
		
		if(!empty($this->_session))
		{
			$arrSession = $this->_session;
			$this->_module = $arrSession[0];
		} 
	}
	
	/**
	 * Set session with _module
	 *
	 * @return void
	 * @access protected
	 */
	protected function setSession()
	{
		$this->Session->set( self::BOMCHECK_SESSION , array($this->_module,'1701') );
	}


} // class
