<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 
 
/*
* Modified for use as the Jplugin installer
* AW
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
defined( '_JEXEC' ) or die();

define( 'JCK_PATH', JPATH_PLUGINS.DS.'editors'.DS.'jckeditor' );
define( 'JCK_PLUGINS', JCK_PATH.DS.'plugins' );

require_once( JPATH_COMPONENT .DS. 'tables' .DS. 'plugin.php' );
require_once(CKEDITOR_LIBRARY.DS . 'toolbar.php');

jckimport('helper');

/**
 * Plugin installer
 *
 * @package		Joomla.Framework
 * @subpackage	Installer
 * @since		1.5
 * Renamed JInstallerPlugin to JCKInstallerPlugin
 */
class JCKInstallerPlugin extends JObject
{
	function __construct(&$parent)
	{
		$this->parent = $parent;
	}

	/**
	 * Custom install method
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 * Minor alteration - see below
	 */
	function install()
	{
		// Get a database connector object
		$db =& $this->parent->getDBO();
		$app = JFactory::getApplication();

		// Get the extension manifest object
		$manifest =& $this->parent->getManifest();
		$this->manifest =  $manifest;//$manifest->document;
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Manifest Document Setup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Set the component name
		$name = '';
				
		if($this->manifest->name)
		{
			$name = $this->manifest->name;
			$this->parent->set('name', $name->data());
		}
		else
			$this->parent->set('name','');
		
		// Get the component description
		$description = & $this->manifest->description;
		if (is_a($description, 'JXMLElement')) {
			$this->parent->set('message', $description->data());
		} else {
			$this->parent->set('message', '' );
		}
		
		$element =& $this->manifest->files;

		// Plugin name is specified
		$pname  = (string) $this->manifest->attributes()->plugin;
		
		//Get type
		$type  = $this->manifest->attributes()->group;
		
		if (!empty ($pname)) {
			// ^ Use JCK_PLUGINS defined path
			$this->parent->setPath('extension_root', JCK_PLUGINS .DS. $pname);
		} else {
			$this->parent->abort('Extension Install: '.JText::_('COM_JCKMAN_ADAPTER_NO_PLUGIN_SPECIFIED'));
			return false;
		}
		
		if ((string)$manifest->scriptfile)
		{
			$manifestScript = (string)$manifest->scriptfile;
			$manifestScriptFile = $this->parent->getPath('source').DS.$manifestScript;
			if (is_file($manifestScriptFile))
			{
				// load the file
				include_once $manifestScriptFile;
			}
			// Set the class name
			$classname = 'plgJCK'.$pname.'InstallerScript';
			
			if (class_exists($classname))
			{
				// create a new instance
				$this->parent->manifestClass = new $classname($this);
				// and set this so we can copy it later
				$this->set('manifest_script', $manifestScript);
				// Note: if we don't find the class, don't bother to copy the file
			}
				
			// run preflight if possible 
			ob_start();
			ob_implicit_flush(false);
			if ($this->parent->manifestClass && method_exists($this->parent->manifestClass,'preflight'))
			{
				if($this->parent->manifestClass->preflight('install', $this) === false)
				{
					// Install failed, rollback changes
					$this->parent->abort(JText::_('COM_JCKMAN_ADAPTER_CUSTOM_ABORT'));
					return false;
				}
			}
			ob_end_clean();
		}
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Filesystem Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// If the extension directory does not exist, lets create it
		$created = false;
		if (!file_exists($this->parent->getPath('extension_root'))) {
			if (!$created = JFolder::create($this->parent->getPath('extension_root'))) {
				$this->parent->abort('Plugin Install: '.JText::_('Failed to create directory').': "'.$this->parent->getPath('extension_root').'"');
				return false;
			}
		}

		/*
		 * If we created the extension directory and will want to remove it if we
		 * have to roll back the installation, lets add it to the installation
		 * step stack
		 */
		if ($created) {
			$this->parent->pushStep(array ('type' => 'folder', 'path' => $this->parent->getPath('extension_root')));
		}

		// Copy all necessary files
		if ($this->parent->parseFiles($element, -1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}
		
		if ($this->_parseLanguages( $this->manifest->languages) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}
		
		// If there is an install file, lets copy it.
		$installScriptElement =& $this->manifest->installfile;
		if (is_a($installScriptElement, 'JXMLElement')) {
			// Make sure it hasn't already been copied (this would be an error in the xml install file)
			if (!file_exists($this->parent->getPath('extension_root').DS.$installScriptElement->data()))
			{
				$path['src']	= $this->parent->getPath('source').DS.$installScriptElement->data();
				$path['dest']	= $this->parent->getPath('extension_root').DS.$installScriptElement->data();
				if (!$this->parent->copyFiles(array ($path))) {
					// Install failed, rollback changes
					$this->parent->abort(JText::_('COM_JCKMAN_ADAPTER_COMPONENT').' '.JText::_('COM_JCKMAN_ADAPTER_INSTALL').': '.JText::_('COM_JCKMAN_ADAPTER_COULD_NOT_COPY_INSTALL_FILE'));
					return false;
				}
			}
			$this->set('install.script', $installScriptElement->data());
		}

		// If there is an uninstall file, lets copy it.
		$uninstallScriptElement =& $this->manifest->uninstallfile;
		if (is_a($uninstallScriptElement, 'JXMLElement')) {
			// Make sure it hasn't already been copied (this would be an error in the xml install file)
			if (!file_exists($this->parent->getPath('extension_root').DS.$uninstallScriptElement->data()))
			{
				$path['src']	= $this->parent->getPath('source').DS.$uninstallScriptElpement->data();
				$path['dest']	= $this->parent->getPath('extension_root').DS.$uninstallScriptElement->data();
				if (!$this->parent->copyFiles(array ($path))) {
					// Install failed, rollback changes
					$this->parent->abort(JText::_('COM_JCKMAN_ADAPTER_COMPONENT').' '.JText::_('COM_JCKMAN_ADAPTER_INSTALL').': '.JText::_('COM_JCKMAN_ADAPTER_COULD_NOT_COPY_INSTALL_FILE'));
					return false;
				}
			}
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Database Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */
		$type = (isset($type) ? (string)$type : 'plugin'); //Add group processimg 
		
		// Check to see if a plugin by the same name is already installed
		// ^ Altered db query for #__JCK_PLUGINS
		$query = 'SELECT `id`' .
				' FROM `#__jckplugins`' .
				' WHERE name = '.$db->Quote($pname);
		$db->setQuery($query);
		if (!$db->Query()) {
			// Install failed, roll back changes
			$this->parent->abort('Plugin Install: '.$db->stderr(true));
			return false;
		}
		$id = $db->loadResult();

		// Was there a module already installed with the same name?
		if($id)
		{

			if (!$this->parent->isOverwrite())
			{
				// Install failed, roll back changes
                
				$this->parent->abort(JText::sprintf('COM_JCKMAN_ADAPTER_PLUGIN_ALREADY_EXISTS',$pname));
				return false;
			}

			$row =& JTable::getInstance('plugin', 'JCKTable');
			$row->type = $type;
			$row->load($id);

		}
		else
		{
			$icon 				= $this->manifest->icon;

			// ^ Changes to plugin parameters. Use JCK Plugins Table class. 
			$row =& JTable::getInstance('plugin', 'JCKTable');
			$row->title 		= $this->parent->get('name');
			$row->name			= $pname;
			$row->type 			= $type;
			$row->row	 		= 4;
			$row->published 	= 1;
			$row->editable 		= 1;
			$row->icon 			= ($icon ? $icon->data() : '');
			$row->iscore 		= 0;
			$row->params 		= $this->parent->getParams();

			if($this->manifest->attributes()->parent)
			{
				$parentName = (string) $this->manifest->attributes()->parent;
				$row->setParent($parentName);
			}

			if (!$row->store()) {
				// Install failed, roll back changes
				$this->parent->abort(JText::sprintf('COM_JCKMAN_ADAPTER_PLUGIN_INSTALL',$db->stderr(true)));
				return false;
			}

			// Since we have created a plugin item, we add it to the installation step stack
			// so that if we have to rollback the changes we can undo it.
			$this->parent->pushStep(array ('type' => 'plugin', 'id' => $row->id));
		}

		/* -------------------------------------------------------------------------------------------
		 * update editor plugin config file    AW 
		 * -------------------------------------------------------------------------------------------
		*/ 
		$config = &	JCKHelper::getEditorPluginConfig();

		$config->set($pname,1);
        

		$cfgFile = CKEDITOR_LIBRARY.DS . 'plugins' . DS . 'toolbarplugins.php'; 

		// Get the config registry in PHP class format and write it to configuation.php
		if (!JFile::write($cfgFile, $config->toString('PHP',array('class' => 'JCKToolbarPlugins extends JCKPlugins'))))
		{ 	  
			JCKHelper::error(JText::sprintf('COM_JCKMAN_ADAPTER_FAILED_PUBLISH_PLUGIN',$pname));
		} 	  

	 	/**
		 *-------------------------------------------------------------------------------------------
		 * Add plugin to toolbars
		 *-------------------------------------------------------------------------------------------
		 */
		$CKfolder 	=  CKEDITOR_LIBRARY.DS . 'toolbar'; 
	
		$toolbars 	= JCKHelper::getEditorToolbars();
		$jform		= $app->input->get( 'jform', array(), 'array' );

		switch( $jform['toolbars'] )
		{
			default :
			case 'all' :
				$toolbarnames = $toolbars;
				break;
			case 'none' :
				$toolbarnames = array();
				break;
			case 'select' :
				$toolbarnames = $jform['selections'];
				break;
		}//end switch

   		if(!empty( $toolbarnames) && $row->icon)
		{
	           
            $values = array();
			foreach($toolbarnames as $toolbarname)
			{
	               
                $tmpfilename = $CKfolder.DS.$toolbarname.'.php';

				require($tmpfilename);

				$classname = 'JCK'. ucfirst($toolbarname);

				$toolbar = new $classname();

				$pluginTitle = str_replace(' ','',$row->title);


				if(isset($toolbar->pluginTitle)) continue;

				//fix toolbar values or they will get wiped out
				foreach (get_object_vars( $toolbar ) as $k => $v)
				{

					if(is_null($v))
					{
						$toolbar->$k = ''; 
					}

					if($k[0] == '_')
						$toolbar->$k = NULL;
				}

				$toolbar->$pluginTitle = '';

            

				$toolbarConfig = new JRegistry('toolbar');
				$toolbarConfig->loadObject($toolbar);		

				// Get the config registry in PHP class format and write it to configuation.php
				if (!JFile::write($tmpfilename, $toolbarConfig->toString('PHP', array('class' => $classname . ' extends JCKToolbar'))))
				{ 	  
					JCKHelper::error(JText::sprintf('COM_JCKMAN_ADAPTER_FAILED_TO_ADD_PLUGIN_TOOLBAR',$pname,$classname));
				} 	  

				//layout stuff
				$sql = $db->getQuery( true );
				$sql->select( 'id' )
					->from( '#__jcktoolbars' )
					->where( 'name = "'. $toolbarname .'"' );
				$toolbarid = $db->setQuery( $sql )->loadResult();

				$rowDetail = JCKHelper::getNextLayoutRow($toolbarid);

				$values[] = '('.(int)$toolbarid.','. $row->id.','.$rowDetail->rowid.','.$rowDetail->rowordering.',1)';
			}

			//insert into layout table
			if(!empty($values))
			{
				//Now delete dependencies
				$query = 'DELETE FROM #__jcktoolbarplugins'
					. ' WHERE pluginid ='. $row->id;
				$db->setQuery( $query );
				if (!$db->query()) {
					JCKHelper::error( $db->getErrorMsg() );
				}

				$query = 'INSERT INTO `#__jcktoolbarplugins` (toolbarid,pluginid,row,ordering,state) VALUES ' . implode(',',$values);
				$db->setQuery( $query );
				if(!$db->query()) 
				{
					JCKHelper::error( $db->getErrorMsg() );
				}
			}
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Finalization and Cleanup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Lastly, we will copy the manifest file to its appropriate place.
		if (!$this->parent->copyManifest(-1)) {
			// Install failed, rollback changes
			$this->parent->abort('Plugin Install: '.JText::_('Could not copy setup file'));
			return false;
		}

		//make a copy of the plugin
		$src = 	$this->parent->getPath('extension_root');
		$dest = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jckman'.DS.'editor'.DS.'plugins'.DS.$pname;
		
		if (!JFolder::copy( $src, $dest,null,true)) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		// And now we run the postflight
		ob_start();
		ob_implicit_flush(false);

		if ($this->parent->manifestClass && method_exists($this->parent->manifestClass,'postflight'))
		{
			$this->parent->manifestClass->postflight('install', $this);
		}
		ob_end_clean();

		return true;
	}

	function _parseLanguages($element)
	{
		// Get the array of file nodes to process; we checked whether this had children above.
		
		if (!$element || !count($element->children()))
		{
			// Either the tag does not exist or has no children (hence no files to process) therefore we return zero files processed.
			return 0;
		}

		$copyfiles = array();

		$destination =  JPATH_COMPONENT.'/language';
		
		/*
		 * Here we set the folder we are going to copy the files from.
		 *
		 * Does the element have a folder attribute?
		 *
		 * If so this indicates that the files are in a subdirectory of the source
		 * folder and we should append the folder attribute to the source path when
		 * copying files.
		 */

	
		
		$folder = (string) $element->attributes()->folder;

		if ($folder && file_exists($this->parent->getPath('source') . '/' . $folder))
		{
			$source = $this->parent->getPath('source') . '/' . $folder;
		}
		else
		{
			$source = $this->parent->getPath('source');
		}
	
		$path = array();


		// Process each file in the $files array (children of $tagName).
		foreach ($element->children() as $file)
		{
			/*
			 * Language files go in a subfolder based on the language code, ie.
			 * <language tag="en-US">en-US.mycomponent.ini</language>
			 * would go in the en-US subdirectory of the language folder.
			 */

			// We will only install language files where a core language pack
			// already exists.

			if ((string) $file->attributes()->tag != '')
			{
				$path['src'] = $source . '/' . $file;
				$path['dest'] = $destination . '/' . $file->attributes()->tag . '/' . basename((string) $file);
			}
			else
			{
				$path['src'] = $source . '/' . $file;
				$path['dest'] = $destination . '/' . $file;
			}

			/*
			 * Before we can add a file to the copyfiles array we need to ensure
			 * that the folder we are copying our file to exits and if it doesn't,
			 * we need to create it.
			 */

			if (basename($path['dest']) != $path['dest'])
			{
				$newdir = dirname($path['dest']);

				if (!JFolder::create($newdir))
				{
					JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_CREATE_DIRECTORY', $newdir), JLog::WARNING, 'jerror');

					return false;
				}
			}

			// Add the file to the copyfiles array
			$copyfiles[] = $path;
		}

		return $this->parent->copyFiles($copyfiles);
	}
	
	
	/**
	 * Custom uninstall method
	 *
	 * @access	public
	 * @param	int		$cid	The id of the plugin to uninstall
	 * @param	int		$clientId	The id of the client (unused)
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function uninstall($id)
	{
		// Initialize variables
		$row	= null;
		$retval = true;
		$db		=& $this->parent->getDBO();

		// First order of business will be to load the module object table from the database.
		// This should give us the necessary information to proceed.

		// ^ Changes to plugin parameters. Use JCK Plugins Table class. 
		$row =& JTable::getInstance('plugin', 'JCKTable');
		$row->load((int) $id);

		// Is the plugin we are trying to uninstall a core one?
		// Because that is not a good idea...
		if ($row->iscore) {
			JCKHelper::error( 'Plugin Uninstall: '.JText::sprintf('WARNCOREPLUGIN', $row->title)."<br />".JText::_('WARNCOREPLUGIN2'));
			return false;
		}

		// Get the plugin folder so we can properly build the plugin path
		if (trim($row->name) == '') {
			JCKHelper::error( JText::_('COM_JCKMAN_ADAPTER_PLUGIN_FIELD_EMPTY'));
			return false;
		}

		//Now delete dependencies
		$sql = $db->getQuery( true );
		$sql->delete( '#__jcktoolbarplugins' )
			->where( 'pluginid ='. $row->id );

		if (!$db->setQuery( $sql )->query())
		{
			JCKHelper::error( $db->getErrorMsg() );
		}

		// Set the plugin root path
		$this->parent->setPath('extension_root', JCK_PLUGINS . DS . $row->name);

		$manifestFile = $this->parent->getPath('extension_root') . DS . $row->name . '.xml';

		if (file_exists($manifestFile))
		{
			// If we cannot load the xml file return null
			if (!($xml = JFactory::getXML($manifestFile))) {
				JCKHelper::error(JText::_('COM_JCKMAN_ADAPTER_UNINSTALL_COULD_NOT_LOAD_MANIFEST'));
				return false;
			}

			$pname = (string) $xml->attributes()->plugin;
			
			if ((string)$xml->scriptfile)
			{
				$manifestScript = (string)$xml->scriptfile;
				$manifestScriptFile = $this->parent->getPath('extension_root').DS.$manifestScript;
				if (is_file($manifestScriptFile))
				{
					// load the file
					include_once $manifestScriptFile;
				}
				// Set the class name
				$classname = 'plgJCK'.$pname.'InstallerScript';

				if (class_exists($classname))
				{
					// create a new instance
					$this->parent->manifestClass = new $classname($this);
					// and set this so we can copy it later
					$this->set('manifest_script', $manifestScript);
					// Note: if we don't find the class, don't bother to copy the file
				}

				// run preflight if possible 
				ob_start();
				ob_implicit_flush(false);
				if ($this->parent->manifestClass && method_exists($this->parent->manifestClass,'preflight'))
				{
					if($this->parent->manifestClass->preflight('uninstall', $this) === false)
					{
						// Install failed, rollback changes
						$this->parent->abort(JText::_('COM_JCKMAN_ADAPTER_CUSTOM_ABORT'));
						return false;
					}
				}
				ob_end_clean();

				ob_start();
				ob_implicit_flush(false);
				if ($this->parent->manifestClass && method_exists($this->parent->manifestClass,'uninstall'))
				{
					$this->parent->manifestClass->uninstall($this);
				}
				$msg = ob_get_contents(); // append messages
				ob_end_clean();
			}

			/**  
			 *
			 * Remove plugin from toolbars file  AW
			 *
			 */
			$CKfolder =  CKEDITOR_LIBRARY.DS . 'toolbar'; 

			$toolbarnames =& JCKHelper::getEditorToolbars();

			foreach($toolbarnames as $toolbarname)
			{
				$tmpfilename = $CKfolder.DS.$toolbarname.'.php';

				require_once($tmpfilename);

				$classname 	= 'JCK'. ucfirst($toolbarname);
				$toolbar 	= new $classname();

				$pluginTitle =  str_replace(' ','',$row->title);
				$pluginTitle = ucfirst($pluginTitle);
				if(!isset($toolbar->$pluginTitle)) continue;
				//fix toolbar values or they will get wiped out

				foreach (get_object_vars( $toolbar ) as $k => $v)
				{
					if(is_null($v))
					{
						$toolbar->$k = ''; 
					}

					if($k[0] == '_')
					$toolbar->$k = NULL;
				}

				$toolbar->$pluginTitle = NULL;

				$toolbarConfig = new JRegistry('toolbar');

				$toolbarConfig->loadObject($toolbar);		

				// Get the config registry in PHP class format and write it to configuation.php
				if (!JFile::write($tmpfilename, $toolbarConfig->toString('PHP', array('class' => $classname . ' extends JCKToolbar'))))
				{
					JCKHelper::error(	JText::sprintf('COM_JCKMAN_ADAPTER_FAILED_TO_REMOVE_PLUGIN_TOOLBAR',$row->name,$classname));
				}
			}

			 /**
			  *
			  * Remove plugin from config file  AW
			  *
			  */
			$config = &	JCKHelper::getEditorPluginConfig();

			$config->set($row->name,NULL); // remove value from output

			$cfgFile = CKEDITOR_LIBRARY.DS . 'plugins' . DS . 'toolbarplugins.php'; 

			// Get the config registry in PHP class format and write it to configuation.php
			if (!JFile::write($cfgFile, $config->toString('PHP', array('class' => 'JCKToolbarPlugins extends JCKPlugins'))))
			{
				JCKHelper::error(JText::sprintf('COM_JCKMAN_ADAPTER_FAILED_TO_REMOVE_PLUGIN_FROM_CONFIG',$pname));
			}

			$root =& $xml;

			if ($root->getName() != 'extension' && $root->getName() != 'install') {
				JCKHelper::error( JText::_('COM_JCKMAN_ADAPTER_UNINSTALL_INVALID_MANIFEST'));
				return false;
			}

			// Remove the plugin files
			$this->parent->removeFiles($root->files, -1);
			JFile::delete($manifestFile);

			// Remove all media and languages as well
			$this->parent->removeFiles($root->languages, 0);

		} else {
			JCKHelper::error( Jtext::_('COM_JCKMAN_ADAPTER_UNINSTALL_INVALID_MANIFEST_OR_NOT_FOUND'));

			$row->delete($row->id);
			unset ($row);
			$retval = false;
		}

		if( $row )
		{
			// Now we will no longer need the plugin object, so lets delete it
			$row->delete($row->id);
			unset ($row);
		}

		// If the folder is empty, let's delete it
		$files = JFolder::files($this->parent->getPath('extension_root'));
		if (!count($files)) {
			JFolder::delete($this->parent->getPath('extension_root'));
		}

		//Now delete copy of plugin stored in the component
		$copyPath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jckman'.DS.'editor'.DS.'plugins'.DS.$pname;
		JFolder::delete($copyPath);

		return $retval;
	}

	/**
	 * Custom rollback method
	 * 	- Roll back the plugin item
	 *
	 * @access	public
	 * @param	array	$arg	Installation step to rollback
	 * @return	boolean	True on success
	 * @since	1.5
	 * Minor changes to the db query
	 */
	function _rollback_plugin($arg)
	{
		// Get database connector object
		$db 	=& $this->parent->getDBO();
		$sql 	= $db->getQuery( true );

		// Remove the entry from the #__JCK_PLUGINS table
		$sql->delete( '`#__jckplugins`' )
			->where( 'id='.(int)$arg['id'] );

		return ($db->setQuery($sql)->query() !== false);
	}
}