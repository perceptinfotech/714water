<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2013 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

// no direct access
defined( '_JEXEC' ) or die();

class JCKManViewExtension extends JViewLegacy
{
	protected $canDo;
	protected $app;
	protected $item;
	protected $items;
	protected $pagination;

	function display( $tpl = null )
	{
		$this->canDo		= JCKHelper::getActions();
		$this->app			= JFactory::getApplication();
		$model = JModelLegacy::getInstance('Plugin','JCKManModel');
		$this->items	= $model->getItems();
		$this->pagination	= $model->getPagination();
		$langModel = JModelLegacy::getInstance('Language','JCKManModel');
		$this->languages = $langModel->getItems();
		$this->langPagination = $langModel->getPagination(); 

		if(!$this->canDo->get('jckman.uninstall'))
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_jckman&view=cpanel', false ), JText::_( 'COM_JCKMAN_PLUGIN_PERM_NO_INSTALL' ), 'error' );
			return false;
		}//end if

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$bar 	= JToolBar::getInstance('toolbar');

		JToolBarHelper::title( JText::_( 'COM_JCKMAN_SUBMENU_UNINSTALL_NAME' ), 'plugin.png' );

		if($this->canDo->get('core.edit.state'))
		{
			JToolBarHelper::deleteList( '', 'manage.remove', JText::_( 'JTOOLBAR_UNINSTALL' ) );
		}//end if

		$bar->appendButton( 'Link', 'cpanel', JText::_( 'COM_JCKMAN_SUBMENU_CPANEL_NAME' ), 'index.php?option=com_jckman&view=cpanel');

		JCKHelper::addSubmenu( $this->app->input->get( 'view' ) );

		$this->sidebar = JHtmlSidebar::render();
	}//end function

	/**
	 * Pass row item to item tmpl
	 */
	function loadItem($index=0)
	{
		$item 				= $this->items[$index];
		$item->index 		= $index;
		$item->author_info 	= @$item->authorEmail .'<br />'. @$item->authorUrl;
		$this->item			= $item;
	}
	
	/**
	 * Pass row item to item tmpl
	 */
	function loadLanguage($index=0)
	{
		$language 			= $this->languages[$index];
		$language->index 		= $index;
		$language->author_info 	= @$language->authorEmail .'<br />'. @$language->authorUrl;
		$this->language			= $language;
	}
	
	
}