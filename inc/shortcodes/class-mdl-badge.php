<?php

namespace MDL_Shortcodes\Shortcodes;

class MDL_Badge extends Shortcode {

// NOTES:
//
// http://www.getmdl.io/components/index.html#badges-section
//
// $badge_text could be text like "Inbox" or "Mood" or an icon
// Examples:
// [mdl-badge badge_text="Updates" data="4"]
// [mdl-badge icon=account_box data=4 type=link url="https://google.com/" target=blank]
//

	public static function get_shortcode_ui_args() {
		return array(
			'label'          => esc_html__( 'MDL Badge', 'mdl-shortcodes' ),
			'listItemImage'  => 'dashicons-welcome-comments',
			'add_button'	 => 'icon_only',
			'attrs'          => array(
				array(
					'label'  => esc_html__( 'Badge Type', 'mdl-shortcodes' ),
					'attr'   => 'type',
					'type'   => 'select',
					'options' => array(
						''	=> esc_html__( 'Span', 'mdl-shortcodes' ),
						'link'	=> __( 'Link <a>', 'mdl-shortcodes' ),
						'div'	=> esc_html__( 'Div', 'mdl-shortcodes' ),
					),
					'description'  => esc_html__( 'Type of HTML element (Default: Span)', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Display Badge over an Icon (Required unless using badgetext)', 'mdl-shortcodes' ),
					'attr'   => 'icon',
					'type'   => 'select',
					'options' => parent::mdl_icons_selection_array(),
					'description'  => parent::mdl_icon_description_text( 'If no Icon chosen here, Badge will be displayed over the text you enter for "badgetext". ' ),
				),
				array(
					'label'  => esc_html__( '(OR) Display Badge over Text', 'mdl-shortcodes' ),
					'attr'   => 'badgetext',
					'type'   => 'text',
					'description'  => esc_html__( 'Display Badge over this text. If you choose an Icon, this content will be ignored.', 'mdl-shortcodes' ),
					'meta' => array(
						'placeholder' => esc_html__( 'Example: Updates', 'mdl-shortcodes' ),
					),
				),
				array(
					'label'  => esc_html__( 'Badge Data (Required)', 'mdl-shortcodes' ),
					'attr'   => 'data',
					'type'   => 'text',
					'description'  => esc_html__( 'Content inside the Badge (3 or fewer characters). Typically a number.', 'mdl-shortcodes' ),
					'meta' => array(
						'placeholder' => esc_html__( 'Example: 12', 'mdl-shortcodes' ),
					),
				),
				array(
					'label'  => esc_html__( 'Link URL', 'mdl-shortcodes' ),
					'attr'   => 'url',
					'type'   => 'url',
					'description'  => esc_html__( 'Required if Link type, otherwise ignored.', 'mdl-shortcodes' ),
					'meta' => array(
						'placeholder' => __( 'http://' ),
					),
				),
				array(
					'label'  => esc_html__( 'Link Target', 'mdl-shortcodes' ),
					'attr'   => 'target',
					'type'   => 'select',
					'options' => parent::mdl_targets_selection_array(),
					'description'  => esc_html__( 'Optional if Link type, otherwise ignored.', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Disable Data Background?', 'mdl-shortcodes' ),
					'attr'   => 'databgoff',
					'type'   => 'checkbox',
					'description'  => esc_html__( 'The Theme sets the Badge text and background colors. Check this box to disable adding the background class/styling.', 'mdl-shortcodes' ),
				),
				array(
					'label'			=> esc_html__( 'Custom CSS Class(es)', 'mdl-shortcodes' ),
					'attr'			=> 'class',
					'type'			=> 'text',
					'description'	=> parent::mdl_classes_description_text('', '', 'Span, Link, or Div'),
					'meta'			=> array(
						'placeholder' => esc_html__( 'my-class-1 other-custom-class', 'mdl-shortcodes' ),
					),
				),
			),
		);
	}

	public static function callback( $atts, $content = '' ) {
				
		$defaults = array(
			'type'			=> 'span', // span, link, div
			'icon'			=> '',	// if $badgetext will be an icon, like 'account_box':
				// Example output: <div class="material-icons mdl-badge" data-badge="1">account_box</div>
			'badgetext'		=> '',
			'url'			=> '',
			'target'		=> '', // e.g. 'blank'
			'databgoff'		=> 'false', // badge background color has color or is transparent
			'datalimit' 	=> 3, // MDL recommends no more than 3 characters in the badge, but allowing user to override
			'data'			=> '', // what goes in the badge
			'class'			=> '',
		);
		
		$atts = shortcode_atts( $defaults, $atts, self::get_shortcode_tag() );
		
		$type		=	strtolower( $atts['type'] );
		$icon		=	strtolower( $atts['icon'] );
		$badge_text =	$atts['badgetext'];
		$url		=	esc_url( $atts['url'] );
		$target		=	parent::mdl_url_target( $atts['target'] );
		$data_bg_off =	strtolower( $atts['databgoff'] );
		$data_limit	=	$atts['datalimit']; // no Shortcake UI
		$data		=	$atts['data'];
		
		
		$allowed_types = array( 'span', 'link', 'div' );
		if( ! in_array( $type, $allowed_types ) ) {
			$type = 'span';
		}
				
		// Invalid Icon Name
		if ( ! array_key_exists( $icon, parent::mdl_icons_selection_array( 'false' ) ) ) {
			$icon = '';
		}
		
		// Override $badge_text with $icon
		if( $icon ) {
			$badge_text = $icon;
		}
		
		$badge_text = sanitize_text_field( $badge_text );
		
		// No Badge Text
		if( empty( $badge_text ) ) {
			return '';
		}
		
		$data_bg_off = parent::mdl_truefalse( $data_bg_off, 'false' );
		
		$data_limit = absint( $data_limit );
		if( $data_limit < 1 ) {
			$data_limit = 3;
		}
		
		$data = sanitize_text_field( $data );
		$data = substr( $data, 0, $data_limit );
		
		// No "data" attribute
		if( empty( $data ) ) {
			return '';
		}
		
		if( method_exists( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_sanitize_html_classes' ) ) {
			$class = parent::mdl_sanitize_html_classes( $atts['class'] );
		} else {
			// this will remove spaces so is faulty but better than not sanitizing at all
			$class = sanitize_html_class( $atts['class'] );
		}
		
		// BUILD OUTPUT
		$output = '<!-- MDL Badge -->';
		
		if ( 'link' == $type && empty($url) ) {
			$type = 'span';
		}
		
		$classes = 'mdl-badge';
		if ( $icon ) {
			$classes .= ' material-icons';
		}
		if ( 'true' == $data_bg_off ) {
			$classes .= ' mdl-badge--no-background';
		}
		
		if( $class ) {
			$classes .= ' ' . $class;
		}
		
		if( 'span' == $type ) {
			$output .= sprintf( '<span class="%s" data-badge="%s">%s</span>', $classes, $data, $badge_text );
		} elseif( 'div' == $type ) {
			$output .= sprintf( '<div class="%s" data-badge="%s">%s</div>', $classes, $data, $badge_text );
		} elseif( 'link' == $type ) {
			$output .= '<a';
			if ( $target ) {
				$output .= sprintf( ' target="%s"', $target );
			}
			$output .= sprintf( ' href="%s" class="%s" data-badge="%s">%s</a>', $url, $classes, $data, $badge_text );
		} else {
			// Invalid "type" attribute
			// Should not happen though
			return '';
		}
	
		return do_shortcode( $output );
	
	}
	
}
