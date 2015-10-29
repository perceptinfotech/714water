<?php

/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('JPATH_BASE') or die('RESTRICTED');

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
defined( '_JEXEC' ) or die();

/**
 * Language installer
 *
 * @package		JCK
 * @subpackage	Installer
 * @since		1.5
 */
class JCKInstallerLanguage extends JObject {

    /**
     * Constructor
     *
     * @param	object	$parent	Parent object [JInstaller instance]
     * @return	void
     */
    function __construct(& $parent) 
    {
        $this->parent = $parent;
    }

    /**
     * Install method
     *
     * @access	public
     * @return	boolean	True on success
     */
    public function install() 
    {
     

        // Get the extension manifest object
	    $this->manifest = $this->parent->getManifest();
	
         $values = array('name', 'version', 'description', 'tag');
    		 
    	 foreach($values as $value) 
	     {
    	 	    $this->parent->set($value, $this->manifest->$value);
    	 }
    	 
    	     $elements = array('administration', 'editor');
    	 
    	foreach($elements as $element) 
	    {
	  	    $this->set($element, $this->manifest->$element);
    	}

        // Check language tag - if we didn't, we may be trying to install from an older language package
        if (!$this->parent->get('tag'))
        {
		    $this->parent->abort(JText::sprintf('JLIB_INSTALLER_ABORT', JText::_('JLIB_INSTALLER_ERROR_NO_LANGUAGE_TAG')));
		    return false;
        }

        $folder =(string)  $this->parent->get('tag');
        // Set the installation target paths
        $this->parent->setPath('extension_administrator', JPATH_COMPONENT . '/language/overrides');

        // Copy admin files
        if ($this->parent->parseFiles($this->get('administration'), 1) === false) 
        {
            $this->parent->abort();
            return false;
        }
        
        // Copy editor files
        $this->parent->setPath('extension_administrator', JPATH_COMPONENT . '/editor/lang');

        if ($this->parent->parseFiles($this->get('editor'),1) === false) 
        {
            $this->parent->abort();
            return false;
        }

  

	    // Add an entry to the language table 
	    $row = JTable::getInstance('language', 'JCKTable');
	    $row->tag = $folder;
	    $file = $this->parent->getPath('manifest');
	    $filename = str_replace($this->parent->getPath('source').DS,'',$file);	

	    $row->filename =  $filename;

	    if (!$row->store())
	    {
		    // Install failed, roll back changes
		    $this->parent->abort(JText::sprintf('JLIB_INSTALLER_ABORT', $row->getError()));
		    return false;
	    }
		
        // Set path back for manifest
        $this->parent->setPath('extension_administrator', JPATH_COMPONENT . '/language/overrides');
        // Lastly, we will copy the manifest file to its appropriate place.
        if (!$this->parent->copyManifest(1)) 
        {
            // Install failed, rollback changes
	        $this->parent->abort(JText::sprintf('JLIB_INSTALLER_ABORT_PLG_INSTALL_COPY_SETUP', JText::_('JLIB_INSTALLER_INSTALL')));
            return false;
        }

            return true;
    }
	    
    
    
    /**
     * Uninstall method
     *
     * @access	public
     * @param	string	$tag		The tag of the language to uninstall
     * @return	mixed	Return value for uninstall method in component uninstall file
     */
    function uninstall($eid) 
    {
    
	    // Load up the extension details
	    $language = JTable::getInstance('language', 'JCKTable');
	    $language->load($eid);


        $tag = $language->tag;
	
        // Set defaults
        $this->parent->set('tag', $tag);
        $this->parent->set('version', '');

        $path = JPATH_COMPONENT . '/language/overrides';

        if (!JFolder::exists($path)) {
            JError::raiseWarning(100, JText::_('JLIB_INSTALLER_UNINSTALL') . ' : ' . JText::_('JLIB_INSTALLER_ERROR_LANG_UNINSTALL_ELEMENT_EMPTY'));
            return false;
        }
     
        
        $manifestFile =  $path.'/'. $language->filename;

	
       if (JFile::exists($manifestFile)) 
       {

	        $this->manifest  = JFactory::getXML($manifestFile);
              
            if (!$this->manifest) {
                JError::raiseWarning(100, JText::_('JLIB_INSTALLER_INSTALL') . ' : ' . JText::_('JCK_INSTALLER_MANIFEST_INVALID'));
            }
            
            $values = array('name', 'version', 'description');
    		 
    	    foreach($values as $value) 
	        {
    	 	$this->parent->set($value, $this->manifest->$value);
    	    }
    	 
    	    $elements = array('administration', 'editor');
    	 
    	    foreach($elements as $element) 
	        {
	  	    $this->set($element, $this->manifest->$element);
    	    }

            // Set the installation target paths
            $this->parent->setPath('extension_administrator', $path );

            if (!$this->parent->removeFiles($this->get('administration'), 1)) {
                JError::raiseWarning(100, JText::_('JLIB_INSTALLER_INSTALL') . ' : ' . JText::_('JCK_INSTALL_DELETE_FILES_ERROR'));
                return false;
            }

            $this->parent->setPath('extension_administrator', JPATH_COMPONENT. '/editor/lang');

            if (!$this->parent->removeFiles($this->get('editor'),1)) {
                JError::raiseWarning(100, JText::_('JLIB_INSTALLER_INSTALL') . ' : ' . JText::_('JCK_INSTALL_DELETE_FILES_ERROR'));
                return false;
            }
            JFile::delete($manifestFile);
        } 
        else 
        {
            JError::raiseWarning(100, JText::_('JLIB_INSTALLER_INSTALL') . ' : ' . JText::_('JCK_INSTALLER_MANIFEST_ERROR'));
            return false;
        }
	
	    // Remove the language table entry
	    $language->delete();
        return true; // return false to stop at this point.
    }

}