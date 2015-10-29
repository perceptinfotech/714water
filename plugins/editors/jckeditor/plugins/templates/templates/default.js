/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.addTemplates('default', {
    imagesPath: CKEDITOR.getUrl(CKEDITOR.plugins.getPath('templates') + 'templates/images/'),
    templates: [{
        title: '1 big buttons (no descriptions)',
        image: 'template1.gif',
        description: 'One big buttons with no descriptions.',
        html: '<a class="button-big" href="#">Click here to edit link</a>'
    }, {
        title: '2 big buttons with descriptions',
        image: 'template2.gif',
        description: 'Two big buttons with descriptions (Mobile friendly).',
        html: '<div class="row-fluid"><div class="span6"><a class="button-big" href="#" >Click here to edit link</a><br />Lorem ipsum dolor sit amet, scrutor audire propter Apollonius in deinde vero diam ille. Stans dicenda tuam celat in deinde vero non ait in modo genito in modo.</div><div class="span6"><a class="button-big" href="#">Click here to edit link</a><br />Lorem ipsum dolor sit amet, scrutor audire propter Apollonius in deinde vero diam ille. Stans dicenda tuam celat in deinde vero non ait in modo genito in modo.</div></div>'
    }, {
        title: '3 big buttons with descriptions',
        image: 'template3.gif',
        description: 'Three columns big buttons with descriptions (Mobile friendly)',
        html: '<div class="row-fluid"><div class="span4"><a class="button-big" href="#">Click here to edit link</a><br />Lorem ipsum dolor sit amet, scrutor audire propter Apollonius in deinde vero diam ille. Stans dicenda tuam celat in deinde vero non ait in modo genito in modo.</div><div class="span4"><a class="button-big" href="#">Click here to edit link</a><br />Lorem ipsum dolor sit amet, scrutor audire propter Apollonius in deinde vero diam ille. Stans dicenda tuam celat in deinde vero non ait in modo genito in modo.</div><div class="span4"><a class="button-big" href="#">Click here to edit link</a><br />Lorem ipsum dolor sit amet, scrutor audire propter Apollonius in deinde vero diam ille. Stans dicenda tuam celat in deinde vero non ait in modo genito in modo.</div></div>'
    }, {
        title: 'User profiles and description',
        image: 'template4.gif',
        description: 'User profile description. This is used for the Team and Trustees profiles.',
        html: '<h2>Edit Profile Name</h2><div class="row-fluid"><img alt="Profile" class="polaroids_zoom" src="/plugins/editors/jckeditor/skins/office2007/images/noimage.png" style="float: left;" />Lorem ipsum dolor sit amet, dicentem cives eius non potentiae. Iusto opes mihi Tyrum in deinde vero diam &#39;. Lycoridem Apollonio omnino doming igitur maris iactaverunt cum autem est amet amet coram posset quaelibet potentialitas maiorem ei. Dona abstulit meis dolor Jesus Circumdat flante vestibus regni Tyrius nos filiae Hellenicus sui coniungens in. Scelus quod non solutionem ascendens piratae suppetit.<br /><br />Lorem ipsum dolor sit amet, dicentem cives eius non potentiae. Iusto opes mihi Tyrum in deinde vero diam &#39;. Lycoridem Apollonio omnino doming igitur maris iactaverunt cum autem est amet amet coram posset quaelibet potentialitas maiorem ei. Dona abstulit meis dolor Jesus Circumdat flante vestibus regni Tyrius nos filiae Hellenicus sui coniungens in. Scelus quod non solutionem ascendens piratae suppetit.</div>'
    },{
        title: 'Create your own template',
        image: 'template5.gif',
        description: 'I will quickly run through what you need to do to add a template. The first thing to do is work out what you would like as a template...',
        html: '<p>I will quickly run through what you need to do to add a template. The first thing to do is work out what you would like as a template and customise it to fit your project. The file that the templates html snippets are stored in is located in the default.js file found here:</p><pre>/plugins/editors/jckeditor/plugins/templates/templates/default.js</pre><p>You can create and define your templates by simply adding your custom HTML code to the default.js file as detailed below.</p><pre>{title: &#39;Image and Title&#39;, image: &#39;template1.gif&#39;, description: &#39;Description.&#39;, html: &lt;p&gt;Text here&lt;/p&gt;&#39;}</pre><p>Remember that JavaScript can&rsquo;t have any new line character so your HTML will have to be on one line. Then take a screenshot of your HTML template, crop it down to 100 x 70 pixels and upload the icon to the directory located here:</p><pre>/plugins/editors/jckeditor/plugins/templates/templates/images/</pre>'
    }]
});
