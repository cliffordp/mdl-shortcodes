<?php

namespace MDL_Shortcodes\Shortcodes;

class MDL_Grid extends Shortcode {

// NOTES:
// FYI: Must start/end mdl-grid for EACH ROW (i.e. after mdl-cell sizes total 12)
//
// http://www.getmdl.io/components/index.html#layout-section/grid
//
// $content should include shortcodes to create cells (e.g. mdl-cell) or nested rows (e.g. mdl-grid-a)
//
// Examples:
// See examples for [mdl-cell]
//
	
	public static function get_shortcode_ui_args() {
		return array(
			'label'          => esc_html__( 'MDL Grid (Single Row)', 'mdl-shortcodes' ),
			'listItemImage'  => 'dashicons-grid-view',
			//'add_button'	 => 'icon_only', // no UI so no add_button
			'inner_content' => array(
				//'value'		=> '[mdl-cell][/mdl-cell]',
				'description'	=> __( 'Enter [mdl-cell][/mdl-cell] shortcodes inside [mdl-grid][/mdl-grid] shortcodes.', 'mdl-shortcodes' ),
			),
			'attrs'          => array(
				array(
					'label'  => esc_html__( 'Spacing between Cells', 'mdl-shortcodes' ),
					'attr'   => 'spacing',
					'type'   => 'select',
					'options' => parent::mdl_true_false_selection_array( 'true', 'false', 'true' ),
					'description'  => esc_html__( 'False adds .mdl-grid--no-spacing to remove spacing between cells.', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Text Color', 'mdl-shortcodes' ),
					'attr'   => 'color',
					'type'   => 'select',
					'options' => parent::mdl_color_palette_classes_selection_array( 'true', 'text' ),
					'description'  => parent::mdl_color_description_text(),
				),
				array(
					'label'  => esc_html__( 'Background Color', 'mdl-shortcodes' ),
					'attr'   => 'bgcolor',
					'type'   => 'select',
					'options' => parent::mdl_color_palette_classes_selection_array( 'true', 'background' ),
					'description'  => parent::mdl_color_description_text( '', ' (Disallowed from choosing same color as Icon color to avoid displaying a colored square.)' ),
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
			'spacing'	=> '',
			'color'		=> '',
			'bgcolor'	=> '',
			'class'		=> '',
		);
		
		$atts = shortcode_atts( $defaults, $atts, self::get_shortcode_tag() );
		
		$spacing = parent::mdl_truefalse( $atts['spacing'], 'true' );
		
		$color_text = sanitize_html_class( strtolower( $atts['color'] ) ); // not mdl_sanitize_html_classes() because only allowing one class
		if ( ! array_key_exists( $color_text, parent::mdl_color_palette_classes_selection_array( 'false', 'text' ) ) ) {
			$color_text = '';
		}
		
		$color_background = sanitize_html_class( strtolower( $atts['bgcolor'] ) ); // not mdl_sanitize_html_classes() because only allowing one class
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
		
		$content = do_shortcode( $content );
		
		// BUILD OUTPUT
		$output = '<!-- MDL Grid -->';
			
		$classes = $class;
		
		if ( 'false' == $spacing ) {
			if( $classes ) {
				$classes .= ' ';
			}
			$classes .= 'mdl-grid--no-spacing';
		}
		
		if ( $color_text ) {
			if( $classes ) {
				$classes .= ' ';
			}
			$classes .= $color_text;
		}
		
		if ( $color_background ) {
			if( $classes ) {
				$classes .= ' ';
			}
			$classes .= $color_background;
		}
		
		if( $classes ) {
			$classes .= ' ';
		}
		$classes .= 'mdl-grid'; // put custom, color, and spacing classes first for P tag parsing
		
		$output .= sprintf( '<div class="%s">%s</div>', $classes, $content );
		
		// remove invalid P tags
		$output = str_replace( 'mdl-grid"></p>', 'mdl-grid">', $output );
		$output = str_replace( '<p><!-- MDL Cell -->', '<!-- MDL Cell -->', $output );
		$output = parent::mdl_cleanup_invalid_p_tags( $output );
		
		//return do_shortcode( $output ); // already did this above
		return $output;
	}


}