<?php
header("Content-type: text/css; charset: UTF-8");


/*
.wpview-wrap[data-wpview-type=mdl-icon] i.mce-i-dashicon.dashicons-edit {
	display: none;
}
*/

// override WP 4.3's /wp-includes/js/tinymce/skins/wordpress/wp-content.css body { word-break: break-word } causing MDL icons used in links (button, card, menu) to not display and MDL Icon background being wider than the icon's square-ish area in TinyMCE/Shortcake preview
// WP 4.3 added these 3 rules --> overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;
?>
body {
	word-break: normal;
}