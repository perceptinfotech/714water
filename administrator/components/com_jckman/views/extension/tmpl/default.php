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

JHTML::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

?>
<script language="javascript">
    jQuery(document).ready(
        function ($) {

            function GetQueryParameter(sParam) {
                var sPageURL = window.location.search.substring(1);
                var sURLVariables = sPageURL.split('&');
                for (var i = 0; i < sURLVariables.length; i++) {
                    var sParameterName = sURLVariables[i].split('=');
                    if (sParameterName[0] == sParam) {
                        return sParameterName[1];
                    }
                }
                return false;
            }

            var tab = GetQueryParameter('tab');
            if (tab) {
                $('.nav-tabs a[href="#' + tab + '"]').tab('show');
            }
            
            //override uninstall toolbar buttion
            $('#toolbar-delete button.btn-small')
                .attr('onclick', '')
                    .click(function (e) 
                    {
                        e.preventDefault();
                        var view = $('.tab-pane.active').attr('id');
                        document.adminForm.view.value = view;
                        if (document.adminForm.boxchecked.value==0)
                        {
                            alert('Please first make a selection from the list');
                        }
                        else
                        { 
                            Joomla.submitbutton('manage.remove')
                        }
                    });
        }
    );
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<?php if(!empty( $this->sidebar)): ?>
	<div id="sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
		<?php JCKHelper::fixBug(); ?>
	</div>
	<div id="main-container" class="span10">
<?php else : ?>
	<div id="main-container">
<?php endif;?>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#plugin" data-toggle="tab"><?php echo JText::_('COM_JCK_UNINSTALLER_PLUGINS');?></a></li>
			<li><a href="#language" data-toggle="tab"><?php echo JText::_('COM_JCK_UNINSTALLER_LANGUAGES');?></a></li>
		</ul>
		<div  class="tab-content">
			<div class="tab-pane active" id="plugin">
				<?php if (count($this->items)) : ?>
				<table class="table table-striped adminlist" cellspacing="1">
					<thead>
						<tr>
							<th width="20" class="nowrap center hidden-phone"><?php echo JText::_( 'Num' ); ?></th>
							<th width="1%">
								<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
							</th>
							<th class="nowrap"><?php echo JText::_( 'COM_JCK_UNINSTALLER_PLUGIN' ); ?></th>
							<th class="nowrap center" width="10%"><?php echo JText::_( 'JVERSION' ); ?></th>
							<th class="nowrap hidden-phone" width="15%"><?php echo JText::_( 'JDATE' ); ?></th>
							<th class="nowrap hidden-phone" width="25%"><?php echo JText::_( 'JAUTHOR' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
						</tr>
					</tfoot>
					<tbody>
					<?php foreach($this->items as $i => $item ) : ?>
						<?php
							$this->loadItem($i);
							echo $this->loadTemplate('item');
						?>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php else : ?>
					<table class="table table-striped" cellspacing="1">
						<tr>
							<td class="nowrap center"><?php echo JText::_( 'COM_JCK_UNINSTALLER_NO_CUSTOM_PLUGINS' ); ?></td>
						</tr>
					</table>
				<?php endif; ?>
			</div>
			<div class="tab-pane" id="language">
				<?php if (count($this->languages)) : ?>
				<table class="table table-striped adminlist" cellspacing="1">
					<thead>
						<tr>
							<th width="20" class="nowrap center hidden-phone"><?php echo JText::_( 'Num' ); ?></th>
							<th width="1%">
								<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
							</th>
							<th class="nowrap"><?php echo JText::_( 'COM_JCK_UNINSTALLER_LANGUAGE' ); ?></th>
							<th class="nowrap center"><?php echo JText::_( 'COM_JCK_UNINSTALLER_LANGUAGE_TAG' ); ?></th>
							<th class="nowrap" width="25%"><?php echo JText::_( 'COM_JCK_UNINSTALLER_PLUGIN' ); ?></th>
							<th class="nowrap center hidden-phone" width="10%"><?php echo JText::_( 'JVERSION' ); ?></th>
							<th class="nowrap hidden-phone" width="15%"><?php echo JText::_( 'JDATE' ); ?></th>
							<th class="nowrap hidden-phone" width="25%"><?php echo JText::_( 'JAUTHOR' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="8"><?php echo $this->langPagination->getListFooter(); ?></td>
						</tr>
					</tfoot>
					<tbody>
					<?php foreach($this->languages as $i => $language ) : ?>
						<?php
							$this->loadLanguage($i);
							echo $this->loadTemplate('language');
						?>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php else : ?>
					<table class="table table-striped" cellspacing="1">
						<tr>
							<td class="nowrap center"><?php echo JText::_( 'COM_JCK_UNINSTALLER_NO_CUSTOM_LANGUAGES' ); ?></td>
						</tr>
					</table>
				<?php endif; ?>
		
			</div>
		</div>
        <input type="hidden" name="view" value="plugin" />
		<input type="hidden" name="task" value="manage" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="option" value="com_jckman" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</div>
</form>