
CKEDITOR.plugins.add('ie11selectionfix',
{

    init: function (editor) {
        // This plugin only applies to IE11+
        if (!(CKEDITOR.env.ie && CKEDITOR.env.version > 10)) {
            return;
        }

        (function () {

            var bookmarks;

            CKEDITOR.editor.prototype.getSelection = function () {
                var selection = this.document && this.document.getSelection();
                try {
                    if (this.ie11_bookmarks && selection)
                        selection.selectBookmarks(this.ie11_bookmarks);
                } catch (ex) { };
                return selection;
            };


            CKEDITOR.plugins.contextMenu.prototype.show = function (offsetParent, corner, offsetX, offsetY) {
                //this.editor.focus();

                // Selection will be unavailable after context menu shows up
                // in IE, lock it now.
                var selection = editor.document && editor.document.getSelection();
                 if (selection) {
                    if (selection)
                        editor.ie11_bookmarks = selection.createBookmarks2(true);
                 }
                this._.onMenu(offsetParent || CKEDITOR.document.getDocumentElement(), corner, offsetX || 0, offsetY || 0);
            }



            editor.on('beforeCommandExec', function (evt) {

                var selection = this.getSelection();
                try {
                    if (this.ie11_bookmarks && selection) {
                        selection.selectBookmarks(this.ie11_bookmarks);
                    }
                } catch (ex) { };
            });

            editor.on('insertElement', function (evt) {
                var selection = this.document && this.document.getSelection();
                if (this.ie11_bookmarks && selection)
                    selection.selectBookmarks(this.ie11_bookmarks);
            });

            CKEDITOR.editor.prototype.setBookmarks = function (bookmarks) {
                var selection = this.document && this.document.getSelection();
                if (bookmarks && selection)
                    selection.selectBookmarks(bookmarks);
            }

            editor.on('contentDom', function (evt) {

                bookmarks = null;

                this.document.getWindow().on('blur', function (evt) {
                    editor.ie11_bookmarks = bookmarks;
                });

                this.document.getWindow().on('focus', function (evt) {
                    editor.ie11_bookmarks = null;
                });

                this.document.on('selectionchange', function (evt) {
                    if (CKEDITOR.env.ie && CKEDITOR.env.version > 10) {
                        var selection = this.getSelection();
                        if (selection)
                            bookmarks = selection.createBookmarks2(true);
                    }
                });

            });
        })();
    }
});    