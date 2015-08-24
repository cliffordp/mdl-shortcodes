<?php

namespace MDL_Shortcodes\Shortcodes;

class MDL_Button extends Shortcode {

// NOTES:
//
// http://www.getmdl.io/components/index.html#buttons-section
// https://www.google.com/design/spec/components/buttons.html
//
// FAB = Floating Action Button
//
// $text could be text or an icon
// Examples:
// [mdl-button]Button Text Here[/mdl-button]
// [mdl-button type=fab color=true effect=false]X[/mdl-button]
// [mdl-button type=fab disabled=true]X[/mdl-button]
// [mdl-button type=icon][mdl-icon icon=add][/mdl-button]
	// above uses the 'mdl-icon' shortcode
// [mdl-button type=icon color=primary url="https://www.google.com/" target=blank]<i class="material-icons">mood</i>[/mdl-button]
	// above uses custom HTML
//


	public static function get_shortcode_ui_args() {
		return array(
			'label'          => esc_html__( 'MDL Button', 'mdl-shortcodes' ),
			'listItemImage'  => 'dashicons-migrate',
			'add_button'	 => 'icon_only',
			'attrs'          => array(
				array(
					'label'  => esc_html__( 'Button Type', 'mdl-shortcodes' ),
					'attr'   => 'type',
					'type'   => 'select',
					'options' => array(
						''			=> esc_html__( 'None / Flat', 'mdl-shortcodes' ),
						'raised'	=> esc_html__( 'Raised', 'mdl-shortcodes' ),
						'fab'		=> esc_html__( 'FAB (Floating Action Button) / Circle', 'mdl-shortcodes' ),
						'mini-fab'	=> esc_html__( 'Mini-FAB', 'mdl-shortcodes' ),
					),
					'description'  => esc_html__( 'Default: None.', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Icon as Button', 'mdl-shortcodes' ),
					'attr'   => 'icon',
					'type'   => 'select',
					'options' => parent::mdl_icons_selection_array(),
					'description'  => parent::mdl_icon_description_text( 'Display Icon inside Button' ),
				),
				array(
					'label'  => esc_html__( 'Icon Display', 'mdl-shortcodes' ),
					'attr'   => 'icondisplay',
					'type'   => 'select',
					'options' => array(
						''			=> esc_html__( 'Icon Button (plain circular with padding around icon)', 'mdl-shortcodes' ),
						'mini-icon'	=> esc_html__( 'Mini-Icon (plain circular tight to icon)', 'mdl-shortcodes' ),
						'nested'	=> esc_html__( 'Nested (to allow using Raised, FAB, or Mini-FAB type)', 'mdl-shortcodes' ),
					),
					'description'  => esc_html__( 'If using an Icon, how should the Icon be displayed?', 'mdl-shortcodes' ),
				),
				// FYI: Cannot do icon + text inside a button
				array(
					'label'  => esc_html__( '(OR) Button Text', 'mdl-shortcodes' ),
					'attr'   => 'text',
					'type'   => 'text',
					'description'  => esc_html__( 'Will be ignored if Icon is set, above.', 'mdl-shortcodes' ),
					'meta' => array(
						'placeholder' => esc_html__( 'Example: Click Here', 'mdl-shortcodes' ),
					),
				),
				array(
					'label'  => esc_html__( 'Button Color Scheme', 'mdl-shortcodes' ),
					'attr'   => 'color',
					'type'   => 'select',
					'options' => array(
						''			=> esc_html__( 'Colored / Default Scheme', 'mdl-shortcodes' ),
						'primary'	=> esc_html__( 'Primary', 'mdl-shortcodes' ),
						'accent'	=> esc_html__( 'Accent', 'mdl-shortcodes' ),
						'false'		=> esc_html__( 'None / Transparent', 'mdl-shortcodes' ),
					),
					'description'  => esc_html__( 'Default: Colored', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Button Click Effect', 'mdl-shortcodes' ),
					'attr'   => 'effect',
					'type'   => 'select',
					'options' => parent::mdl_ripple_effect_array(),
					'description'  => esc_html__( 'Default: Ripple', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Link URL', 'mdl-shortcodes' ),
					'attr'   => 'url',
					'type'   => 'url',
					'description'  => esc_html__( 'If entered, Button goes to this URL.', 'mdl-shortcodes' ),
					'meta' => array(
						'placeholder' => __( 'http://' ),
					),
				),
				array(
					'label'  => esc_html__( 'Link Target', 'mdl-shortcodes' ),
					'attr'   => 'target',
					'type'   => 'select',
					'options' => parent::mdl_targets_selection_array(),
					'description'  => esc_html__( 'Ignored if no Link URL', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Button Disabled', 'mdl-shortcodes' ),
					'attr'   => 'disabled',
					'type'   => 'select',
					'options' => parent::mdl_true_false_selection_array( 'true', 'true', 'false' ),
					'description'  => __( 'Adds the standard HTML boolean attribute "disabled"', 'mdl-shortcodes' ),
				),
				array(
					'label'			=> esc_html__( 'Custom CSS Class(es)', 'mdl-shortcodes' ),
					'attr'			=> 'class',
					'type'			=> 'text',
					'description'	=> parent::mdl_classes_description_text('', '', 'button element'),
					'meta'			=> array(
						'placeholder' => esc_html__( 'my-class-1 other-custom-class', 'mdl-shortcodes' ),
					),
				),
			),
		);
	}

	public static function callback( $atts, $content = '' ) {
				
		$defaults = array(
			'type'			=> 'none',	// none (flat), raised, fab (circular), mini-fab (small circular), icon
								// if you use 'icon', you need to add an icon as the $content
			'icon'			=> '',	// if $badge_text will be an icon, like 'account_box':
			'icondisplay'	=> '',
			'text'			=> '',
			'color'			=> '', // colored/true, false, primary, accent
			'effect'		=> 'ripple', // 'true' is same as 'ripple' because it is the only existing effect
			'url'			=> '',
			'target'		=> '', // e.g. 'blank'
			'disabled'		=> 'false', // add HTML 'disabled' attribute
			'class'			=> '',
		);
		
		$atts = shortcode_atts( $defaults, $atts, self::get_shortcode_tag() );
		
		$type			=	strtolower( $atts['type'] );
		$icon			=	strtolower( $atts['icon'] );
		$icon_display	=	strtolower( $atts['icondisplay'] );
		$text			=	trim( $atts['text'] ); // remove surrounding whitespace
		$color			=	strtolower( $atts['color'] );
		$effect			=	strtolower( $atts['effect'] );
		$url			=	esc_url( $atts['url'] );
		$target			=	parent::mdl_url_target( $atts['target'] );
		$disabled		=	strtolower( $atts['disabled'] );
		
		
		// Invalid Icon Name
		if ( ! array_key_exists( $icon, parent::mdl_icons_selection_array( 'false' ) ) ) {
			$icon = '';
		}
		
		if( $icon ) {
			$text = $icon; // if button type is Icon, do not display button text, just replace it
			if( 'nested' == $icon_display ) {
				$text = sprintf( '<i class="material-icons">%s</i>', $text );
			}
		}
		
		$allowed_icon_displays = array( 'icon', 'mini-icon', 'nested' );
		if( ! in_array( $icon_display, $allowed_icon_displays ) ) {
			if( $icon ) {
				$icon_display = 'icon';
			} else {
				$icon_display = '';
			}
		}
		
		// No Button text (neither Icon nor Text)
		if( empty( $text ) ) {
			return '';
		}
		
		$allowed_types = array( 'none', 'raised', 'fab', 'mini-fab', 'icon' );
		if( 'circle' == $type ) {
			$type = 'fab';
		}
		if(    'minifab'		== $type
			|| 'mini_fab'		== $type
			|| 'minicircle'		== $type
			|| 'mini-circle'	== $type
			|| 'mini_circle'	== $type
		) {
			$type = 'mini-fab';
		}
		if( ! in_array( $type, $allowed_types ) ) {
			$type = 'none';
		}
		
		$allowed_colors = array( 'false', 'colored', 'primary', 'accent' );
		if( 'true' == $color ) {
			$color = 'colored';
		}
		if( ! in_array( $color, $allowed_colors ) ) {
			$color = 'colored';
		}
		
		if( 'true' == $effect ) {
			$effect = 'ripple';
		}
		if( ! array_key_exists( $effect, parent::mdl_ripple_effect_array() ) ) {
			$effect = 'ripple';
		}
		
		$disabled = parent::mdl_truefalse( $disabled, 'false' );
		if( 'false' == $disabled ) {
			$disabled = '';
		} else {
			$disabled = ' disabled';
		}
		
		if( method_exists( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_sanitize_html_classes' ) ) {
			$class = parent::mdl_sanitize_html_classes( $atts['class'] );
		} else {
			// this will remove spaces so is faulty but better than not sanitizing at all
			$class = sanitize_html_class( $atts['class'] );
		}
		
		// BUILD OUTPUT
		$output = '<!-- MDL Button -->';
		
		$classes = 'mdl-button mdl-js-button';
		
		if( 'mini-fab' == $type || 'fab' == $type ) {
			$classes .= ' mdl-button--fab';
		}
		if ( 'fab' !== $type ) {
			$classes .= sprintf( ' mdl-button--%s', $type );
		}
		
		if( 'primary' == $color || 'accent' == $color ) {
			$classes .= sprintf( ' mdl-button--%1$s mdl-button--%1$s', $color ); // double-add for some reason -- .mdl-button--accent.mdl-button--accent
		} elseif( 'false' !== $color ) {
			$classes .= sprintf( ' mdl-button--%s', $color );
		}
		
		if ( 'false' !== $effect ) {
			$classes .= sprintf( ' mdl-js-%s-effect', $effect );
		}
		
		if ( 'icon' == $icon_display || 'mini-icon' == $icon_display ) {
			$classes .= ' mdl-button--icon material-icons';
		}
		if ( 'mini-icon' == $icon_display ) {
			$classes .= ' mdl-button--mini-icon';
		}
		
		if( $class ) {
			$classes .= ' ' . $class;
		}
		
		$button = sprintf( '<button class="%s"%s>%s</button>', $classes, $disabled, $text );
		
		if ( ! empty( $url ) ) {
			$output .= '<a';
			if ( $target ) {
				$output .= sprintf( ' target="%s"', $target );
			}
			$output .= sprintf( ' href="%s">%s</a>', $url, $button );
		} else {
			$output .= $button;
		}
		
		// return do_shortcode( $output ); // no shortcodes here, boss!
		return $output;
	
	}
	
}
