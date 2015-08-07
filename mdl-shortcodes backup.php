<?php

/*
Plugin Name: MDL Shortcodes
Plugin URI: http://www.getmdl.io/components/
Description: Material Design Lite (MDL) components are viewable at http://www.getmdl.io/components/
Author: TourKick (Clifford P)
Author URI: http://tourkick.com/
Version: 1.0
License: GPLv3
*/

/*
LICENSE FAQ:
Why not "GPLv2" or "GPLv2 or later"?
Because:
- http://www.getmdl.io/started/index.html#license is licensed as Apache-2
- https://wordpress.org/plugins/about/guidelines/ requires "GPLv2 or later" (which includes GPLv3)
- https://www.gnu.org/licenses/rms-why-gplv3.html says GPLv3 is compatible with Apache licensing but GPLv2 is not
- http://www.apache.org/licenses/GPL-compatibility.html agrees Apache v2 is compatible with GPLv3 but not GPLv2
*/


/*
* START notes

TODO:
Move to a class -- https://codex.wordpress.org/Function_Reference/add_action#Using_with_a_Class
Add filters and actions (make hookable)

Inspirations:
https://medium.com/google-developers/introducing-material-design-lite-3ce67098c031
https://wordpress.org/themes/corpobox-lite/
http://www.premiumwp.com/premium-and-free-material-design-wordpress-themes/
http://themeforest.net/item/material-design-wordpress-theme-rare/11408042
http://www.getmdl.io/templates/
http://www.getmdl.io/components/
http://www.getmdl.io/styles/
http://www.getmdl.io/faq/
https://www.google.com/design/icons/
https://www.google.com/design/spec/components/
http://mdlhut.com/
https://material.angularjs.org/
http://materialdesignblog.com/material-design-wordpress/

* END notes
*/

// implement Material Design Lite http://www.getmdl.io/started/
add_action( 'wp_enqueue_scripts', 'mdl_setup_func' );
function mdl_setup_func() {
	
	// edit colors -- make sure proper spelling and color combos from http://www.getmdl.io/customize/index.html
	$primary = 'yellow';
	$secondary = 'blue';
	
	$color_names = array(
		'teal',
		'purple',
		'deep_purple',
		'deep_orange',
		'pink',
		'indigo',
		'blue_grey',
		'red',
		'yellow',
		'blue',
		'green',
		'brown',
		'grey',
		'orange',
		'light_blue',
		'amber',
		'cyan',
		'light_green',
		'lime',
	);
	
	if( ! in_array($primary, $color_names) || ! in_array($secondary, $color_names) ) {
		return false; // wrong color names -- even if true, might not be right color combinations!
	}
	
	// and decide if you want it hosted by Google or not (ugh, yes!)
	$hosted_google = true;
	
	
	//
	// let the code take it from here
	//
	$deps = array();
	$version = '1.0.1'; //from Google Hosted URI
	
	// MDL CSS stylesheet
	$css_handle = sprintf('mdl-%s-%s', $primary, $secondary);
	
	$css = '';
	if($hosted_google) {
		$css = sprintf('https://storage.googleapis.com/code.getmdl.io/%s/material.%s-%s.min.css', $version, $primary, $secondary);
	}	
	if($css) {
		wp_enqueue_style( $css_handle, $css, $deps, $version );
	}
	
	// MDL icons stylesheet ($deps and $version variables inherited from CSS, above)
	$icons = '';
	if($hosted_google) {
		$icons = 'https://fonts.googleapis.com/icon?family=Material+Icons';
	}
	if($icons) {
		wp_enqueue_style( 'mdl-icons', $icons, $deps, '2.0.0' ); // Version should keep up with https://github.com/google/material-design-icons/blob/master/package.json
	}
	
	// MDL JavaScript ($deps and $version variables inherited from CSS, above)
	$js = '';
	if($hosted_google) {
		$js = sprintf('https://storage.googleapis.com/code.getmdl.io/%s/material.min.js', $version);
	}
	if($js) {
		wp_enqueue_script( 'mdl-script', $js, $deps, $version, true );
	}

}



// HELPER FUNCTIONS



// remove all but integers and commas from string -- from http://stackoverflow.com/a/5798519/893907
// Example: $str = ", 3.3,,x,,, , 2 4b , , 3 , 2 4 ,,,,,";
// results in: 33,24,3,24
function mdl_only_integers_commas( $string = null ) {
	$value = preg_replace(
		array(
		'/[^\d,]/',    // Matches anything that's not a comma or number.
		'/(?<=,),+/',  // Matches consecutive commas.
		'/^,+/',       // Matches leading commas.
		'/,+$/'        // Matches trailing commas.
		),
		'',              // Remove all matched substrings.
		(string) $string //typecast as string -- string in, string out
	);
	
	return $value; // NULL and FALSE both return an empty string
}


// string is either 'true' or 'false'
// and if it is not, set a default value
function mdl_truefalse( $string, $default = '' ) {
	$string = trim ( strtolower( (string) $string ) );
	
	$allowed_true_false = array( 'true', 'false' );
	
	if( in_array( $string, $allowed_true_false) ) {
		return $string;
	} else {
		return $default;
	}
}


// string is h1-h6
// and if it is not, set a default value
function mdl_htag_default_h2( $string, $default = 'h2' ) {
	$string = trim( strtolower( (string) $string ) );
	
	$allowed_htags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
	
	if( in_array( $string, $allowed_htags) ) {
		return $string;
	} else {
		return $default;
	}
}


// string is something like a CSS length or position value (not perfect but works well enough -- e.g. +15exm does not validate but -20%x does)
//
// based on http://www.shamasis.net/2009/07/regular-expression-to-validate-css-length-and-position-values/
// reference: http://www.w3schools.com/cssref/css_units.asp
function mdl_restrict_css_unit_value( $string, $default = '' ) {
	$string = trim( strtolower( (string) $string ) );
	
	if( strpos( $string, 'auto') !== false
		|| strpos( $string, '0') !== false
		|| preg_match( '/^[+-]?[0-9]+.?([0-9]+)?(em|ex|%|px|cm|mm|in|pt|pc|ch|rem|vh|vw|vmin|vmax)$/', $string )
	) {
		return $string;
	} else {
		return $default;
	}
}





// sanitize_html_classes() is from https://gist.github.com/justnorris/5387539
if ( ! function_exists( 'mdl_sanitize_html_classes' ) && function_exists( 'sanitize_html_class' ) ) {
	 // sanitize_html_class works just fine for a single class
	 // Some times le wild <span class="blue hedgehog"> appears, which is when you need this function,
	 // to validate both blue and hedgehog,
	 // Because sanitize_html_class doesn't allow spaces.
	 //
	 // @uses   sanitize_html_class
	 // @param  (mixed: string/array) $class   "blue hedgehog goes shopping" or array("blue", "hedgehog", "goes", "shopping")
	 // @param  (mixed) $fallback Anything you want returned in case of a failure
	 // @return (mixed: string / $fallback )
	function mdl_sanitize_html_classes( $class, $fallback = null ) {

		// Explode it, if it's a string
		if ( is_string( $class ) ) {
			$class = explode(' ', $class);
		}
		
		if ( is_array( $class ) && count( $class ) > 0 ) {
			$class = array_map('sanitize_html_class', $class);
			return implode(' ', $class);
		} else { 
			return sanitize_html_class( $class, $fallback );
		}
	}
}


//
// Whitelist of all Material Design icon names
// Current as of July 20, 2015 -- from https://github.com/google/material-design-icons/blob/master/iconfont/codepoints
// To preview (and search through) all Material Design icons, visit https://www.google.com/design/icons/
// TODO: possibly add support for IE9 or below:
	// For modern browsers: <i class="material-icons">description</i>
	// For IE9 or below: <i class="material-icons">&#xE873;</i>
