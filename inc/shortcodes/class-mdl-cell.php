<?php

namespace MDL_Shortcodes\Shortcodes;

class MDL_Cell extends Shortcode {

// NOTES:
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
	
	public static function get_shortcode_ui_args() {
		return array(
			'label'          => esc_html__( 'MDL Cell (Columns inside MDL Grid)', 'mdl-shortcodes' ),
			'listItemImage'  => 'dashicons-image-rotate-left',
			'add_button'	 => 'icon_only',
			'inner_content' => array(
					'description'	=> esc_html__( 'REQUIRED. Any content can go inside here and it will be handled by the mdl-grid and mdl-cell properties you set.', 'mdl-shortcodes' ),
			),
			'attrs'          => array(
				array(
					'label'  => esc_html__( 'Size / Column Width', 'mdl-shortcodes' ),
					'attr'   => 'size',
					'type'   => 'select',
					'options' => parent::mdl_cell_sizes_array( 'desktop' ),
					'description'  => esc_html__( 'Default: 4', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Desktop Size/Width', 'mdl-shortcodes' ),
					'attr'   => 'desktop',
					'type'   => 'select',
					'options' => parent::mdl_cell_sizes_array( 'desktop' ),
					'description'  => esc_html__( 'Custom column sizing on devices greater than or equal to 840px', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Tablet Size/Width', 'mdl-shortcodes' ),
					'attr'   => 'tablet',
					'type'   => 'select',
					'options' => parent::mdl_cell_sizes_array( 'tablet' ),
					'description'  => esc_html__( 'Custom column sizing on devices 480px to 839px', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Phone Size/Width', 'mdl-shortcodes' ),
					'attr'   => 'phone',
					'type'   => 'select',
					'options' => parent::mdl_cell_sizes_array( 'phone' ),
					'description'  => esc_html__( 'Custom column sizing on devices less than 480px', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Hidden on Desktop', 'mdl-shortcodes' ),
					'attr'   => 'desktophide',
					'type'   => 'select',
					'options' => parent::mdl_true_false_selection_array( 'true', 'true', 'false' ),
					'description'  => esc_html__( 'If True, content will be hidden on Desktop-width devices.', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Hidden on Tablet', 'mdl-shortcodes' ),
					'attr'   => 'tablethide',
					'type'   => 'select',
					'options' => parent::mdl_true_false_selection_array( 'true', 'true', 'false' ),
					'description'  => esc_html__( 'If True, content will be hidden on Tablet-width devices.', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Hidden on Phone', 'mdl-shortcodes' ),
					'attr'   => 'phonehide',
					'type'   => 'select',
					'options' => parent::mdl_true_false_selection_array( 'true', 'true', 'false' ),
					'description'  => esc_html__( 'If True, content will be hidden on Phone-width devices.', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Cell Flexbox Alignment (CSS align-self)', 'mdl-shortcodes' ),
					'attr'   => 'align',
					'type'   => 'select',
					'options' => parent::mdl_cell_flex_align_array( 'true' ),
					'description'  => __( 'Override this Cell\'s CSS Flexbox alignment. Default: Stretch. Reference: https://css-tricks.com/almanac/properties/a/align-self/', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Cell Typography Text', 'mdl-shortcodes' ),
					'attr'   => 'text',
					'type'   => 'select',
					'options' => parent::mdl_typography_text_array( 'true' ),
					'description'  => __( '', 'mdl-shortcodes' ),
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
					'description'  => parent::mdl_color_description_text( '', ' (Disallowed from choosing same color as Text color.)' ),
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
			'size'			=> '',
			'desktop'		=> '',
			'tablet'		=> '',
			'phone'			=> '',
			'desktophide'	=> '',
			'tablethide'	=> '',
			'phonehide'		=> '',
			'align'			=> '',
			'text'			=> '',
			'color'			=> '',
			'bgcolor'		=> '',
			'class'			=> '',
		);
		
		$atts = shortcode_atts( $defaults, $atts, self::get_shortcode_tag() );
		
		$size 			= $atts['size'];
		$size_desktop 	= $atts['desktop'];
		$size_tablet 	= $atts['tablet'];
		$size_phone 	= $atts['phone'];
		$hide_desktop	= strtolower( $atts['desktophide'] );
		$hide_tablet	= strtolower( $atts['tablethide'] );
		$hide_phone		= strtolower( $atts['phonehide'] );
		$align			= strtolower( $atts['align'] );
		$text			= strtolower( $atts['text'] );
		
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
				
		if( ! array_key_exists( $size, parent::mdl_cell_sizes_array( 'desktop' ) ) ) {
			$size = '';
		}
		
		if( ! array_key_exists( $size_desktop, parent::mdl_cell_sizes_array( 'desktop' ) ) ) {
			$size_desktop = '';
		}
		
		if( ! array_key_exists( $size_tablet, parent::mdl_cell_sizes_array( 'tablet' ) ) ) {
			$size_tablet = '';
		}
		
		if( ! array_key_exists( $size_phone, parent::mdl_cell_sizes_array( 'phone' ) ) ) {
			$size_phone = '';
		}
		
		$hide_desktop = parent::mdl_truefalse( $hide_desktop, 'false' );
		$hide_tablet = parent::mdl_truefalse( $hide_tablet, 'false' );
		$hide_phone = parent::mdl_truefalse( $hide_phone, 'false' );
		
		if( ! array_key_exists( $align, parent::mdl_cell_flex_align_array( 'false' ) ) ) {
			$align = '';
		}
		
		if( ! array_key_exists( $text, parent::mdl_typography_text_array( 'false' ) ) ) {
			$text = '';
		}
		
		if( method_exists( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_sanitize_html_classes' ) ) {
			$class = parent::mdl_sanitize_html_classes( $atts['class'] );
		} else {
			$class = sanitize_html_class( $atts['class'] );
		}
		
		// we do NOT want Shortcake to render this shortcode, just to be able to edit it
		if( is_admin() ) {
			return '';
		}
		
		// no content!
		// but that's ok because sometimes it's just used as a spacer!
/*
		if( empty( $content ) ) {
			return '';
		}
*/
		
		// BUILD OUTPUT
		$output = '<!-- MDL Cell -->';
			
		$classes = 'mdl-cell';
		if ( $size ) {
			$classes .= sprintf( ' mdl-cell--%s-col', $size );
		}
		
		if ( $color_text ) {
			$classes .= ' ' . $color_text;
		}
		
		if ( $color_background ) {
			$classes .= ' ' . $color_background;
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


}