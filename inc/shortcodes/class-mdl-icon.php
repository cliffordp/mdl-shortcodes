<?php

namespace MDL_Shortcodes\Shortcodes;

class MDL_Icon extends Shortcode {

// NOTES:
//
// https://www.google.com/design/icons/
//
// Examples:
// [mdl-icon icon=zoom_in]
// [mdl-icon class="my-custom-size-10 my-other-custom-class" icon=wifi]
//
	
	public static function get_shortcode_ui_args() {
		return array(
			'label'          => esc_html__( 'MDL Material Design Icon', 'mdl-shortcodes' ),
			'listItemImage'  => 'dashicons-editor-textcolor',
			'add_button'	 => 'icon_only',
			'attrs'          => array(
				array(
					'label'  => esc_html__( 'Icon Name (required)', 'mdl-shortcodes' ),
					'attr'   => 'icon',
					'type'   => 'select',
					'options' => parent::mdl_icons_selection_array( 'false' ), // no BLANK option since an Icon is required
					'description'  => parent::mdl_icon_description_text(),
				),
				array(
					'label'  => esc_html__( 'Icon Color', 'mdl-shortcodes' ),
					'attr'   => 'color',
					'type'   => 'select',
					'options' => parent::mdl_color_palette_classes_selection_array( 'true', 'text' ),
					'description'  => parent::mdl_color_description_text(),
				),
				array(
					'label'  => esc_html__( 'Icon Background Color', 'mdl-shortcodes' ),
					'attr'   => 'bgcolor',
					'type'   => 'select',
					'options' => parent::mdl_color_palette_classes_selection_array( 'true', 'background' ),
					'description'  => parent::mdl_color_description_text( '', esc_html__( ' (Disallowed from choosing same color as Icon color to avoid displaying a colored square.)', 'mdl-shortcodes' ) ),
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
			'icon'		=> '',
			'color'		=> '',
			'bgcolor'	=> '',
			'class'		=> '',
		);
		
		$atts = shortcode_atts( $defaults, $atts, self::get_shortcode_tag() ); // shortcode_atts() does not cause PHP warning if $atts is not an array, unlike array_merge() -- plus it is required to make shortcode hookable -- more info at http://sumobi.com/how-to-filter-shortcodes-in-wordpress-3-6/
		
		$icon = strtolower( $atts['icon'] );
		
		// Missing Icon Selection
		if ( empty( $icon ) ) {
			return '';
		}
		
		// Invalid Icon Name
		if ( ! array_key_exists( $icon, parent::mdl_icons_selection_array( 'false' ) ) ) {
			return '';
		}
		
		$color_text = sanitize_html_class( $atts['color'] ); // not mdl_sanitize_html_classes() because only allowing one class
		if ( ! array_key_exists( $color_text, parent::mdl_color_palette_classes_selection_array( 'false', 'text' ) ) ) {
			$color_text = '';
		}
		
		$color_background = sanitize_html_class( $atts['bgcolor'] ); // not mdl_sanitize_html_classes() because only allowing one class
		if ( ! array_key_exists( $color_background, parent::mdl_color_palette_classes_selection_array( 'false', 'background' ) ) ) {
			$color_background = '';
		}
		
		// disallow background color being same as text color
		if ( 'true' == parent::mdl_text_background_colors_same( $color_text, $color_background ) ) {
			$color_background = '';
		}
		
		if( method_exists( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_sanitize_html_classes' ) ) {
			$class = parent::mdl_sanitize_html_classes( $atts['class'] );
		} else {
			// this will remove spaces so is faulty but better than not sanitizing at all
			$class = sanitize_html_class( $atts['class'] );
		}
		
		// BUILD OUTPUT
		$output = '<!-- MDL Icon -->';
			
		$classes = 'material-icons';
		
		if( $color_text ) {
			$classes .= ' ' . $color_text;
		}
		
		if( $color_background ) {
			$classes .= ' ' . $color_background;
		}
		
		if( $class ) {
			$classes .= ' ' . $class;
		}
		
		$output .= sprintf( '<i class="%s">%s</i>', $classes, $icon );
		
		return do_shortcode( $output );
	}


}
