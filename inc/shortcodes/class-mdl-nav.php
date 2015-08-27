<?php

namespace MDL_Shortcodes\Shortcodes;

class MDL_Nav extends Shortcode {

// NOTES:
//
// http://www.getmdl.io/components/index.html#layout-section/layout
//
/*
TODO:
- allow icon in title or as title
- allow image as title (e.g. http://www.getmdl.io/templates/android-dot-com/ )
- figure out <main> -- reference: http://www.w3schools.com/tags/tag_main.asp
- layout parent/children like Android Drawer menu
- allow image area in Drawer, like Android
- add search area
- add more_vert button icon like Android
- Jetpack site logo	
*/

	public static function get_shortcode_ui_args() {
		return array(
			'label'          => esc_html__( 'MDL Navigation Menu', 'mdl-shortcodes' ),
			//'listItemImage'  => 'dashicons-menu',
/*
			'inner_content' => array(
					//'value'			=> '',
					'description'	=> sprintf( esc_html__( 'Content you want displayed inside the %s element of the Navigation.', 'mdl-shortcodes' ), '<main>' ),
			),
*/
			'add_button'	 => 'icon_only',
			'attrs'          => array(
				array(
					'label'  => esc_html__( 'Title Text', 'mdl-shortcodes' ),
					'attr'   => 'title',
					'type'   => 'text',
					'description'  => esc_html__( 'Navigation Title Text', 'mdl-shortcodes' ),
					'meta' => array(
						'placeholder' => esc_html__( 'Default: Menu', 'mdl-shortcodes' ),
					),
				),
				array(
					'label'  => esc_html__( 'Nav Type', 'mdl-shortcodes' ),
					'attr'   => 'type',
					'type'   => 'select',
					'options' => parent::mdl_nav_types_selection_array(),
					'description'  => esc_html__( 'Default: Transparent Header, Collapsible Drawer.', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Header Nav', 'mdl-shortcodes' ),
					'attr'   => 'header',
					'type'   => 'select',
					'options' => parent::mdl_nav_menus_selection_array(),
					//'description'  => parent::mdl_icon_description_text( esc_html__( 'Display Icon inside Button', 'mdl-shortcodes' ) ),
				),
				array(
					'label'  => esc_html__( 'Drawer Nav', 'mdl-shortcodes' ),
					'attr'   => 'drawer',
					'type'   => 'select',
					'options' => parent::mdl_nav_menus_selection_array(),
					'description'  => esc_html__( 'If not set and Header Nav is set, will be the same as Header Nav. Otherwise will be None.', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Header Text Color', 'mdl-shortcodes' ),
					'attr'   => 'headercolor',
					'type'   => 'select',
					'options' => parent::mdl_color_palette_classes_selection_array( 'true', 'text' ),
					'description'  => parent::mdl_color_description_text(),
				),
/*
				array(
					'label'  => esc_html__( 'Header Background Color', 'mdl-shortcodes' ),
					'attr'   => 'headerbgcolor',
					'type'   => 'select',
					'options' => parent::mdl_color_palette_classes_selection_array( 'true', 'background' ),
					'description'  => parent::mdl_color_description_text( '', esc_html__( ' (Disallowed from choosing same color as Header Text color.)', 'mdl-shortcodes' ) ),
				),
*/
				array(
					'label'  => esc_html__( 'Drawer Text Color', 'mdl-shortcodes' ),
					'attr'   => 'drawercolor',
					'type'   => 'select',
					'options' => parent::mdl_color_palette_classes_selection_array( 'true', 'text' ),
					'description'  => parent::mdl_color_description_text(),
				),
/*
				array(
					'label'  => esc_html__( 'Drawer Background Color', 'mdl-shortcodes' ),
					'attr'   => 'drawerbgcolor',
					'type'   => 'select',
					'options' => parent::mdl_color_palette_classes_selection_array( 'true', 'background' ),
					'description'  => parent::mdl_color_description_text( '', esc_html__( ' (Disallowed from choosing same color as Drawer Text color.)', 'mdl-shortcodes' ) ),
				),
*/
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
			'title'			=> '', 
			'type'			=> '',
			'header'		=> '',
			'drawer'		=> '',
			'headercolor'	=> '',
			//'headerbgcolor'	=> '',
			'drawercolor'	=> '',
			//'drawerbgcolor'	=> '',
			'class'			=> '',
		);
		
		$atts = shortcode_atts( $defaults, $atts, self::get_shortcode_tag() );
		
		$title			=	$atts['title'];
		$type			=	strtolower( $atts['type'] );
		$header_nav		=	intval( $atts['header'] );
		$drawer_nav		=	intval( $atts['drawer'] );
		
		// no Shortcake preview
		if( is_admin() ) {
			return '';
		}
		
		// MDL navs require <main>
/*
		if( empty( $content ) ) {
			return '';
		}
*/
		
		if( empty( $title ) ) {
			$title = 'Menu';
		}
		if( 'fixed-fixed' == $type ) {
			$header_title = '';
		} else {
			$header_title = sprintf( '<span class="mdl-layout-title">%s</span>', $title );
		}
		
		if( ! array_key_exists( $type, parent::mdl_nav_types_selection_array( 'false' ) ) ) {
			$type = 'transparent';
		}
		
		if( ! array_key_exists( $header_nav, parent::mdl_nav_menus_selection_array( 'false' ) ) ) {
			$header_nav = '';
		}
		
		if( ! array_key_exists( $drawer_nav, parent::mdl_nav_menus_selection_array( 'false' ) ) ) {
			$drawer_nav = '';
		}
		
		// no navs so nothing to output
		if( empty( $header_nav ) && empty( $drawer_nav ) ) {
			return '';
		}
		
		if( empty( $drawer_nav ) ) {
			$drawer_nav = $header_nav;
		}
		
		
		//
		// custom background color class styling was overridden by some of the $types selected, but not on transparent
		//
		$header_text = '';
		$header_background = '';
		
		// Header colors
		$header_text = sanitize_html_class( $atts['headercolor'] ); // not mdl_sanitize_html_classes() because only allowing one class
		if ( ! array_key_exists( $header_text, parent::mdl_color_palette_classes_selection_array( 'false', 'text' ) ) ) {
			$header_text = '';
		}
		
/*
		$header_background = sanitize_html_class( $atts['headerbgcolor'] ); // not mdl_sanitize_html_classes() because only allowing one class
		if ( ! array_key_exists( $header_background, parent::mdl_color_palette_classes_selection_array( 'false', 'background' ) ) ) {
			$header_background = '';
		}
		
		// disallow background color being same as text color
		if ( 'true' == parent::mdl_text_background_colors_same( $header_text, $header_background ) ) {
			$header_background = '';
		}
*/
		
		
		$drawer_text = '';
		$drawer_background = '';
		
		// Drawer colors
		$drawer_text = sanitize_html_class( $atts['drawercolor'] ); // not mdl_sanitize_html_classes() because only allowing one class
		if ( ! array_key_exists( $drawer_text, parent::mdl_color_palette_classes_selection_array( 'false', 'text' ) ) ) {
			$drawer_text = '';
		}
		
/*
		$drawer_background = sanitize_html_class( $atts['drawerbgcolor'] ); // not mdl_sanitize_html_classes() because only allowing one class
		if ( ! array_key_exists( $drawer_background, parent::mdl_color_palette_classes_selection_array( 'false', 'background' ) ) ) {
			$drawer_background = '';
		}
		
		// disallow background color being same as text color
		if ( 'true' == parent::mdl_text_background_colors_same( $drawer_text, $drawer_background ) ) {
			$drawer_background = '';
		}
*/
				
		
		if( method_exists( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_sanitize_html_classes' ) ) {
			$class = parent::mdl_sanitize_html_classes( $atts['class'] );
		} else {
			// this will remove spaces so is faulty but better than not sanitizing at all
			$class = sanitize_html_class( $atts['class'] );
		}
		
		// BUILD OUTPUT
		$output = '<!-- MDL Navigation -->';
		
		$classes = 'mdl-layout mdl-js-layout mdl-layout--overlay-drawer-button';
		
		$outer_class = '';
		
		$header_class = 'mdl-layout__header';
		$drawer_class = 'mdl-layout__drawer';
		
		if( ! empty( $type ) ) {
			if( 'transparent' == $type ) {
				$header_class .= ' mdl-layout__header--transparent';
			} elseif ( 'none-fixed' == $type ) {
				$header_class = ''; // clear it out since no header
				$outer_class .= ' mdl-layout--fixed-drawer';
			} elseif ( 'fixed' == $type ) {
				$outer_class .= ' mdl-layout--fixed-header';
			} elseif ( 'fixed-fixed' == $type ) {
				$outer_class .= ' mdl-layout--fixed-header';
				$outer_class .= ' mdl-layout--fixed-drawer';
			} elseif ( 'scrolling' == $type ) {
				$header_class .= ' mdl-layout__header--scroll';
			} elseif ( 'waterfall' == $type ) {
				$header_class .= ' mdl-layout__header--waterfall';
			} elseif ( 'scrollabletabs' == $type ) {
				$outer_class .= ' mdl-layout--fixed-header';
			} elseif ( 'fixedtabs' == $type ) {
				$outer_class .= ' mdl-layout--fixed-tabs';
			} else {
				return ''; // should not get here because a default type is set above
			}
		}
		
		if( ! empty( $header_background ) ) {
			$header_class .= ' ' . $header_background;
		}
		
		if( ! empty( $drawer_background ) ) {
			$drawer_class .= ' ' . $drawer_background;
		}
		
		$classes .= $outer_class;
		
/*
		if( $color_text ) {
			$classes .= ' ' . $color_text;
		}
		
		if( $color_background ) {
			$classes .= ' ' . $color_background;
		}
		
*/
		if( $class ) {
			$classes .= ' ' . $class;
		}
		
		$build_header = '';
		if( ! empty( $header_class ) ) {
			$build_header = sprintf( '
				<header class="%s">
					<div class="mdl-layout-icon"></div>
					<div class="mdl-layout__header-row">
						%s
						<div class="mdl-layout-spacer"></div>
						%s
					</div>
				</header>',
				$header_class,
				$header_title,
				parent::mdl_build_nav_menu_items( $header_nav, $header_text )
			);
		}
		
		$build_drawer = '';
		if( ! empty( $drawer_class ) ) {
			$build_drawer = sprintf( '
				<div class="%s">
					<span class="mdl-layout-title">%s</span>
					%s
				</div>',
				$drawer_class,
				$title,
				parent::mdl_build_nav_menu_items( $drawer_nav, $drawer_text )
			);
		}
				
		$output .= sprintf( '<div class="%s">', $classes );
			$output .= $build_header;
			$output .= $build_drawer;
			//$output .= sprintf( '<main class="mdl-layout__content">%s</main>', $content );
		$output .= '</div>';
			
		return do_shortcode( $output );
	
	}
	
}
