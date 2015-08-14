<?php

namespace MDL_Shortcodes\Shortcodes;

class MDL_Menu extends Shortcode {

// NOTES:
//
// http://www.getmdl.io/components/index.html#menus-section
// https://www.google.com/design/spec/components/menus.html
//
//


	public static function get_shortcode_ui_args() {
		return array(
			'label'          => esc_html__( 'MDL Menu Button', 'mdl-shortcodes' ),
			'listItemImage'  => 'dashicons-plus-alt',
			'add_button'	 => 'icon_only',
			'attrs'          => array(
				array(
					'label'  => esc_html__( 'Menu Button Nav', 'mdl-shortcodes' ),
					'attr'   => 'nav',
					'type'   => 'select',
					'options' => parent::mdl_nav_menus_selection_array(),
					'description'  => esc_html__( 'Enter a WordPress Navigation Menu\'s ID and a Button Menu will be created. If entered, this shortcode\'s manually-entered arguments (like url, target, etc.) will be ignored, and the Icon will default to more_vert.', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Menu Position', 'mdl-shortcodes' ),
					'attr'   => 'position',
					'type'   => 'select',
					'options' => parent::mdl_menu_positions_selection_array(),
					'description'  => esc_html__( 'Menu position in relation to the Menu Button. Default: Lower Left.', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Icon', 'mdl-shortcodes' ),
					'attr'   => 'icon',
					'type'   => 'select',
					'options' => parent::mdl_icons_selection_array(),
					'description'  => parent::mdl_icon_description_text( 'Default: more_vert. ' ),
				),
				array(
					'label'  => esc_html__( 'Menu Item Click Effect', 'mdl-shortcodes' ),
					'attr'   => 'effect',
					'type'   => 'select',
					'options' => parent::mdl_ripple_effect_array(),
					'description'  => esc_html__( 'Default: Ripple', 'mdl-shortcodes' ),
				),
				array(
					'label'			=> esc_html__( 'Menu Button ID', 'mdl-shortcodes' ),
					'attr'			=> 'id',
					'type'			=> 'text',
					'description'	=> esc_html__( 'If left blank, one will be automatically generated for you.', 'mdl-shortcodes' ),
					'meta'			=> array(
						'placeholder' => esc_html__( 'my-class-1 other-custom-class', 'mdl-shortcodes' ),
					),
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
			'nav'			=> '',
			'position'		=> '',
			'icon'			=> '',
			'effect'		=> 'ripple', // 'true' is same as 'ripple' because it is the only existing effect
			'id'			=> '',
			'class'			=> '',
		);
		
		$atts = shortcode_atts( $defaults, $atts, self::get_shortcode_tag() );
		
		$nav		=	intval( $atts['nav'] );
		$position	=	strtolower( $atts['position'] );
		$icon		=	strtolower( $atts['icon'] );
		$effect		=	strtolower( $atts['effect'] );
		$id			=	$atts['id'];
		
		
		// Invalid Nav Menu
		if( ! array_key_exists( $nav, parent::mdl_nav_menus_selection_array( 'false' ) ) ) {
			$nav = '';
		}
		
		// Invalid Menu Position
		if( ! array_key_exists( $position, parent::mdl_menu_positions_selection_array( 'false' ) ) ) {
			$position = '';
		}
		
		// bail if no Nav
		if( empty( $nav ) ) {
			return '';
		}
		
		// Invalid Icon Name
		if ( ! array_key_exists( $icon, parent::mdl_icons_selection_array( 'false' ) ) ) {
			$icon = '';
		}
		
		if( empty( $icon ) ) {
			$icon = 'more_vert';
		}
		
		$icon = sprintf( '<i class="material-icons">%s</i>', $icon );
		
		if( 'true' == $effect ) {
			$effect = 'ripple';
		}
		if( ! array_key_exists( $effect, parent::mdl_ripple_effect_array() ) ) {
			$effect = 'ripple';
		}
		
		$id = sanitize_html_class( $id );
		if( empty( $id ) ) {
			$id = 'mdlmenuid-rand-' . rand( 101, 199 );
		}
		$id = sanitize_html_class( $id );
				
		if( method_exists( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_sanitize_html_classes' ) ) {
			$class = parent::mdl_sanitize_html_classes( $atts['class'] );
		} else {
			// this will remove spaces so is faulty but better than not sanitizing at all
			$class = sanitize_html_class( $atts['class'] );
		}
		
		// BUILD OUTPUT
		$output = '<!-- MDL Menu -->';
		
		$classes = 'mdl-button mdl-js-button mdl-button--icon';
		$ul_classes = 'mdl-menu mdl-js-menu';
		$li_classes = 'mdl-menu__item';
		
		if ( 'false' !== $effect ) {
			$ul_classes .= sprintf( ' mdl-js-%s-effect', $effect );
		}
		
		if ( ! empty( $position ) ) {
			$ul_classes .= ' ' . $position;
		}
		
		$menu = parent::mdl_build_menu_button_li_items($nav, $li_classes );
		if( empty( $menu ) ) { //false or no items
			return '';
		}
		
		if( $class ) {
			$classes .= ' ' . $class;
		}
		
		$output .= sprintf( '<button id="%s" class="%s">%s</button>', $id, $classes, $icon );
		$output .= sprintf( '<ul class="%s" for="%s">%s</ul>', $ul_classes, $id, $menu );
				
		// return do_shortcode( $output ); // no shortcodes here, boss!
		return $output;
	}
	
}
