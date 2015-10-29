/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 
CKEDITOR.plugins.add( 'chromereadmorefix',
{
	init:function(editor) 
	 {
		//Nothing to do	 
	 },
	  
	afterInit: function (editor) 
	{
            
		var dataProcessor = editor.dataProcessor,
			 htmlFilter = dataProcessor && dataProcessor.htmlFilter;
	  
		if (htmlFilter)
		{
			htmlFilter.addRules(
			{
				
				elements: 
				{
					hr : function (element) 
					{
						if(element.attributes.id && element.attributes.id == 'system-readmore' && element.attributes.style)
						{
							delete element.attributes.style 
						}
					
						return element;
					}
				}
			});
		}
			
    }
	
	
});