//
function mdl_valid_icon_name_func( $icon_name ) {
	if( empty($icon_name) ) {
		return false;
	}
	
	$mdl_icons = array(
		'3d_rotation',
		'access_alarm',
		'access_alarms',
		'access_time',
		'accessibility',
		'account_balance',
		'account_balance_wallet',
		'account_box',
		'account_circle',
		'adb',
		'add',
		'add_alarm',
		'add_alert',
		'add_box',
		'add_circle',
		'add_circle_outline',
		'add_shopping_cart',
		'add_to_photos',
		'adjust',
		'airline_seat_flat',
		'airline_seat_flat_angled',
		'airline_seat_individual_suite',
		'airline_seat_legroom_extra',
		'airline_seat_legroom_normal',
		'airline_seat_legroom_reduced',
		'airline_seat_recline_extra',
		'airline_seat_recline_normal',
		'airplanemode_active',
		'airplanemode_inactive',
		'airplay',
		'alarm',
		'alarm_add',
		'alarm_off',
		'alarm_on',
		'album',
		'android',
		'announcement',
		'apps',
		'archive',
		'arrow_back',
		'arrow_drop_down',
		'arrow_drop_down_circle',
		'arrow_drop_up',
		'arrow_forward',
		'aspect_ratio',
		'assessment',
		'assignment',
		'assignment_ind',
		'assignment_late',
		'assignment_return',
		'assignment_returned',
		'assignment_turned_in',
		'assistant',
		'assistant_photo',
		'attach_file',
		'attach_money',
		'attachment',
		'audiotrack',
		'autorenew',
		'av_timer',
		'backspace',
		'backup',
		'battery_alert',
		'battery_charging_full',
		'battery_full',
		'battery_std',
		'battery_unknown',
		'beenhere',
		'block',
		'bluetooth',
		'bluetooth_audio',
		'bluetooth_connected',
		'bluetooth_disabled',
		'bluetooth_searching',
		'blur_circular',
		'blur_linear',
		'blur_off',
		'blur_on',
		'book',
		'bookmark',
		'bookmark_border',
		'border_all',
		'border_bottom',
		'border_clear',
		'border_color',
		'border_horizontal',
		'border_inner',
		'border_left',
		'border_outer',
		'border_right',
		'border_style',
		'border_top',
		'border_vertical',
		'brightness_1',
		'brightness_2',
		'brightness_3',
		'brightness_4',
		'brightness_5',
		'brightness_6',
		'brightness_7',
		'brightness_auto',
		'brightness_high',
		'brightness_low',
		'brightness_medium',
		'broken_image',
		'brush',
		'bug_report',
		'build',
		'business',
		'cached',
		'cake',
		'call',
		'call_end',
		'call_made',
		'call_merge',
		'call_missed',
		'call_received',
		'call_split',
		'camera',
		'camera_alt',
		'camera_enhance',
		'camera_front',
		'camera_rear',
		'camera_roll',
		'cancel',
		'card_giftcard',
		'card_membership',
		'card_travel',
		'cast',
		'cast_connected',
		'center_focus_strong',
		'center_focus_weak',
		'change_history',
		'chat',
		'chat_bubble',
		'chat_bubble_outline',
		'check',
		'check_box',
		'check_box_outline_blank',
		'check_circle',
		'chevron_left',
		'chevron_right',
		'chrome_reader_mode',
		'class',
		'clear',
		'clear_all',
		'close',
		'closed_caption',
		'cloud',
		'cloud_circle',
		'cloud_done',
		'cloud_download',
		'cloud_off',
		'cloud_queue',
		'cloud_upload',
		'code',
		'collections',
		'collections_bookmark',
		'color_lens',
		'colorize',
		'comment',
		'compare',
		'computer',
		'confirmation_number',
		'contact_phone',
		'contacts',
		'content_copy',
		'content_cut',
		'content_paste',
		'control_point',
		'control_point_duplicate',
		'create',
		'credit_card',
		'crop',
		'crop_16_9',
		'crop_3_2',
		'crop_5_4',
		'crop_7_5',
		'crop_din',
		'crop_free',
		'crop_landscape',
		'crop_original',
		'crop_portrait',
		'crop_square',
		'dashboard',
		'data_usage',
		'dehaze',
		'delete',
		'description',
		'desktop_mac',
		'desktop_windows',
		'details',
		'developer_board',
		'developer_mode',
		'device_hub',
		'devices',
		'dialer_sip',
		'dialpad',
		'directions',
		'directions_bike',
		'directions_boat',
		'directions_bus',
		'directions_car',
		'directions_railway',
		'directions_run',
		'directions_subway',
		'directions_transit',
		'directions_walk',
		'disc_full',
		'dns',
		'do_not_disturb',
		'do_not_disturb_alt',
		'dock',
		'domain',
		'done',
		'done_all',
		'drafts',
		'drive_eta',
		'dvr',
		'edit',
		'eject',
		'email',
		'equalizer',
		'error',
		'error_outline',
		'event',
		'event_available',
		'event_busy',
		'event_note',
		'event_seat',
		'exit_to_app',
		'expand_less',
		'expand_more',
		'explicit',
		'explore',
		'exposure',
		'exposure_neg_1',
		'exposure_neg_2',
		'exposure_plus_1',
		'exposure_plus_2',
		'exposure_zero',
		'extension',
		'face',
		'fast_forward',
		'fast_rewind',
		'favorite',
		'favorite_border',
		'feedback',
		'file_download',
		'file_upload',
		'filter',
		'filter_1',
		'filter_2',
		'filter_3',
		'filter_4',
		'filter_5',
		'filter_6',
		'filter_7',
		'filter_8',
		'filter_9',
		'filter_9_plus',
		'filter_b_and_w',
		'filter_center_focus',
		'filter_drama',
		'filter_frames',
		'filter_hdr',
		'filter_list',
		'filter_none',
		'filter_tilt_shift',
		'filter_vintage',
		'find_in_page',
		'find_replace',
		'flag',
		'flare',
		'flash_auto',
		'flash_off',
		'flash_on',
		'flight',
		'flight_land',
		'flight_takeoff',
		'flip',
		'flip_to_back',
		'flip_to_front',
		'folder',
		'folder_open',
		'folder_shared',
		'folder_special',
		'font_download',
		'format_align_center',
		'format_align_justify',
		'format_align_left',
		'format_align_right',
		'format_bold',
		'format_clear',
		'format_color_fill',
		'format_color_reset',
		'format_color_text',
		'format_indent_decrease',
		'format_indent_increase',
		'format_italic',
		'format_line_spacing',
		'format_list_bulleted',
		'format_list_numbered',
		'format_paint',
		'format_quote',
		'format_size',
		'format_strikethrough',
		'format_textdirection_l_to_r',
		'format_textdirection_r_to_l',
		'format_underlined',
		'forum',
		'forward',
		'forward_10',
		'forward_30',
		'forward_5',
		'fullscreen',
		'fullscreen_exit',
		'functions',
		'gamepad',
		'games',
		'gesture',
		'get_app',
		'gif',
		'gps_fixed',
		'gps_not_fixed',
		'gps_off',
		'grade',
		'gradient',
		'grain',
		'graphic_eq',
		'grid_off',
		'grid_on',
		'group',
		'group_add',
		'group_work',
		'hd',
		'hdr_off',
		'hdr_on',
		'hdr_strong',
		'hdr_weak',
		'headset',
		'headset_mic',
		'healing',
		'hearing',
		'help',
		'help_outline',
		'high_quality',
		'highlight_off',
		'history',
		'home',
		'hotel',
		'hourglass_empty',
		'hourglass_full',
		'http',
		'https',
		'image',
		'image_aspect_ratio',
		'import_export',
		'inbox',
		'indeterminate_check_box',
		'info',
		'info_outline',
		'input',
		'insert_chart',
		'insert_comment',
		'insert_drive_file',
		'insert_emoticon',
		'insert_invitation',
		'insert_link',
		'insert_photo',
		'invert_colors',
		'invert_colors_off',
		'iso',
		'keyboard',
		'keyboard_arrow_down',
		'keyboard_arrow_left',
		'keyboard_arrow_right',
		'keyboard_arrow_up',
		'keyboard_backspace',
		'keyboard_capslock',
		'keyboard_hide',
		'keyboard_return',
		'keyboard_tab',
		'keyboard_voice',
		'label',
		'label_outline',
		'landscape',
		'language',
		'laptop',
		'laptop_chromebook',
		'laptop_mac',
		'laptop_windows',
		'launch',
		'layers',
		'layers_clear',
		'leak_add',
		'leak_remove',
		'lens',
		'library_add',
		'library_books',
		'library_music',
		'link',
		'list',
		'live_help',
		'live_tv',
		'local_activity',
		'local_airport',
		'local_atm',
		'local_bar',
		'local_cafe',
		'local_car_wash',
		'local_convenience_store',
		'local_dining',
		'local_drink',
		'local_florist',
		'local_gas_station',
		'local_grocery_store',
		'local_hospital',
		'local_hotel',
		'local_laundry_service',
		'local_library',
		'local_mall',
		'local_movies',
		'local_offer',
		'local_parking',
		'local_pharmacy',
		'local_phone',
		'local_pizza',
		'local_play',
		'local_post_office',
		'local_printshop',
		'local_see',
		'local_shipping',
		'local_taxi',
		'location_city',
		'location_disabled',
		'location_off',
		'location_on',
		'location_searching',
		'lock',
		'lock_open',
		'lock_outline',
		'looks',
		'looks_3',
		'looks_4',
		'looks_5',
		'looks_6',
		'looks_one',
		'looks_two',
		'loop',
		'loupe',
		'loyalty',
		'mail',
		'map',
		'markunread',
		'markunread_mailbox',
		'memory',
		'menu',
		'merge_type',
		'message',
		'mic',
		'mic_none',
		'mic_off',
		'mms',
		'mode_comment',
		'mode_edit',
		'money_off',
		'monochrome_photos',
		'mood',
		'mood_bad',
		'more',
		'more_horiz',
		'more_vert',
		'mouse',
		'movie',
		'movie_creation',
		'music_note',
		'my_location',
		'nature',
		'nature_people',
		'navigate_before',
		'navigate_next',
		'navigation',
		'network_cell',
		'network_locked',
		'network_wifi',
		'new_releases',
		'nfc',
		'no_sim',
		'not_interested',
		'note_add',
		'notifications',
		'notifications_active',
		'notifications_none',
		'notifications_off',
		'notifications_paused',
		'offline_pin',
		'ondemand_video',
		'open_in_browser',
		'open_in_new',
		'open_with',
		'pages',
		'pageview',
		'palette',
		'panorama',
		'panorama_fish_eye',
		'panorama_horizontal',
		'panorama_vertical',
		'panorama_wide_angle',
		'party_mode',
		'pause',
		'pause_circle_filled',
		'pause_circle_outline',
		'payment',
		'people',
		'people_outline',
		'perm_camera_mic',
		'perm_contact_calendar',
		'perm_data_setting',
		'perm_device_information',
		'perm_identity',
		'perm_media',
		'perm_phone_msg',
		'perm_scan_wifi',
		'person',
		'person_add',
		'person_outline',
		'person_pin',
		'personal_video',
		'phone',
		'phone_android',
		'phone_bluetooth_speaker',
		'phone_forwarded',
		'phone_in_talk',
		'phone_iphone',
		'phone_locked',
		'phone_missed',
		'phone_paused',
		'phonelink',
		'phonelink_erase',
		'phonelink_lock',
		'phonelink_off',
		'phonelink_ring',
		'phonelink_setup',
		'photo',
		'photo_album',
		'photo_camera',
		'photo_library',
		'photo_size_select_actual',
		'photo_size_select_large',
		'photo_size_select_small',
		'picture_as_pdf',
		'picture_in_picture',
		'pin_drop',
		'place',
		'play_arrow',
		'play_circle_filled',
		'play_circle_outline',
		'play_for_work',
		'playlist_add',
		'plus_one',
		'poll',
		'polymer',
		'portable_wifi_off',
		'portrait',
		'power',
		'power_input',
		'power_settings_new',
		'present_to_all',
		'print',
		'public',
		'publish',
		'query_builder',
		'question_answer',
		'queue',
		'queue_music',
		'radio',
		'radio_button_checked',
		'radio_button_unchecked',
		'rate_review',
		'receipt',
		'recent_actors',
		'redeem',
		'redo',
		'refresh',
		'remove',
		'remove_circle',
		'remove_circle_outline',
		'remove_red_eye',
		'reorder',
		'repeat',
		'repeat_one',
		'replay',
		'replay_10',
		'replay_30',
		'replay_5',
		'reply',
		'reply_all',
		'report',
		'report_problem',
		'restaurant_menu',
		'restore',
		'ring_volume',
		'room',
		'rotate_90_degrees_ccw',
		'rotate_left',
		'rotate_right',
		'router',
		'satellite',
		'save',
		'scanner',
		'schedule',
		'school',
		'screen_lock_landscape',
		'screen_lock_portrait',
		'screen_lock_rotation',
		'screen_rotation',
		'sd_card',
		'sd_storage',
		'search',
		'security',
		'select_all',
		'send',
		'settings',
		'settings_applications',
		'settings_backup_restore',
		'settings_bluetooth',
		'settings_brightness',
		'settings_cell',
		'settings_ethernet',
		'settings_input_antenna',
		'settings_input_component',
		'settings_input_composite',
		'settings_input_hdmi',
		'settings_input_svideo',
		'settings_overscan',
		'settings_phone',
		'settings_power',
		'settings_remote',
		'settings_system_daydream',
		'settings_voice',
		'share',
		'shop',
		'shop_two',
		'shopping_basket',
		'shopping_cart',
		'shuffle',
		'signal_cellular_4_bar',
		'signal_cellular_connected_no_internet_4_bar',
		'signal_cellular_no_sim',
		'signal_cellular_null',
		'signal_cellular_off',
		'signal_wifi_4_bar',
		'signal_wifi_4_bar_lock',
		'signal_wifi_off',
		'sim_card',
		'sim_card_alert',
		'skip_next',
		'skip_previous',
		'slideshow',
		'smartphone',
		'sms',
		'sms_failed',
		'snooze',
		'sort',
		'sort_by_alpha',
		'space_bar',
		'speaker',
		'speaker_group',
		'speaker_notes',
		'speaker_phone',
		'spellcheck',
		'star',
		'star_border',
		'star_half',
		'stars',
		'stay_current_landscape',
		'stay_current_portrait',
		'stay_primary_landscape',
		'stay_primary_portrait',
		'stop',
		'storage',
		'store',
		'store_mall_directory',
		'straighten',
		'strikethrough_s',
		'style',
		'subject',
		'subtitles',
		'supervisor_account',
		'surround_sound',
		'swap_calls',
		'swap_horiz',
		'swap_vert',
		'swap_vertical_circle',
		'switch_camera',
		'switch_video',
		'sync',
		'sync_disabled',
		'sync_problem',
		'system_update',
		'system_update_alt',
		'tab',
		'tab_unselected',
		'tablet',
		'tablet_android',
		'tablet_mac',
		'tag_faces',
		'tap_and_play',
		'terrain',
		'text_format',
		'textsms',
		'texture',
		'theaters',
		'thumb_down',
		'thumb_up',
		'thumbs_up_down',
		'time_to_leave',
		'timelapse',
		'timer',
		'timer_10',
		'timer_3',
		'timer_off',
		'toc',
		'today',
		'toll',
		'tonality',
		'toys',
		'track_changes',
		'traffic',
		'transform',
		'translate',
		'trending_down',
		'trending_flat',
		'trending_up',
		'tune',
		'turned_in',
		'turned_in_not',
		'tv',
		'undo',
		'unfold_less',
		'unfold_more',
		'usb',
		'verified_user',
		'vertical_align_bottom',
		'vertical_align_center',
		'vertical_align_top',
		'vibration',
		'video_library',
		'videocam',
		'videocam_off',
		'view_agenda',
		'view_array',
		'view_carousel',
		'view_column',
		'view_comfy',
		'view_compact',
		'view_day',
		'view_headline',
		'view_list',
		'view_module',
		'view_quilt',
		'view_stream',
		'view_week',
		'vignette',
		'visibility',
		'visibility_off',
		'voice_chat',
		'voicemail',
		'volume_down',
		'volume_mute',
		'volume_off',
		'volume_up',
		'vpn_key',
		'vpn_lock',
		'wallpaper',
		'warning',
		'watch',
		'wb_auto',
		'wb_cloudy',
		'wb_incandescent',
		'wb_iridescent',
		'wb_sunny',
		'wc',
		'web',
		'whatshot',
		'widgets',
		'wifi',
		'wifi_lock',
		'wifi_tethering',
		'work',
		'wrap_text',
		'youtube_searched_for',
		'zoom_in',
		'zoom_out',
	);
	
	$icon_name = strtolower( $icon_name );
	
	if( in_array( $icon_name, $mdl_icons ) ) {
		return true; // yes, it is a valid icon name
	} else {
		return false;
	}
	
}


