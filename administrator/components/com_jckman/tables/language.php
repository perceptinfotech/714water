<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_BASE') or die();

class JCKTableLanguage extends JTable
{
	/**
	 * Primary Key
	 *
	 *  @var int
	 */
	var $id = null;

	/**
	 * @var varchar
	 */
	var $tag = null;

	/**
	 * @var varchar
	 */
	var $filename = null;


	function __construct(& $db) {
		parent::__construct('#__jcklanguages', 'id', $db);
	}

}