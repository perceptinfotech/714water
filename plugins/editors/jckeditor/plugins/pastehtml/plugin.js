  
  (function()
  {
  
      CKEDITOR.plugins.add('pastehtml', 
      {

        init: function(editor) 
        {
          // This plugin only applies to IE11+
          if (!(CKEDITOR.env.ie && CKEDITOR.env.version > 10)) 
          {
            return;
          }
    
          editor.on('insertHtml', onInsertFragement,null,null,21);    
        }
      });
   
   
    function checkReadOnly( selection )
	{
		if ( selection.getType() == CKEDITOR.SELECTION_ELEMENT )
			return selection.getSelectedElement().isReadOnly();
		else
			return selection.getCommonAncestor().isReadOnly();
	}
   
   
    function onInsertFragement( evt )
	{
		if ( this.mode == 'wysiwyg' )
		{
			this.focus();

            var selection = this.getSelection();
			if ( checkReadOnly( selection ) )
				return;

            var selection = this.getSelection();
            var selIsLocked = selection.isLocked;

			if ( selIsLocked )
				selection.unlock(true);

            var sel = selection.getNative();

			//var sel = this.document.getWindow().$.getSelection();
	         

			this.fire( 'saveSnapshot' );

            range = sel.getRangeAt(0);
            range.deleteContents();

            // Range.createContextualFragment() would be useful here but is
            // only relatively recently standardized and is not supported in
            // some browsers (IE9, for one)
            var el = this.document.$.createElement("div");
            html = evt.data;
            el.innerHTML = html;
            var frag = this.document.$.createDocumentFragment(), node, lastNode;
            while ( (node = el.firstChild) ) {
                lastNode = frag.appendChild(node);
            }
            range.insertNode(frag);

            // Preserve the selection
            if (lastNode) {
                range = range.cloneRange();
                range.setStartAfter(lastNode);
                range.collapse(true);
                sel.removeAllRanges();
                sel.addRange(range);
            }

            if ( selIsLocked )
					this.getSelection().lock();

			// Save snaps after the whole execution completed.
			// This's a workaround for make DOM modification's happened after
			// 'insertElement' to be included either, e.g. Form-based dialogs' 'commitContents'
			// call.
			CKEDITOR.tools.setTimeout( function(){
				this.fire( 'saveSnapshot' );
			}, 0, this );
		}
	}


})();