// START Icon
//
// https://www.google.com/design/icons/
//
// Examples:
// [mdl-icon icon=zoom_in]
// [mdl-icon class="custom-48 inverse-bg" icon=wifi]
//
add_shortcode( 'mdl-icon', 'mdl_icon_func' );
function mdl_icon_func( $atts ) {

	// Attributes
	$defaults = array(
		'icon'	=>	'',
		'class'	=>	'', //additional classes
	);

	$atts = shortcode_atts( $defaults, $atts );
	
	$icon = strtolower( $atts['icon'] );
	
	if( function_exists( 'mdl_valid_icon_name_func' ) ) {
		if( false === mdl_valid_icon_name_func( $icon ) ) {
			return '<!-- MDL Icon ERROR: Invalid Icon Name -->';
		}
	} else {
		return '<!-- MDL Icon ERROR: Missing Icons Whitelist Function -->';
	}
		
	if( function_exists( 'mdl_sanitize_html_classes' ) ) {
		$class = mdl_sanitize_html_classes( $atts['class'] );
	} else {
		// this will remove spaces so is faulty but better than not sanitizing output at all
		$class = sanitize_html_class( $atts['class'] );
	}
	
	// BUILD OUTPUT
	$output = '<!-- MDL Icon -->';
		
	$classes = 'material-icons';
	if( $class ) {
		$classes .= ' ' . $class;
	}
	
	$output .= sprintf( '<i class="%s">%s</i>', $classes, $icon );
	
	return $output; // no do_shortcode() necessary since not allowed
}
// END Icon






