<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

// -PC- J3.0 fix
if( !defined( 'DS' ) ) define( 'DS', DIRECTORY_SEPARATOR );

class com_jckmanInstallerScript
{
	function preflight( $type, $parent ) 
	{
		$jversion = new JVersion();
		// Installing component manifest file version
		$this->release = $parent->get( "manifest" )->version;

		// If fresh install, lang file can't be loaded yet so use the tmp dir one.
		$lang = JFactory::getLanguage();
		$lang->load( 'com_jckman' , dirname(__FILE__) );
		$lang->load( 'com_jckman.sys' , dirname(__FILE__) );

		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;   
	   
		if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) 
		{
			JError::raiseWarning(null, JText::sprintf( 'COM_JCKMAN_CUSTOM_INSTALL_NOT_JOOMLA_PRIOR', $this->minimum_joomla_release ) );
			return false;
		}
		
		//Workaround I shouldn't have to do
		
		$app 	= JFactory::getApplication();
		$db 	= JFactory::getDBO();
		
		$sql = $db->getQuery( true );
		$sql->select( '1' )
			->from('#__extensions')
			->where( '`name` = "com_jckman"' );
		if( !$db->setQuery( $sql )->loadResult() )
		{
			$sql = $db->getQuery( true ); 
			$sql->delete( '#__assets' )
				->where( '`title` like "%JCK%MAN%"' );
			$db->setQuery( $sql )->query();
		}

	}

	function install($parent)
	{
		$version 	= '6.4.4';
		$mainframe 	= JFactory::getApplication();
		$db 		= JFactory::getDBO();

		$query = "SELECT count(1) FROM #__modules"
		." WHERE module = 'mod_jckquickicon'";
		$db->setQuery( $query );
		$count = $db->loadResult();
		if($count)  $this->uninstall($parent);

		jimport('joomla.filesystem.folder');

		$src 	= 'components' .DS. 'com_jckman' .DS. 'modules' .DS. 'mod_jckquickicon';
		$dest 	= 'modules' .DS. 'mod_jckquickicon';

		if( !JFolder::copy( $src, $dest, JPATH_ADMINISTRATOR, true ) ){
			$mainframe->enqueueMessage( JText::sprintf( 'COM_JCKMAN_CUSTOM_INSTALL', 'control panel icon module!' ) );
		}

		/*===========================================================> */
		/*==============================================> LEFT MODULES */
		/*===========================================================> */
		$row 			= JTable::getInstance('module');
		$row->position 	= 'jck_icon';
		$row->published = 1;
		$row->showtitle = 1;
		$row->access 	= 1;
		$row->client_id = 1;
		$row->params 	= '';

		$row->id 		= 0;
		$row->title 	= 'JCK Manager';
		$row->content 	= '<img alt="" src="components/com_jckman/icons/jck-manager-logo.png" />';
		$row->ordering 	= $row->getNextOrder( "position='jck_icon'" );
		$row->module 	= 'mod_custom';
		$row->params 	= '{"prepare_content":0}';
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_INSTALL','JCK PlugMan custom Module data!') );
		}

		$row->id 		= 0;
		$row->title 	= 'Dashboard';
		$row->module 	= 'mod_jckquickicon';
		$row->params 	= '';
		$row->ordering = $row->getNextOrder( "position='jck_icon'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_INSTALL','Control Panel icon Module data!') );
		}

		$row->id 		= 0;
		$row->title 	= 'JCK Manager v' . $version;
		$row->module 	= 'mod_custom';
		$row->content 	= 	
		'<table class="table table-striped" border="0" cellpadding="0" cellspacing="0" style="margin-bottom:0px;">
			<tr>
				<td>Version:</td>
				<td>' . $version . '</td>
			</tr>
			<tr>
				<td>Author:</td>
				<td><a href="http://www.joomlackeditor.com" target="_blank">www.joomlackeditor.com</a></td>
			</tr>
			<tr>
				<td>Copyright:</td>
				<td>&copy; WebxSolution Ltd, All rights reserved.</td>
			</tr>
			<tr>
				<td>License:</td>
				<td>GPLv2.0</td>
			</tr>
			<tr>
				<td>More info:</td>
				<td><a href="http://joomlackeditor.com/terms-of-use" target="_blank">http://joomlackeditor.com/terms-of-use</a></td>
			</tr>
		</table>';
		$row->ordering = $row->getNextOrder( "position='jck_icon'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_INSTALL','JCK PlugMan custom Module data!') );
		}

		/*===========================================================> */
		/*============================================> CPANEL MODULES */
		/*===========================================================> */		
		$row  = JTable::getInstance('module');
		$row->module 	= '';
		$row->position 	= 'jck_cpanel';
		$row->published = 1;
		$row->showtitle = 1;
		$row->access 	= 1;
		$row->client_id = 1;
		$row->params 	= '{}';

		$row->id = 0;
		$row->title = 'COM_JCKMAN_CPANEL_SLIDER_MANAGER_LABEL';
		$row->content = 'COM_JCKMAN_CPANEL_SLIDER_MANAGER_HTML';
		$row->ordering = $row->getNextOrder( "position='jck_cpanel'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_INSTALL','JCK PlugMan custom Module data!') );
		}

		$row->id = 0;
		$row->title = 'COM_JCKMAN_CPANEL_SLIDER_PLUGIN_LABEL';
		$row->content = 'COM_JCKMAN_CPANEL_SLIDER_PLUGIN_HTML';
		$row->ordering = $row->getNextOrder( "position='jck_cpanel'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_INSTALL','JCK PlugMan custom Module data!') );
		}

		$row->id = 0;
		$row->title = 'COM_JCKMAN_CPANEL_SLIDER_SYSTEM_CHECK_LABEL';
		$row->content = 'COM_JCKMAN_CPANEL_SLIDER_SYSTEM_CHECK_HTML';	
		
		$row->ordering = $row->getNextOrder( "position='jck_cpanel'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_INSTALL','JCK PlugMan custom Module data!') );
		}

		$row->id = 0;
		$row->title = 'COM_JCKMAN_CPANEL_SLIDER_SYSTEM_LAYOUT_MANAGER';
		$row->content = 'COM_JCKMAN_CPANEL_SLIDER_SYSTEM_LAYOUT_MANAGER_HTML';	
	
		$row->ordering = $row->getNextOrder( "position='jck_cpanel'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_INSTALL','JCK PlugMan custom Module data!') );
		}	

		$row->id = 0;
		$row->title = 'COM_JCKMAN_CPANEL_SLIDER_BACKUP_LABEL';
		$row->content = 'COM_JCKMAN_CPANEL_SLIDER_BACKUP_LABEL_HTML';	
		;
		$row->ordering = $row->getNextOrder( "position='jck_cpanel'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_INSTAL','custom Module data!') );
		}

		$row->id = 0;
		$row->title = 'COM_JCKMAN_CPANEL_SLIDER_RESTORE_LABEL';
		$row->content = 'COM_JCKMAN_CPANEL_SLIDER_RESTORE_HTML';
		$row->ordering = $row->getNextOrder( "position='jck_cpanel'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_INSTALL','JCK PlugMan custom Module data!') );
		}

		$row->id = 0;
		$row->title = 'COM_JCKMAN_CPANEL_SLIDER_SYNC_LABEL';
		$row->content = 'COM_JCKMAN_CPANEL_SLIDER_SYNC_HTML';
		$row->ordering = $row->getNextOrder( "position='jck_cpanel'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_INSTALL','JCK PlugMan custom Module data!') );
		}

		jimport('joomla.filesystem.file');

		$src 	= JPATH_ADMINISTRATOR.DS.'components' .DS. 'com_jckman' .DS. 'editor' .DS. 'pluginoverrides.php';
		$dest 	= JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'jckeditor'.DS.'includes'.DS.'ckeditor'.DS.'plugins'.DS.'editor'.DS. 'pluginoverrides.php';

		if( !JFile::copy( $src, $dest) ){
			$mainframe->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_INSTALL_UNABLE_TO_MOVE_SPRINTF','pluginoverrides JCK plugin!') );
		}
		
		
		$src 	= JPATH_ADMINISTRATOR.DS.'components' .DS. 'com_jckman' .DS. 'editor' .DS. 'languageoverrides.php';
		$dest 	= JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'jckeditor'.DS.'includes'.DS.'ckeditor'.DS.'plugins'.DS.'editor'.DS. 'languageoverrides.php';

		if( !JFile::copy( $src, $dest) ){
			$mainframe->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_INSTALL_UNABLE_TO_MOVE_SPRINTF','languageoverrides JCK plugin!') );
		}

		$src 	= JPATH_ADMINISTRATOR.DS.'components' .DS. 'com_jckman' .DS. 'editor' .DS. 'acl.php';
		$dest 	= JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'jckeditor'.DS.'includes'.DS.'ckeditor'.DS.'plugins'.DS.'editor'.DS. 'acl.php';

		
		if( !JFile::copy( $src, $dest) ){
			$mainframe->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_INSTALL_UNABLE_TO_MOVE_SPRINTF',' ACL JCK plugin!') );
		}

		$src 	= JPATH_ADMINISTRATOR.DS.'components' .DS. 'com_jckman' .DS. 'editor' .DS. 'components.php';
		$dest 	= JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'jckeditor'.DS.'includes'.DS.'ckeditor'.DS.'plugins'.DS.'toolbar'.DS. 'components.php';

		if( !JFile::copy( $src, $dest) ){
			$mainframe->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_INSTALL_UNABLE_TO_MOVE_SPRINTF','JCK  toolbar plugin!') );
		}

		$src 	= JPATH_ADMINISTRATOR.DS .'components' .DS. 'com_jckman' .DS. 'editor'.DS.'plugins.php';
		$dest 	= JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'jckeditor'.DS.'includes'.DS.'ckeditor'.DS.'plugins.php';

		if( !JFile::copy( $src, $dest) ){
			$mainframe->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_INSTALL_UNABLE_TO_MOVE_SPRINTF','base plugins file to JCK library!') );
		}

		//$src 	= JPATH_ADMINISTRATOR.DS .'components' .DS. 'com_jckman' .DS. 'editor'.DS.'includes.php';
		//$dest 	= JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'includes.php';

		/*
		if( !JFile::copy( $src, $dest) ){
			$mainframe->enqueueMessage( JText::_('Unable to move updated includes file to JCK plugin!') );
		}
		*/
		
		unset( $row );

		//Check System requirements for the editor 
		define('JCK_BASE',JPATH_CONFIGURATION .DS.'plugins'.DS.'editors'.DS.'jckeditor');

		if(!JFolder::exists(JCK_BASE))
		{
			$mainframe->enqueueMessage( JText::_('COM_JCKMAN_CUSTOM_INSTALL_SYSTEM_DETECTED_EDITOR_NOT_INSTALLED') );
			return;
		}

		$perms  = fileperms(JPATH_CONFIGURATION.DS.'index.php');
		$perms = (decoct($perms & 0777));

		$default_fperms = '0644';
		$default_dperms = '0755'; 

		if($perms == 777 || $perms == 666)
		{
			$default_fperms = '0666';
			$default_dperms = '0777'; 
		}

		$fperms = JCK_BASE.DS.'config.js';

		if(!stristr(PHP_OS,'WIN') && JPath::canChmod(JCK_BASE)  && $perms != decoct(fileperms($fperms) & 0777))
		{

			$path = JCK_BASE.DS.'plugins';

			if(!JPath::setPermissions($path,$default_fperms,$default_dperms))
			{
				$mainframe->enqueueMessage( JText::_('COM_JCKMAN_CUSTOM_INSTALL_SYSTEM_DETECTED_INCORRECT_FILE_PERMISSONS_FOR_EDITOR') );
			}
		}
		
		// clear explorer cache to avoid breaking the export feature (filename too long)
		// This code is also run of install/upgrade of the jckexplorer plugin
		$cache 	= JCK_BASE . '/plugins/jckexplorer/cache';

		if( JFolder::exists( $cache ) )
		{
			// Delete cache folders
			foreach( JFolder::folders( $cache ) as $folder )
			{
				JFolder::delete( $cache . DS . $folder );
			}//end foreach

			// Delete cache files
			foreach( JFolder::files( $cache ) as $file )
			{
				if( $file != 'index.html' )
				{
					JFile::delete( $cache . DS . $file );
				}//end if
			}//end foreach
		}//end if

		//for upgrade
		$query = 'SELECT p.name FROM `#__jckplugins` p WHERE p.iscore = 0';
		$db->setQuery( $query );
		$results = $db->loadObjectList();

		if(!empty($results))
		{
			for($i = 0; $i < count($results);$i++)
			{
				if(JFolder::exists(JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'plugins'.DS.$results[$i]->name) && 
					!JFolder::exists(JPATH_ADMINISTRATOR.DS .'components' .DS. 'com_jckman'.DS.'editor'.DS.'plugins'.DS.$results[$i]->name)
				)
				{
					$src 	= JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'plugins'.DS.$results[$i]->name;
					$dest 	= JPATH_ADMINISTRATOR.DS .'components'.DS.'com_jckman'.DS.'editor'.DS.'plugins'.DS.$results[$i]->name;

					if( !JFolder::copy( $src, $dest) )
					{
						$mainframe->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_INSTALL_UNABLE_TO_MOVE_SPRINTF','base plugin .'.$results[$i]->name.' to JCK backup folder!') );
					}
				}
			}//end for loop
		}
		
		//fix remove component install file from the editor's folder
		$file = JPATH_ADMINISTRATOR.DS .'components' .DS. 'com_jckman'.DS.'editor'.DS.'com_jckman.xml';
		if(JFile::exists($file))
			JFile::delete($file);
	
	}
	
	
	
	
	
    function update($parent) 
    {
		$this->install($parent);
		
		$db = JFactory::getDBO();
	
		if(method_exists($parent, 'extension_root')) {
			$sqlfile = $parent->getPath('extension_root').DS.'sql'.DS.'install.sql';
		} else {
			$sqlfile = $parent->getParent()->getPath('extension_root').DS.'sql'.DS.'install.sql';
		}
		
		// Don't modify below this line
		$buffer = file_get_contents($sqlfile);
		
		if ($buffer !== false) {
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
							
			if (count($queries) != 0) {
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->query()) {
							if( !class_exists( 'JCKHelper' ) ) require_once( JPATH_COMPONENT_ADMINISTRATOR . DS . 'helper.php' );

							JCKHelper::error( JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
							return false;
						}
					}
				}
			}
		}
    }
	
	function uninstall($parent)
	{
		$app 	= JFactory::getApplication();
		$db 	= JFactory::getDBO();
		$path 	= JPATH_ADMINISTRATOR .DS. 'modules' .DS. 'mod_jckquickicon';
		jimport('joomla.filesystem.folder');

		if( JFolder::exists( $path ) && !JFolder::delete( $path ) ){
			$app->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_UNINSTALL','control panel icon module!') );
		}

		$sql = $db->getQuery( true );
		$sql->delete( '#__modules' )
			->where( 'position IN ( "jck_cpanel", "jck_icon" )' );
		if( !$db->setQuery( $sql )->query() )
		{
			$app->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_UNINSTALL','PlugMan modules data!') );
		}

		$file = JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'jckeditor'.DS.'includes'.DS.'ckeditor'.DS.'plugins'.DS.'editor'.DS. 'pluginoverrides.php';
		
		if(JFile::exists($file) && !JFile::delete($file)) {
			$app->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_UNINSTALL','pluginoverrides JCK plugin!') );
		}

		$file = JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'jckeditor'.DS.'includes'.DS.'ckeditor'.DS.'plugins'.DS.'editor'.DS. 'languageoverrides.php';
		
		if(JFile::exists($file) && !JFile::delete($file)) {
			$app->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_UNINSTALL','languageoverrides JCK plugin!') );
		}
	
		$file = JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'jckeditor'.DS.'includes'.DS.'ckeditor'.DS.'plugins'.DS.'editor'.DS. 'acl.php';

		if(JFile::exists($file) && !JFile::delete($file)) {
			$app->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_UNINSTALL','ACL JCK plugin!') );
		}

		$file = JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'jckeditor'.DS.'includes'.DS.'ckeditor'.DS.'plugins'.DS.'toolbar'.DS. 'components.php';

		if(JFile::exists($file) && !JFile::delete($file)) {
			$app->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_UNINSTALL','components JCK  toolbar plugin!') );
		}
		
		// For some reason we need to remove the row from the asset table?!
		$sql = $db->getQuery( true );
		$sql->delete( '#__assets' )
			->where( 'name = "com_jckman"' );
		if( !$db->setQuery( $sql )->query() )
		{
			$app->enqueueMessage( JText::sprintf('COM_JCKMAN_CUSTOM_UNINSTALL','Unable to remove JCKMan asset record!') );
		}
		
		$sql = $db->getQuery( true ); 
		$sql->delete( '#__assets' )
			->where( '`title` like "%JCK%MAN%"' );
		$db->setQuery( $sql )->query();
	
	}
}