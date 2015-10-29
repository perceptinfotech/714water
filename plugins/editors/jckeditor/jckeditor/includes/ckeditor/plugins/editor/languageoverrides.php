<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2013 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.event.plugin');
jckimport('ckeditor.htmlwriter.javascript');


class plgEditorlanguageOverrides extends JPlugin 
{
		
  	function plgEditorlanguageOverrides(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}

	function beforeLoad(&$params)
	{
		
        
        $basePath = JPATH_CONFIGURATION.'/administrator/components/com_jckman/editor/lang';


         $languages = JFolder::files($basePath, '.js$', 1, true);

         $js = "";
         $default = $params->get("joomlaLang","en");
         
         foreach($languages as $language)           
         {
            $content = file_get_contents($language);
            $content = preg_replace("/\/\*.*?\*\//s","",$content);

            $content = str_replace('"',"'",$content);
            $language = str_replace("\\","/",$language);
            $parts = explode("/",$language);
            $lang = preg_replace("/\.js$/","",array_pop($parts));
            $plugin = array_pop($parts);
            if(($lang != $default && $lang != 'en' ) || $plugin == 'lang' ) //make sure we always load in default english file
                continue;

            $content = preg_replace("/\)$/",");",trim($content));

            if($plugin == 'jflash')
                $plug = 'flash';
            else
                $plug = $plugin;
            $js .=  "CKEDITOR.on('".$plugin."PluginLoaded', function(evt)
            {
               editor.lang.".$plug." = null;
               evt.data.lang = ['".$default."'];             
               ".$content."           
            });";
          } 
                         
        //lets create JS object	
		$javascript = new JCKJavascript();
        $javascript->addScriptDeclaration($js);
		return $javascript->toRaw();
	}

}