// START Badge
//
// http://www.getmdl.io/components/index.html#badges-section
//
// $content could be text like "Inbox" or "Mood" or an icon
// Examples:
// [mdl-badge data="4"]Updates[/mdl-badge]
// [mdl-badge icon=true data=4 type=link url="https://google.com/" target=blank]account_box[/mdl-badge]
//
add_shortcode( 'mdl-badge', 'mdl_badge_func' );
function mdl_badge_func( $atts, $content = null ) {

	// Attributes
	$defaults = array(
		'type'			=>	'span', // span, link, div
		'icon'			=>	'false',	// if $content will be an icon, like 'account_box':
									// <div class="material-icons mdl-badge" data-badge="1">account_box</div>
		'url'			=>	'',
		'target'		=>	'', // e.g. 'blank'
		'data_bg'		=>	'true', // badge background color has color or is transparent
		'data_limit' 	=>	3, // MDL recommends no more than 3 characters in the badge, but allowing user to override
		'data'			=>	'', // what goes in the badge
	);

	$atts = shortcode_atts( $defaults, $atts );
	
	$type		=	strtolower( $atts['type'] );
	$icon		=	strtolower( $atts['icon'] );
	$url		=	strtolower( $atts['url'] );
	$target		=	strtolower( $atts['target'] );
	$data_bg	=	strtolower( $atts['data_bg'] );
	$data_limit	=	$atts['data_limit'];
	$data		=	$atts['data'];
	
	
	// SANITIZE INPUT
	
	
	$content = esc_html( $content );
	
	
	$allowed_types = array( 'span', 'link', 'div' );
	if( ! in_array( $type, $allowed_types ) ) {
		$type = 'span';
	}
	
	$icon = mdl_truefalse( $icon, 'false' );
	
	$url = esc_url( $url );
	
	$allowed_targets = array( '_blank', '_self', '_parent', '_top' );
	if( substr( $target, 0, 1 ) !== '_' ) {
		$target = '_' . $target;
	}
	if( ! in_array( $target, $allowed_targets ) ) {
		$target = '';
	}
	
	$data_bg = mdl_truefalse( $data_bg, 'true' );
	
	$data_limit = absint( $data_limit );
	if( $data_limit < 1 ) {
		$data_limit = 3;
	}
	
	$data = sanitize_text_field( $data );
	$data = substr( $data, 0, $data_limit );
	
	// EXIT if no badge!
	if( empty( $data ) ) {
		return false;
	}
	
	// BUILD OUTPUT
	$output = '<!-- MDL Badge -->';
	
	if ( 'link' == $type && empty($url) ) {
		$type = 'span';
	}
	
	$classes = 'mdl-badge';
	if ( 'true' == $icon ) {
		$classes .= ' material-icons';
	}
	if ( 'false' == $data_bg ) {
		$classes .= ' mdl-badge--no-background';
	}
	
	if( 'span' == $type ) {
		$output .= sprintf( '<span class="%s" data-badge="%s">%s</span>', $classes, $data, $content );
	} elseif( 'div' == $type ) {
		$output .= sprintf( '<div class="%s" data-badge="%s">%s</div>', $classes, $data, $content );
	} elseif( 'link' == $type ) {
		$output .= '<a';
		if ( $target ) {
			$output .= sprintf( ' target="%s"', $target );
		}
		$output .= sprintf( ' href="%s" class="%s" data-badge="%s">%s</a>', $url, $classes, $data, $content );
	} else {
		return false;
	}
	
	return do_shortcode( $output );
}
// END Badge




// START Buttons
//
// http://www.getmdl.io/components/index.html#buttons-section
// https://www.google.com/design/spec/components/buttons.html
//
// FAB = Floating Action Button
//
// $content could be text or an icon
// Examples:
// [mdl-button]Button Text Here[/mdl-button]
// [mdl-button type=fab color=true effect=false]X[/mdl-button]
// [mdl-button type=fab disabled=true]X[/mdl-button]
// [mdl-button type=icon][mdl-icon icon=add][/mdl-button]
	// above uses the 'mdl-icon' shortcode
// [mdl-button type=icon color=primary url="https://www.google.com/" target=blank]<i class="material-icons">mood</i>[/mdl-button]
	// above uses custom HTML
