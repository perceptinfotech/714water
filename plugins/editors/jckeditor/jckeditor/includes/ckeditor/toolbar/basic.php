<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die( 'Restricted access' );

class JCKBasic extends JCKToolbar
{
	// row 1
	var $Source = 1;
	
	//row 2
	var $SelectAll = 2;
	var $Cut;
	var $Copy;
	var $Paste;
	var $PasteText;
	var $PasteFromWord;
	var $sep_1;
	var $SpellChecker;
	
	//row 3
	var $Undo = 3;
	var $Redo;
	var $RemoveFormat;
	var $sep_2;
	var $Find;
	var $sep_3;
	
	//row 4
	var $Bold = 4;
	var $Italic;
	var $Underline;
	
	//row 5
	var $NumberedList = 5;
	var $BulletedList;
	var $sep_4;
	var $Outdent;
	var $Indent;
	
	//row 6
	var $JustifyLeft = 6;
	var $JustifyCenter;
	var $JustifyRight;
	var $JustifyBlock;
	
	//row 7
	var $JTreeLink = 7;
	var $Link;
	var $Unlink;
	var $Anchor;
	
	//row 8
	var $Image= 8;
	var $Templates;
	var $Table;
	
	//row 9
	var $Format = 9;
	var $Styles;
	
	//row 10
	var $CreateDiv  = 10;
	var $ShowBlocks;
	
}