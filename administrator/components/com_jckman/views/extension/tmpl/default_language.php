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

?>
<tr>
	<td class="center hidden-phone">
		<?php echo $this->langPagination->getRowOffset( $this->language->index ); ?>
	</td>
	<td class="center">
		<?php echo JHtml::_('grid.id', $this->language->index, $this->language->id); ?>
	</td>
	<td class="jckbreak">
		<?php echo $this->language->name; ?>
	</td>
	<td class="center">
		<?php echo $this->language->tag;?>
	</td>
	<td class="jckbreak">
		<?php echo $this->language->plugin;?>
	</td>
	<td class="center">
		<?php echo @$this->language->version ? $this->language->version : '--'; ?>
	</td>
	<td class="hidden-phone">
		<?php echo @$this->language->creationDate ? $this->language->creationDate : '--'; ?>
	</td>
	<td class="hidden-phone">
		<span class="hasTip" title="<?php echo JText::_( 'COM_JCK_UNINSTALLER_AUTHOR_INFORMATION' );?>::<?php echo $this->language->author_info; ?>">
			<?php echo @$this->language->author ? $this->language->author : '--'; ?>
		</span>
	</td>
</tr>