//
add_shortcode( 'mdl-button', 'mdl_button_func' );
function mdl_button_func( $atts, $content = null ) {

	// Attributes
	$defaults = array(
		'type' => 'none',	// none, raised, fab (circular), mini-fab (small circular), icon ... none is effectively flat / no background
							// if you use 'icon', you need to add an icon as the $content
		'color' => 'false', // colored, true, false, primary, accent ... mdl-button--colored uses accent color so 'colored' is same as 'accent'
		'effect' => 'true', // 'true' is same as 'ripple' because it is the only existing effect
		'disabled' => 'false', // add HTML 'disabled' attribute
		'url' => '',
		'target' => '', // e.g. 'blank'
	);

	$atts = shortcode_atts( $defaults, $atts );
	
	$type		=	strtolower( $atts['type'] );
	$color		=	strtolower( $atts['color'] );
	$effect		=	strtolower( $atts['effect'] );
	$disabled	=	strtolower( $atts['disabled'] );
	$url		=	strtolower( $atts['url'] );
	$target		=	strtolower( $atts['target'] );
	
		
	// SANITIZE INPUT
	
	
	// cannot if we want to allow HTML like <i> icons
	// $content = esc_html( $content );
	
	
	$allowed_types = array( 'none', 'raised', 'fab', 'mini-fab', 'icon' );
	if( 'circle' == $type ) {
		$type = 'fab';
	}
	if(    'minifab' == $type
		|| 'mini_fab' == $type
		|| 'minicircle' == $type
		|| 'mini-circle' == $type
		|| 'mini_circle' == $type
	) {
		$type = 'mini-fab';
	}
	if( ! in_array( $type, $allowed_types ) ) {
		$type = 'none';
	}
	
	$allowed_colors = array( 'false', 'primary', 'colored', 'accent' );
	if( 'true' == $color ) {
		$color = 'colored';
	}
	if( ! in_array( $color, $allowed_colors ) ) {
		$color = 'colored';
	}
	
	$allowed_effects = array( 'false', 'ripple' );
	if( 'true' == $effect ) {
		$effect = 'ripple';
	}
	if( ! in_array( $effect, $allowed_effects ) ) {
		$effect = 'ripple';
	}
	
	$disabled = mdl_truefalse( $disabled, 'false' );
	if( 'false' == $disabled ) {
		$disabled = '';
	} else {
		$disabled = ' disabled';
	}
	
	$url = esc_url( $url );
	
	$allowed_targets = array( '_blank', '_self', '_parent', '_top' );
	if( substr( $target, 0, 1 ) !== '_' ) {
		$target = '_' . $target;
	}
	if( ! in_array( $target, $allowed_targets ) ) {
		$target = '';
	}
	
	// EXIT if no content!
	if( empty( $content ) ) {
		return false;
	}
	
	// BUILD OUTPUT
	$output = '<!-- MDL Button -->';
		
	$classes = 'mdl-button mdl-js-button';
	if ( 'none' !== $type ) {
		$classes .= sprintf( ' mdl-button--%s', $type );
	}
	if ( 'false' !== $color ) {
		$classes .= sprintf( ' mdl-button--%s', $color );
	}	
	if ( 'false' !== $effect ) {
		$classes .= sprintf( ' mdl-js-%s-effect', $effect );
	}
	
	$button = sprintf( '<button class="%s"%s>%s</button>', $classes, $disabled, $content );
	
	if( !empty($url) ) {
		$output .= '<a';
		if ( $target ) {
			$output .= sprintf( ' target="%s"', $target );
		}
		$output .= sprintf( ' href="%s">%s</a>', $url, $button );
	} else {
		$output .= $button;
	}
	
	return do_shortcode( $output );
}
// END Buttons




// START Cards
// TODO:
// add option to set mdl-card__title background image and color
//
// http://www.getmdl.io/components/index.html#cards-section
// https://www.google.com/design/spec/components/cards.html
//
// Post IDs Examples:
// [mdl-cards postid=480]
// [mdl-cards postid=480 title="Override this post title with this text. Ha Ha!"]
// [mdl-cards postid=480 menu="<a href='https://twitter.com/intent/tweet' target='_blank'><button class='mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect'><i class='material-icons'>share</i></button></a>"]
//
// Fully Manual (no Post IDs) Example:
// [mdl-cards title="Custom Title FOLKS!" subtitle="If you must..." media="<img src='http://www.getmdl.io/assets/demos/welcome_card.jpg'>" supporting="Thanks for your interest. This post has all the info you're wondering about." actions="<a href='http://www.getmdl.io/components/index.html#cards-section' target='_blank'>Plain link text here</a>" menu="<a href='https://twitter.com/intent/tweet' target='_blank'><button class='mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect'><i class='material-icons'>share</i></button></a>"]
//
// Grid Examples:
/*
	<div class="mdl-grid">
		[mdl-cards postid=480,436,418,241 shadow=2 class="mdl-cell mdl-cell--3-col"]
	</div>
	
	OR
	use shortcode grid with no spacing between cells
	
	[mdl-grid spacing=false]
		[mdl-cards postid=480,436,418,241 shadow=2 class="mdl-cell mdl-cell--3-col"]
	[/mdl-grid]
	
	
	NOTE: must add custom classes to Cards if entering multiple Post IDs because that would just result in 4 cards within a single 3-wide cell (i.e. missing the other 9-wide) -- but you could choose to break it out into 4 different card shortcodes, like this:
	[mdl-grid]
		[mdl-cell size=3][mdl-cards postid=480 shadow=2][/mdl-cell]
		[mdl-cell size=3][mdl-cards postid=436 shadow=2][/mdl-cell]
		[mdl-cell size=3][mdl-cards postid=418 shadow=2][/mdl-cell]
		[mdl-cell size=3][mdl-cards postid=241 shadow=2][/mdl-cell]
	[/mdl-grid]
	
*/
//
add_shortcode( 'mdl-cards', 'mdl_cards_func' );
function mdl_cards_func( $atts ) {

	// Attributes
	$defaults = array(
		'postid' => '', // one or more comma-separated post IDs or any post type (should support featured image and excerpt
			'title' => '', // manual Title -- override Post Title or to create single card even if no Post ID arg
			'subtitle' => '', // NOT RECOMMENDED TO USE because it does not look good, which is probably why there are no demos for it
			'media' => '', // manual Media (typically img with 10px padding) -- override Featured Image or to create single card even if no Post ID arg
			'supporting' => '', // manual Supporting Text -- override Post Excerpt or to create single card even if no Post ID arg
		'actions' => '', // bottom text, link(s), button(s), etc. -- defaults to "Read More" if 'actionspostlink' is 'true' and 'actions' is empty
		'actionspostlink' => 'true', // if using 'postid' and this is true, link the 'actions' text to the post as a button -- otherwise, just put full 'a href' HTML in 'actions' argument
		'menu' => '', // upper-right corner, typically an icon type button
		'mediasize' => 'thumbnail', // get_the_post_thumbnail() default is 'post-thumbnail' which is large, unlike 'thumbnail'
		'mediapadding' => '10px', // valid CSS value (25px, 5%, 0, auto, -2em, ...)
		'shadow' => '', // anything other than valid value will result in no shadow
		'titleborder' => 'false', // border before the supporting area
		'actionsborder' => 'true', // border after the supporting area
		'htag' => 'h2', // h1-h6
		'class' => '', // e.g. 'mdl-cell mdl-cell--4-col' (then manually wrap this shortcode's output in .mdl-grid ) --> will result in 3 columns of cards
	);

	$atts = shortcode_atts( $defaults, $atts );
	
	$using_post_ids = 'false';
	
	$post_ids = $atts['postid'];
	$post_ids = mdl_only_integers_commas( $post_ids );
	
	if( ! empty( $post_ids ) ) {
		
		$post_ids = array_map( 'intval', array_filter( explode( ',' , $post_ids ), 'is_numeric' ) ); //array of post IDs stored as integers
		
		if( ! is_array( $post_ids ) ) {
			return '<!-- MDL Cards ERROR: No Post ID Array -->';
		}
		
		$post_ids = array_filter( $post_ids ); //remove empties from array so get valid COUNT
		
		$post_ids = array_unique( $post_ids, SORT_NUMERIC ); // compares items numerically, does not actually SORT or ORDER
		
		if( empty( $post_ids ) || 1 > count( $post_ids ) ) {
			return '<!-- MDL Cards ERROR: No Post IDs -->';
		}
		
		$using_post_ids = 'true'; // $post_ids is good so let's continue...
	}
	
	
	$title			=	$atts['title'];
	$subtitle		=	$atts['subtitle'];
	$media			=	$atts['media'];
	$supporting		=	$atts['supporting'];
	$actions		=	$atts['actions'];
	$actionslink	=	strtolower( $atts['actionspostlink'] );
	$fimage_size	=	strtolower( $atts['mediasize'] );
	$media_padding	=	mdl_restrict_css_unit_value( $atts['mediapadding'], '10px' );
	$menu			=	$atts['menu'];
	$shadow			=	$atts['shadow'];
	$title_border	=	strtolower( $atts['titleborder'] );
	$actions_border	=	strtolower( $atts['actionsborder'] );
	$htag			=	strtolower( $atts['htag'] );
	if( function_exists( 'mdl_sanitize_html_classes' ) ) {
		$class = mdl_sanitize_html_classes( $atts['class'] );
	} else {
		$class = sanitize_html_class( $atts['class'] );
	}
	
	// SANITIZE INPUT
	
	// WARNING: we are NOT sanitizing $title, $subtitle, $media, $supporting, $actions, or $menu because they likely have HTML in them.
	
	if( ! in_array( $fimage_size, get_intermediate_image_sizes() ) ) {
		$fimage_size = ''; // WP defaults to post-thumbnail
	}
	
	$actionslink = mdl_truefalse( $actionslink, 'true' );
	
	$allowed_shadows = array( '2', '3', '4', '6', '8', '16' );
	if( ! in_array( $shadow, $allowed_shadows ) ) {
		$shadow = '';
	}
	
	$title_border = mdl_truefalse( $title_border, 'true' );
	
	$actions_border = mdl_truefalse( $actions_border, 'true' );
	
	$htag = mdl_htag_default_h2( $htag, 'h2' );
	
	
	// BUILD OUTPUT
	$output = '<!-- MDL Cards -->';
	
	$outer_classes = 'mdl-card';
	if( $shadow ) {
		$outer_classes .= sprintf( ' mdl-shadow--%sdp', $shadow );
	}

	if( $class ) {
		$outer_classes .= ' ' . $class;
	}
	
	if( 'true' == $title_border ) {
		$title_border_class = ' mdl-card--border';
	} else {
		$title_border_class = '';
	}
	
	if( 'true' == $actions_border ) {
		$actions_border_class = ' mdl-card--border';
	} else {
		$actions_border_class = '';
	}
	
	if( 'true' == $using_post_ids ) {
		// https://developer.wordpress.org/reference/functions/get_post/
		// https://codex.wordpress.org/Class_Reference/WP_Post
		// https://codex.wordpress.org/Function_Reference/get_post_status
		
		foreach ( $post_ids as $post_id ) {
			$post_data = get_post( $post_id );
			
			$post_status = get_post_status ( $post_id );
			$allowed_statuses = array( 'publish' ); // maybe this ---- array( 'publish', 'future', 'private' );
			if( ! in_array( $post_status, $allowed_statuses ) ) {
				$output .= sprintf( '<!-- Post ID %s is in a disallowed status -->', $post_id );
				break;
			}
			
			$post_title = $title ? $title : $post_data->post_title;
			
			$post_media = $media ? $media : '';
			if( empty( $post_media ) && has_post_thumbnail( $post_id ) ) {
				if( $fimage_size ) {
					$post_media = get_the_post_thumbnail( $post_id, $fimage_size );
				} else {
					$post_media = get_the_post_thumbnail( $post_id );
				}
			}
			
			$post_supporting = $supporting ? $supporting : $post_data->post_excerpt;
			
			if( 'true' == $actionslink ) {
				
				$post_url = esc_url( get_permalink( $post_id ) );
				
				if( empty( $actions ) ) {
					$post_actions_text = 'Read More';
				} else {
					$post_actions_text = $actions;
				}
				
				$post_actions = sprintf( '[mdl-button color=accent url="%s"]%s[/mdl-button]', $post_url, $post_actions_text );
			
			} else {
				$post_actions = $actions;
			}
			
			$output .= sprintf('<div id="mdl-card-post-id-%d" class="%s">', $post_id, $outer_classes );
			
			if( $post_title ) {
				$output .= sprintf('<div class="mdl-card__title%s"><%s>%s</%s>', $title_border_class, $htag, $post_title, $htag );
				if( $subtitle ) {
					$output .= sprintf('<p class="mdl-card__subtitle-text">%s</p>', $subtitle );
				}
				$output .= '</div>';
			
			}
			
			if( $post_media ) {
				// added mdl-typography--text-center to text-align:center the img
				$output .= sprintf('<div class="mdl-card__media mdl-typography--text-center" style="padding: %s">%s</div>', $media_padding, $post_media );
			}
			
			if( $post_supporting ) {
				$output .= sprintf('<div class="mdl-card__supporting-text">%s</div>', $post_supporting );
			}
			
			if( $post_actions ) {
				$output .= sprintf('<div class="mdl-card__actions%s">%s</div>', $actions_border_class, $post_actions );
			}
			
			if( $menu ) {
				$output .= sprintf('<div class="mdl-card__menu">%s</div>', $menu );
			}
			
			$output .= '</div>';
			
		}
		
	} else {
		if( empty( $title ) && empty( $subtitle ) && empty( $media ) && empty( $menu ) ) {
			return '<!-- MDL Cards ERROR: requires Title, Subtitle, Media, or Menu -->';
		}
		
		if( empty( $supporting ) && empty( $actions ) ) {
			return '<!-- MDL Cards ERROR: requires Supporting and Actions -->';
		}
		
		$output .= sprintf('<div class="%s">', $outer_classes );
		
		if( $title ) {
			$output .= sprintf('<div class="mdl-card__title%s"><%s>%s</%s>', $title_border_class, $htag, $title, $htag );
			if( $subtitle ) {
				$output .= sprintf('<p class="mdl-card__subtitle-text">%s</p>', $subtitle );
			}
			$output .= '</div>';
		
		}
		
		if( $media ) {
			// added mdl-typography--text-center to text-align:center the img
			$output .= sprintf('<div class="mdl-card__media mdl-typography--text-center">%s</div>', $media );
		}
		
		if( $supporting ) {
			$output .= sprintf('<div class="mdl-card__supporting-text">%s</div>', $supporting );
		}
		
		if( $actions ) {
			$output .= sprintf('<div class="mdl-card__actions%s">%s</div>', $actions_border_class, $actions );
		}
		
		if( $menu ) {
			$output .= sprintf('<div class="mdl-card__menu">%s</div>', $menu );
		}
		
		$output .= '</div>';

	}
	
		
	return do_shortcode( $output );
}
// END Cards




