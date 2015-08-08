<?php

namespace MDL_Shortcodes\Shortcodes;

class MDL_Tabs extends Shortcode {

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
			'label'          => esc_html__( 'MDL Tabs Container', 'mdl-shortcodes' ),
			'listItemImage'  => 'dashicons-portfolio',
			'add_button'	 => 'icon_only', // no UI so no add_button
			'inner_content' => array(
					//'value'			=> '[mdl-cell][/mdl-cell]',
					'description'	=> __( 'Enter [mdl-tabs-bar][/mdl-tabs-bar] and [mdl-tabs-panel][/mdl-tabs-panel] shortcodes inside.', 'mdl-shortcodes' ),
			),
			'attrs'          => array(
				array(
					'label'  => esc_html__( 'Tabs Click Effect', 'mdl-shortcodes' ),
					'attr'   => 'effect',
					'type'   => 'select',
					'options' => parent::mdl_ripple_effect_array(),
					'description'  => esc_html__( 'Default: Ripple', 'mdl-shortcodes' ),
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
			'effect'	=> 'ripple',
			'class'		=> '',
		);
		
		$atts = shortcode_atts( $defaults, $atts, self::get_shortcode_tag() );
		
		
		$effect = strtolower( $atts['effect'] );
		
		if( 'true' == $effect ) {
			$effect = 'ripple';
		}
		if( ! array_key_exists( $effect, parent::mdl_ripple_effect_array() ) ) {
			$effect = 'ripple';
		}
		
		
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
		$output = '<!-- MDL Tabs -->';
			
		$classes = 'mdl-tabs';
		
		if ( 'false' !== $effect ) {
			$classes .= sprintf( ' mdl-js-tabs mdl-js-%s-effect', $effect );
		}
		
		$output .= sprintf( '<div class="%s">%s</div>', $classes, $content );
		
		return do_shortcode( $output );
	}


}