/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.on( 'instanceReady', function( ev )
{
	// The way to close self closing tags, like <br />.
	ev.editor.dataProcessor.writer.selfClosingEnd = ' />';
});