// START Grid
// 
// FYI: Must start/end mdl-grid for EACH ROW (i.e. after mdl-cell sizes total 12)
//
// http://www.getmdl.io/components/index.html#layout-section/grid
//
// $content should include shortcodes to create cells (e.g. mdl-cell) or nested rows (e.g. mdl-grid-a)
//
// Examples:
// See examples for [mdl-cell]
//
add_shortcode( 'mdl-grid', 'mdl_grid_func' );
// allow Grids within Grids by using different shortcodes but same functionality
add_shortcode( 'mdl-grid-a', 'mdl_grid_func' );
add_shortcode( 'mdl-grid-b', 'mdl_grid_func' );
add_shortcode( 'mdl-grid-c', 'mdl_grid_func' );
add_shortcode( 'mdl-grid-d', 'mdl_grid_func' );
function mdl_grid_func( $atts, $content = null ) {

	// Attributes
	$defaults = array(
		'spacing' => '', // 'false' adds .mdl-grid--no-spacing to remove spacing between cells
		'class' => '',
	);

	$atts = shortcode_atts( $defaults, $atts );
	
	// SANITIZE INPUT
	
	$spacing = mdl_truefalse( $atts['spacing'], 'true' );
	
	if( function_exists( 'mdl_sanitize_html_classes' ) ) {
		$class = mdl_sanitize_html_classes( $atts['class'] );
	} else {
		$class = sanitize_html_class( $atts['class'] );
	}
	
		
	// cannot if we want to allow HTML like <i> icons
	// $content = esc_html( $content );
	
	
	// EXIT if no content!
	if( empty( $content ) ) {
		return false;
	}
	
	// BUILD OUTPUT
	$output = '<!-- MDL Grid -->';
		
	$classes = 'mdl-grid';
	if ( 'false' == $spacing ) {
		$classes .= ' mdl-grid--no-spacing';
	}
	
	$output .= sprintf( '<div class="%s">%s</div>', $classes, $content );
	
	return do_shortcode( $output );
}
// END Grid



// START Cell
// TODO:
// if inside mdl-grid and mdl-cell, make equal height cards and/or make auto-width / flex: 1 card sizing --> https://github.com/google/material-design-lite/issues/1095#issuecomment-123973439
//
// http://www.getmdl.io/components/index.html#layout-section/grid
//
// FYI:
// It is on the USER to make sure they use mdl-cell properly to fill mdl-grid (e.g. 12 columns)
	// Default grid is based on 12 columns
// Device-specifics:
	// Desktop has 12 columns
	// Tablet has 8 columns
	// Phone has 4 columns
//
// Media Queries:
	// Phone: @media (max-width: 479px) {
	// Tablet: @media (min-width: 480px) and (max-width:839px) {
	// Desktop: @media (min-width: 840px) {
