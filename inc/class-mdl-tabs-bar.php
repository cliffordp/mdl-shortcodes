<?php

namespace MDL_Shortcodes\Shortcodes;

class MDL_Tabs_Bar extends Shortcode {

// NOTES:
//
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
	
	public static function get_shortcode_ui_args() {
		return array(
			'label'          => esc_html__( 'MDL Tabs Bar/Nav', 'mdl-shortcodes' ),
			'listItemImage'  => 'dashicons-portfolio',
			'add_button'	 => 'icon_only', // no UI so no add_button
			'inner_content' => array(
					//'value'			=> '[mdl-cell][/mdl-cell]',
					'description'	=> __( 'Enter [mdl-tabs-bar][/mdl-tabs-bar] and [mdl-tabs-panel][/mdl-tabs-panel] shortcodes inside.', 'mdl-shortcodes' ),
			),
			'attrs'          => array(
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
			'class'		=> '',
		);
		
		$atts = shortcode_atts( $defaults, $atts, self::get_shortcode_tag() );
		
		
		if( method_exists( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_sanitize_html_classes' ) ) {
			$class = parent::mdl_sanitize_html_classes( $atts['class'] );
		} else {
			$class = sanitize_html_class( $atts['class'] );
		}
		
// not needed since no UI for this shortcode
/*
		if( is_admin() ) {
			return '';
		}
*/
		
		// no content!
		if( empty( $content ) ) {
			return '';
		}
		
		// BUILD OUTPUT
		$output = '<!-- MDL Tabs Bar -->';
			
		$classes = 'mdl-tabs__tab-bar';
		
		if ( 'false' !== $effect ) {
			$classes .= sprintf( ' mdl-js-tabs mdl-js-%s-effect', $effect );
		}
		
		$output .= sprintf( '<div class="%s">%s</div>', $classes, $content );
		
		return do_shortcode( $output );
	}


}