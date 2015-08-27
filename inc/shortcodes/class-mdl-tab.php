<?php

namespace MDL_Shortcodes\Shortcodes;

class MDL_Tab extends Shortcode {

// NOTES:
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
	
	public static function get_shortcode_ui_args() {
		return array(
			'label'          => esc_html__( 'MDL Tab', 'mdl-shortcodes' ),
			'listItemImage'  => 'dashicons-category',
			'add_button'	 => 'icon_only',
			'inner_content' => array(
					//'value'			=> '',
					'description'	=> sprintf( esc_html__( '(REQUIRED) Tab Panel content. Make sure you wrap this %s inside %s !', 'mdl-shortcodes' ), '[mdl-tab][/mdl-tab]', '[mdl-tab-group][/mdl-tab-group]' ),
			),
			'attrs'          => array(
				array(
					'label'			=> esc_html__( 'Tab Titles', 'mdl-shortcodes' ),
					'attr'			=> 'title',
					'type'			=> 'text',
					'description'	=> esc_html__( 'Default: Tab', 'mdl-shortcodes' ),
					'meta'			=> array(
						'placeholder' => esc_html__( 'Student List', 'mdl-shortcodes' ),
					),
				),
				array(
					'label'  => esc_html__( 'Set as the initially-Active Tab', 'mdl-shortcodes' ),
					'attr'   => 'active',
					'type'   => 'select',
					'options' => parent::mdl_true_false_selection_array( 'true', 'true', 'false' ),
					'description'  => esc_html__( 'Make sure to set 1 Tab (and ONLY 1 Tab) as Active per Tab Group.', 'mdl-shortcodes' ),
				),
				array(
					'label'			=> esc_html__( 'Custom CSS Class(es)', 'mdl-shortcodes' ),
					'attr'			=> 'class',
					'type'			=> 'text',
					'description'	=> parent::mdl_classes_description_text( '', '', esc_html__( 'Span, Link, or Div', 'mdl-shortcodes' ) ),
					'meta'			=> array(
						'placeholder' => esc_html__( 'my-class-1 other-custom-class', 'mdl-shortcodes' ),
					),
				),
			),
		);
	}

	public static function callback( $atts, $content = '' ) {
				
		$defaults = array(
			'title'		=> 'Tab',
			'active'	=> '',
			'class'		=> '',
		);
		
		$atts = shortcode_atts( $defaults, $atts, self::get_shortcode_tag() );
		
		$title = $atts['title']; // not sanitizing to allow HTML (e.g. <b> tags)
		
		$active = strtolower( $atts['active'] );
		
		$active = parent::mdl_truefalse( $active, 'false' );
		if( 'true' == $active ) {
			$active = ' is-active ';
		} else {
			$active = ' ';
		}
		
		if( method_exists( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_sanitize_html_classes' ) ) {
			$class = parent::mdl_sanitize_html_classes( $atts['class'] );
		} else {
			$class = sanitize_html_class( $atts['class'] );
		}
		
/*
		if( $class ) {
			$class = ' ' . $class;
		}
*/
		
		$tab_id = strip_tags( $title );
		$tab_id = sanitize_html_class( $tab_id );
		
		if( empty( $tab_id ) ) {
			$tab_id = 'mdltabid-rand-' . rand( 101, 199 );
		}
		$tab_id = sanitize_html_class( $tab_id );		
				
		// no content!
		if( empty( $content ) ) {
			return '';
		}
		
		// BUILD OUTPUT
		$output = '<!-- MDL Tab -->'; // removed via [mdl-tab-group] parsing
		
		$title_classes = sprintf( '%s%smdl-tabs__tab', $class, $active ); // put custom and active classes first for [mdl-tab-group] parsing
		
		$panel_classes = sprintf( '%s%smdl-tabs__panel', $class, $active ); // put custom and active classes first for [mdl-tab-group] parsing
		
		
		// Build Title
		$output .= '<!-- mdl-tab-title-start -->'; // removed via [mdl-tab-group] parsing
		$output .= sprintf( '<a href="#panel-%s" class="%s">%s</a>', $tab_id, $title_classes, $title );
		// $output .= sprintf( '<a href="%s#panel-%s" class="%s">%s</a>', get_permalink(), $tab_id, $title_classes, $title ); // added get_permalink() to make URLs absolute -- issue with WP Customizer: https://core.trac.wordpress.org/ticket/23225 -- but then it still reloads entire page --> but should use https://kovshenin.com/2012/current-url-in-wordpress/ instead of get_permalink
		$output .= '<!-- mdl-tab-title-end -->'; // removed via [mdl-tab-group] parsing
		
		// Build Panel
		$output .= '<!-- mdl-tab-panel-start -->'; // removed via [mdl-tab-group] parsing
		$output .= sprintf( '<div id="panel-%s" class="%s">%s</div>', $tab_id, $panel_classes, $content );
		$output .= '<!-- mdl-tab-panel-end -->'; // removed via [mdl-tab-group] parsing
		
		return do_shortcode( $output );
	}


}