//
// $content should include whatever we want in a column/cell
//
// Full examples including use of [mdl-grid]:
//
// Example: 8+4, 3+3+3+3
/*
	[mdl-grid]
		[mdl-cell size=8]something here that will be 8 columns wide[/mdl-cell]
		[mdl-cell]something here that will be 4 columns wide, since 4 is the default size[/mdl-cell]
	[/mdl-grid]
	
	[mdl-grid]
		[mdl-cell size=3]1st quarter[/mdl-cell]
		[mdl-cell size=3]2nd quarter[/mdl-cell]
		[mdl-cell size=3]third quarter[/mdl-cell]
		[mdl-cell size=3]4th quarter[/mdl-cell]
	[/mdl-grid]
*/
//
//
// Example: Same as above except:
//		1) NO spacing between columns (i.e. "tight" or "closed" grid)
//		2) text-align: center on row 1 column 2
//		3) row 2 columns 1 and 2 are hidden on tablet
//		4) row 2 columns 3 and 4 are 50% width on tablet
/*
	[mdl-grid spacing=false]
		[mdl-cell size=8]something here that will be 8 columns wide[/mdl-cell]
		[mdl-cell text=center]something here that will be 4 columns wide, since 4 is the default size[/mdl-cell]
	[/mdl-grid]
	
	[mdl-grid spacing=false]
		[mdl-cell size=3 hide_tablet=true]1st quarter[/mdl-cell]
		[mdl-cell size=3 hide_tablet=true]2nd quarter[/mdl-cell]
		[mdl-cell size=3 size_tablet=4]third quarter[/mdl-cell]
		[mdl-cell size=3 size_tablet=4]4th quarter[/mdl-cell]
	[/mdl-grid]
*/
//
//
// Example: Grid within Grid (max allowed is 4 nested, 5 total grids, due to number of shortcodes)
/*
[mdl-grid]
		[mdl-cell-a size=8]
			[mdl-grid-a]
				[mdl-cell size=5]five wide inside the 8 wide, using nested grids![/mdl-cell]
				[mdl-cell size=7]seven wide inside the 8 wide[/mdl-cell]
			[/mdl-grid-a]
		[/mdl-cell-a]
		[mdl-cell]something here that will be 4 columns wide, since 4 is the default size[/mdl-cell]
	[/mdl-grid]
	
	[mdl-grid]
		[mdl-cell size=3]1st quarter[/mdl-cell]
		[mdl-cell size=3]2nd quarter[/mdl-cell]
		[mdl-cell size=3]third quarter[/mdl-cell]
		[mdl-cell size=3]4th quarter[/mdl-cell]
	[/mdl-grid]
*/
//
add_shortcode( 'mdl-cell', 'mdl_cell_func' );
// allow Grids within Grids by using different shortcodes but same functionality
add_shortcode( 'mdl-cell-a', 'mdl_cell_func' );
add_shortcode( 'mdl-cell-b', 'mdl_cell_func' );
add_shortcode( 'mdl-cell-c', 'mdl_cell_func' );
add_shortcode( 'mdl-cell-d', 'mdl_cell_func' );
function mdl_cell_func( $atts, $content = null ) {

	// Attributes
	$defaults = array(
		'size' => '', // 1 through 12 -- 4 (i.e. 1/3rd width) is the stylesheet's default
			//desktop, tablet, and phone overrides
			// e.g. <div class="mdl-cell mdl-cell--4-col mdl-cell--8-col-tablet">here</div>
			'size_desktop' => '', // 1-12
			'size_tablet' => '', // 1-8
			'size_phone' => '', // 1-4
		'hide_desktop' => 'false',
		'hide_tablet' => 'false',
		'hide_phone' => 'false',
		'align' => '', // stylesheet's default is 'stretch'
		'text'	=> '', // e.g. 'center' will center text within the cell
		'class' => '',
	);

	$atts = shortcode_atts( $defaults, $atts );
	
	$size 			= $atts['size'];
	$size_desktop 	= $atts['size_desktop'];
	$size_tablet 	= $atts['size_tablet'];
	$size_phone 	= $atts['size_phone'];
	$hide_desktop	= strtolower( $atts['hide_desktop'] );
	$hide_tablet	= strtolower( $atts['hide_tablet'] );
	$hide_phone		= strtolower( $atts['hide_phone'] );
	$align			= strtolower( $atts['align'] );
	$text			= strtolower( $atts['text'] );
	
	// SANITIZE INPUT
	
	
	// as strings instead of integers because shortcode args come as strings
	$allowed_desktop = array( '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12' );
	$allowed_tablet = array( '1', '2', '3', '4', '5', '6', '7', '8' );
	$allowed_phone = array( '1', '2', '3', '4' );
	
	if( ! in_array( $size, $allowed_desktop ) ) {
		$size = '';
	}
	
	if( ! in_array( $size_desktop, $allowed_desktop ) ) {
		$size_desktop = '';
	}
	
	if( ! in_array( $size_tablet, $allowed_tablet ) ) {
		$size_tablet = '';
	}
	
	if( ! in_array( $size_phone, $allowed_phone ) ) {
		$size_phone = '';
	}
	
	$hide_desktop = mdl_truefalse( $hide_desktop, 'false' );
	$hide_tablet = mdl_truefalse( $hide_tablet, 'false' );
	$hide_phone = mdl_truefalse( $hide_phone, 'false' );
	
	$allowed_alignments = array( 'stretch', 'top', 'middle', 'bottom' );
	if( ! in_array( $align, $allowed_alignments ) ) {
		$align = '';
	}
	
	$allowed_text = array( 'left', 'right', 'center', 'justify', 'nowrap', 'lowercase', 'uppercase', 'capitalize' );
	if( ! in_array( $text, $allowed_text ) ) {
		$text = '';
	}
	
	if( function_exists( 'mdl_sanitize_html_classes' ) ) {
		$class = mdl_sanitize_html_classes( $atts['class'] );
	} else {
		$class = sanitize_html_class( $atts['class'] );
	}
	
	
	// cannot if we want to allow HTML like <i> icons
	// $content = esc_html( $content );
	
	
	// Do NOT EXIT if no content because might just want to take up the space!
	if( empty( $content ) ) {
		//return false;
	}
	
	// BUILD OUTPUT
	$output = '<!-- MDL Cell -->';
		
	$classes = 'mdl-cell';
	if ( $size ) {
		$classes .= sprintf( ' mdl-cell--%s-col', $size );
	}
	
	// DOUBLE-ADDED desktop, tablet, and phone classes in case of
	// .mdl-grid--no-spacing>.mdl-cell--1-col-desktop.mdl-cell--1-col-desktop {
    //    width: 8.33333%
    // }
    
	if ( $size_desktop ) {
		$desktop_class = sprintf( 'mdl-cell--%s-col-desktop', $size_desktop );
		$classes .= sprintf( ' %1$s %1$s', $desktop_class );
	}
	if ( $size_tablet ) {
		$tablet_class = sprintf( 'mdl-cell--%s-col-tablet', $size_tablet );
		$classes .= sprintf( ' %1$s %1$s', $tablet_class );
	}
	if ( $size_phone ) {
		$phone_class = sprintf( 'mdl-cell--%s-col-phone', $size_phone );
		$classes .= sprintf( ' %1$s %1$s', $phone_class );
	}
	
	if ( 'true' == $hide_desktop ) {
		$classes .= ' mdl-cell--hide-desktop';
	}
	if ( 'true' == $hide_tablet ) {
		$classes .= ' mdl-cell--hide-tablet';
	}
	if ( 'true' == $hide_phone ) {
		$classes .= ' mdl-cell--hide-phone';
	}
	
	if ( $align ) {
		$classes .= sprintf( ' mdl-cell--%s', $align );
	}
	
	if ( $text ) {
		$classes .= sprintf( ' mdl-typography--text-%s', $text );
	}
	
	
	$output .= sprintf( '<div class="%s">%s</div>', $classes, $content );
	
	return do_shortcode( $output );
}
// END Cell




