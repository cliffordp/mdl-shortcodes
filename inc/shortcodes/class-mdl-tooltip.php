<?php

namespace MDL_Shortcodes\Shortcodes;

class MDL_Tooltip extends Shortcode {

// NOTES:
//
// http://www.getmdl.io/components/index.html#tooltips-section
//


	public static function get_shortcode_ui_args() {
		return array(
			'label'          => esc_html__( 'MDL Tooltip', 'mdl-shortcodes' ),
			'listItemImage'  => 'dashicons-admin-comments',
			'add_button'	 => 'icon_only',
			'inner_content' => array(
				//'value'		=> '',
				'description'	=> __( 'Enter your tooltip text here. If left blank, Tooltip will not be displayed at all. Example: eXtensible Markup Language (to explain the XML acronym). NOTE: Text Editor Tooltip hovering preview does not work.', 'mdl-shortcodes' ),
			),
			'attrs'          => array(
				array(
					'label'  => esc_html__( 'Large Tooltip Text Size?', 'mdl-shortcodes' ),
					'attr'   => 'large',
					'type'   => 'checkbox',
					'description'  => esc_html__( 'Check the box to display the Tooltip Text in a larger font size.', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Icon', 'mdl-shortcodes' ),
					'attr'   => 'icon',
					'type'   => 'select',
					'options' => parent::mdl_icons_selection_array(),
					'description'  => parent::mdl_icon_description_text( 'Default: add. ' ),
				),
				array(
					'label'  => esc_html__( '(OR) Tooltip target text (instead of an Icon)', 'mdl-shortcodes' ),
					'attr'   => 'text',
					'type'   => 'text',
					'description'  => esc_html__( 'Display Tooltip over this text. If you choose an Icon, this content will be ignored.', 'mdl-shortcodes' ),
					'meta' => array(
						'placeholder' => esc_html__( 'Example: XML', 'mdl-shortcodes' ),
					),
				),
				array(
					'label'  => esc_html__( 'Tooltip Target Element Type', 'mdl-shortcodes' ),
					'attr'   => 'type',
					'type'   => 'select',
					'options' => array(
						''		=> esc_html__( 'Span', 'mdl-shortcodes' ),
						'p'		=> esc_html__( 'Paragraph', 'mdl-shortcodes' ),
						'div'	=> esc_html__( 'Div', 'mdl-shortcodes' ),
					),
					'description'  => esc_html__( 'Type of HTML element. Default: Span. NOTE: MDL styling defaults to displaying as inline-block so changing from Span to Div will not have that block display unless you add your own styling.', 'mdl-shortcodes' ),
				),
				array(
					'label'			=> esc_html__( 'Tooltip ID', 'mdl-shortcodes' ),
					'attr'			=> 'id',
					'type'			=> 'text',
					'description'	=> esc_html__( 'If left blank, one will be automatically generated for you.', 'mdl-shortcodes' ),
					'meta'			=> array(
						'placeholder' => esc_html__( 'tooltip1', 'mdl-shortcodes' ),
					),
				),
				array(
					'label'			=> esc_html__( 'Custom CSS Class(es)', 'mdl-shortcodes' ),
					'attr'			=> 'class',
					'type'			=> 'text',
					'description'	=> parent::mdl_classes_description_text('', '', 'Tooltip (not content that Tooltip targets).'),
					'meta'			=> array(
						'placeholder' => esc_html__( 'my-class-1 other-custom-class', 'mdl-shortcodes' ),
					),
				),
			),
		);
	}

	public static function callback( $atts, $content = '' ) {
				
		$defaults = array(
			'large'			=> '',
			'icon'			=> '',
			'text'			=> '',
			'type'			=> '',
			'id'			=> '',
			'class'			=> '',
		);
		
		$atts = shortcode_atts( $defaults, $atts, self::get_shortcode_tag() );
		
		$large	= $atts['large'] ? ' mdl-tooltip--large' : ''; // if 'large' is anything (e.g. box checked --> "true"), make text size large
		$icon	= strtolower( $atts['icon'] );
		$text	= $atts['text'];
		$type	= strtolower( $atts['type'] );
		$id		= $atts['id'];
		
				
		// bail if no Tooltip Text
		if( empty( $content ) ) {
			return '';
		}
		
		// Invalid Icon Name
		if ( ! array_key_exists( $icon, parent::mdl_icons_selection_array( 'false' ) ) ) {
			$icon = '';
		}
		
		$icon_or_text = 'icon';
		if( empty( $icon ) ) {
			
			if( isset( $text ) && '' != $text ) {
				$icon_or_text = 'text';
			}
			
			if( 'icon' == $icon_or_text ) {
				$icon = 'add';
			}
		}
		
		
		$target_classes = '';
		if( 'icon' == $icon_or_text ) {
			$target_classes = 'icon material-icons';
			$icon_or_text = $icon;
		} else {
			$icon_or_text = $text;
		}
		
		$allowed_types = array( 'span', 'p', 'div' );
		if( ! in_array( $type, $allowed_types ) ) {
			$type = 'span';
		}
		
		
		$id = sanitize_html_class( $id );
		if( empty( $id ) ) {
			$id = 'mdltooltipid-rand-' . rand( 101, 199 );
		}
		$id = sanitize_html_class( $id );
		
		
		if( method_exists( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_sanitize_html_classes' ) ) {
			$class = parent::mdl_sanitize_html_classes( $atts['class'] );
		} else {
			// this will remove spaces so is faulty but better than not sanitizing at all
			$class = sanitize_html_class( $atts['class'] );
		}
		
		
		// BUILD OUTPUT
		$output = '<!-- MDL Tooltip -->';
		
		$classes = 'mdl-tooltip' . $large;
		
		if( $class ) {
			$classes .= ' ' . $class;
		}
		
		$output .= sprintf( '<%1$s id="%2$s" class="%3$s">%4$s</%1$s>',
			$type,
			$id,
			$target_classes,
			$icon_or_text,
			$type
		);
				
		$output .= sprintf( '<span for="%1$s" class="%2$s">%3$s</span>',
			$id,
			$classes,
			$content
		);
				
		return do_shortcode( $output );
	}
	
}