// START Tabs Container
//
// http://www.getmdl.io/components/index.html#layout-section/tabs
//
// $content should include shortcodes to create tabs content
//
// Full Example:
/*
	[mdl-tabs]
	
	[mdl-tabs-bar]
		[mdl-tabs-title id=1 active=true]Tab One[/mdl-tabs-title]
		[mdl-tabs-title id=deux]Deux[/mdl-tabs-title]
		[mdl-tabs-title id=three]3[/mdl-tabs-title]
		[mdl-tabs-title id=4]Four[/mdl-tabs-title]
	[/mdl-tabs-bar]
	
	[mdl-tabs-panel id=1 active=true]Can't complain[/mdl-tabs-panel]
	[mdl-tabs-panel id=deux]I believe we'll be ok[/mdl-tabs-panel]
	[mdl-tabs-panel id=three]We'll be ok[/mdl-tabs-panel]
	[mdl-tabs-panel id=4]Flux capacitor[/mdl-tabs-panel]
	
	[/mdl-tabs]
*/
//
//
add_shortcode( 'mdl-tabs', 'mdl_tabs_func' );
function mdl_tabs_func( $atts, $content = null ) {

	// Attributes
	$defaults = array(
		'effect' => 'true', // 'true' is same as 'ripple' because it is the only existing effect
		'class' => '',
	);

	$atts = shortcode_atts( $defaults, $atts );
	
	$effect = strtolower( $atts['effect'] );
	
	// SANITIZE INPUT
	
		
	$allowed_effects = array( 'false', 'ripple' );
	if( 'true' == $effect ) {
		$effect = 'ripple';
	}
	if( ! in_array( $effect, $allowed_effects ) ) {
		$effect = 'ripple';
	}
	
		
	if( function_exists( 'mdl_sanitize_html_classes' ) ) {
		$class = mdl_sanitize_html_classes( $atts['class'] );
	} else {
		$class = sanitize_html_class( $atts['class'] );
	}
	
	
	// cannot if we want to allow HTML like <i> icons
	// $content = esc_html( $content );
	
	
	// EXIT if no content !
	if( empty( $content ) ) {
		return false;
	}
	
	// BUILD OUTPUT
	$output = '<!-- MDL Tabs -->';
		
	$classes = 'mdl-tabs';
	
	if ( 'false' !== $effect ) {
		$classes .= sprintf( ' mdl-js-tabs mdl-js-%s-effect', $effect );
	}
	
	if ( $class ) {
		$classes .= ' ' . $class;
	}
	
	
	$output .= sprintf( '<div class="%s">%s</div>', $classes, $content );
	
	return do_shortcode( $output );
}
// END Tabs Container



// START Tabs Bar (contains Titles)
//
// http://www.getmdl.io/components/index.html#layout-section/tabs
//
// $content should include mdl-tabs-titles
//
// Examples:
// See examples for [mdl-tabs]
//
//
add_shortcode( 'mdl-tabs-bar', 'mdl_tabs_bar_func' );
function mdl_tabs_bar_func( $atts, $content = null ) {

	// Attributes
	$defaults = array(
		'class' => '',
	);

	$atts = shortcode_atts( $defaults, $atts );
	
	// SANITIZE INPUT
		
	if( function_exists( 'mdl_sanitize_html_classes' ) ) {
		$class = mdl_sanitize_html_classes( $atts['class'] );
	} else {
		$class = sanitize_html_class( $atts['class'] );
	}
	
	
	// BUILD OUTPUT
	$output = '<!-- MDL Tabs Bar -->';
		
	$classes = 'mdl-tabs__tab-bar';
	
	if ( $class ) {
		$classes .= ' ' . $class;
	}
	
	
	$output .= sprintf( '<div class="%s">%s</div>', $classes, $content );
	
	return do_shortcode( $output );
}
// END Tabs Bar (contains Titles)



// START Tabs Title
//
// http://www.getmdl.io/components/index.html#layout-section/tabs
//
// $content should included the desired Tab Title
//
// Examples:
// See examples for [mdl-tabs]
//
add_shortcode( 'mdl-tabs-title', 'mdl_tabs_title_func' );
function mdl_tabs_title_func( $atts, $content = null ) {

	// Attributes
	$defaults = array(
		'id' => '', // must match ID set in mdl-tabs-panel to work -- gets run through !empty() so do NOT use '0'
		'active' => '', // set ONE in the mdl-tabs-bar group to 'true' to make one of the tabs open by default -- if none set as active, none of the tabs will be visually distinctive as active -- make sure it is the same ID as the panel set to active
		'class' => '',
	);

	$atts = shortcode_atts( $defaults, $atts );
	
	$active = strtolower( $atts['active'] );
	
	
	// SANITIZE INPUT
		
	$id = sanitize_html_class( $atts['id'] );
	
	$active = mdl_truefalse( $active, 'false' );
	
	
	if( function_exists( 'mdl_sanitize_html_classes' ) ) {
		$class = mdl_sanitize_html_classes( $atts['class'] );
	} else {
		$class = sanitize_html_class( $atts['class'] );
	}
	
	
	// cannot if we want to allow HTML like <i> icons
	// $content = esc_html( $content );
	
	
	// EXIT if no content !
	if( empty( $content ) ) {
		return '<!-- MDL Tabs Title ERROR: No Title -->';
	}
	
	// EXIT if no ID !
	if( empty( $id ) ) {
		return '<!-- MDL Tabs Title ERROR: No ID -->';
	}
	
	// BUILD OUTPUT
	$output = '<!-- MDL Tabs Title -->';
		
	$classes = 'mdl-tabs__tab';
	
	if ( 'true' == $active ) {
		$classes .= ' is-active';
	}
	
	if ( $class ) {
		$classes .= ' ' . $class;
	}
	
	
	$output .= sprintf( '<a href="#panel-%s" class="%s">%s</a>', $id, $classes, $content );
	
	return do_shortcode( $output );
}
// END Tabs Title





// START Tabs Panel/Content
//
// http://www.getmdl.io/components/index.html#layout-section/tabs
//
// FYI:
// It is on the USER to make sure they use mdl-tabs-title and mdl-tabs-panel properly to fill mdl-tabs
//
// $content should include whatever content we want
//
// Examples:
// See examples for [mdl-tabs]
//
add_shortcode( 'mdl-tabs-panel', 'mdl_tabs_panel_func' );
function mdl_tabs_panel_func( $atts, $content = null ) {

	// Attributes
	$defaults = array(
		'id' => '', // must match ID set in mdl-tabs-title to work -- gets run through !empty() so do NOT use '0'
		'active' => '',  // set ONE of the mdl-tabs-panel to 'true' to make one of the panels displayed by default -- if none set as active, none of the panels will be displayed until one of the tab titles is clicked -- make sure it is the same ID as the mdl-tabs-title set to active
		'class' => '',
	);

	$atts = shortcode_atts( $defaults, $atts );
	
	$id = sanitize_html_class( $atts['id'] );
	$active = strtolower( $atts['active'] );
	
	
	// SANITIZE INPUT
		
	$active = mdl_truefalse( $active, 'false' );
	
		
	if( function_exists( 'mdl_sanitize_html_classes' ) ) {
		$class = mdl_sanitize_html_classes( $atts['class'] );
	} else {
		$class = sanitize_html_class( $atts['class'] );
	}
	
	
	// cannot if we want to allow HTML like <i> icons
	// $content = esc_html( $content );
	
	
	// EXIT if no content !
	if( empty( $content ) ) {
		return '<!-- MDL Tabs Panel ERROR: No content -->';
	}
	
	// EXIT if no ID !
	if( empty( $id ) ) {
		return '<!-- MDL Tabs Panel ERROR: No ID -->';
	}
	
	// BUILD OUTPUT
	$output = '<!-- MDL Tabs Panel -->';
		
	$classes = 'mdl-tabs__panel';
	
	if ( 'true' == $active ) {
		$classes .= ' is-active';
	}
	
	if ( $class ) {
		$classes .= ' ' . $class;
	}
	
	
	$output .= sprintf( '<div id="panel-%s" class="%s">%s</div>', $id, $classes, $content );
	
	return do_shortcode( $output );
}
// END Tabs Panel/Content




