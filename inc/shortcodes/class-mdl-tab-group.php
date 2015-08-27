<?php

namespace MDL_Shortcodes\Shortcodes;

class MDL_Tab_Group extends Shortcode {

// NOTES:
//
// http://www.getmdl.io/components/index.html#layout-section/tabs
//
// $content should include shortcodes to create tabs content
//
// wraps all the nested [mdl-tab] shortcodes' titles into one tab-bar div
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

// MAYBE CHECK FOR ACTIVE!!!

	public static function get_shortcode_ui_args() {
		return array(
			'label'          => esc_html__( 'MDL Tab Group', 'mdl-shortcodes' ),
			'listItemImage'  => 'dashicons-archive',
			'add_button'	 => 'icon_only', // no UI so no add_button
			'inner_content' => array(
					//'value'			=> '[mdl-tab title="" active=""][/mdl-tab]',
					'description'	=> sprintf( esc_html__( 'Enter %s shortcodes inside.', 'mdl-shortcodes' ), '[mdl-tab title="" active=""][/mdl-tab]' ),
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

		$start_tab = '<!-- MDL Tab -->';
		$start_title = '<!-- mdl-tab-title-start -->';
		$end_title = '<!-- mdl-tab-title-end -->';
		$start_panel = '<!-- mdl-tab-panel-start -->';
		$end_panel = '<!-- mdl-tab-panel-end -->';
		
		// each Tab in its own array
		$tabs = preg_split( '@(?=\[mdl-tab)@', $content, -1, PREG_SPLIT_NO_EMPTY ); // regex lookahead -- replace / with @ and add ?= to front of search -- to keep delimeter idea from http://stackoverflow.com/a/26021324
		
		// trim before [mdl-tab and after [/mdl-tab] (avoid all those leading and trailing P tags and whitespace, like nbsp and br too)
		array_walk( $tabs, function( &$item ) {
			$fail = false;
			
			$start_tab_shortcode = '[mdl-tab';
			$end_tab_shortcode = '[/mdl-tab]';
			$end_tab_shortcode_length = strlen( $end_tab_shortcode );
			
			$start_position = strpos( $item, $start_tab_shortcode );
			if( false === $start_position ) {
				$fail = true;
			}
			
			$end_position = strpos( $item, $end_tab_shortcode );
			if( false === $start_position ) {
				$fail = true;
			} else {
				$end_position = $end_position + $end_tab_shortcode_length;
			}
			
			if( true === $fail ) {
				$item = '';
			} else {
				$item = strstr( $item, $start_tab_shortcode ); // trim before $start_tab_shortcode
				$item = substr( $item, 0, $end_position ); // trim after of $end_tab_shortcode
			}
		});
		
		$tabs = array_filter( $tabs ); // remove empty array items
		
		$tabs_html = array();
		foreach( $tabs as $tab ) {
			$tabs_html[] = do_shortcode( $tab );
		}
		$tabs_html = array_filter( $tabs_html ); // remove empty array items
				
		$tab_titles = array();
		$tab_panels = array();
		foreach( $tabs_html as $tab ) {
			$tab_titles[] = parent::substr_getbykeys( $start_title, $end_title, $tab );
		}
		foreach( $tabs_html as $tab ) {
			$tab_panels[] = parent::substr_getbykeys( $start_panel, $end_panel, $tab );
		}
		
		$tab_titles = array_filter( $tab_titles ); // remove empty array items
		$tab_panels = array_filter( $tab_panels ); // remove empty array items
		
		// no MDL tabs inside!
		if( empty( $tab_titles ) || empty( $tab_panels ) ) {
			return '';
		}
		
		$tab_titles_output = '';
		foreach( $tab_titles as $title ) {
			$tab_titles_output .= $title;
		}
		
		$tab_panels_output = '';
		foreach( $tab_panels as $panel ) {
			$tab_panels_output .= $panel;
		}
		
		$content = sprintf( '<div class="mdl-tabs__tab-bar">%s</div>%s', $tab_titles_output, $tab_panels_output );
				
		// BUILD OUTPUT
		$output = '<!-- MDL Tab Group -->';
			
		$classes = 'mdl-tabs mdl-js-tabs';
		
		if ( 'false' !== $effect ) {
			$classes .= sprintf( ' mdl-js-%s-effect', $effect );
		}
		
		$output .= sprintf( '<div class="%s">%s</div>', $classes, $content );
		
		// remove invalid P tags
		$output = str_replace( 'mdl-tabs__panel"></p>', 'mdl-tabs__panel">', $output );
		$output = str_replace( 'mdl-tabs__panel"></p>', 'mdl-tabs__panel">', $output );
		$output = parent::mdl_cleanup_invalid_p_tags( $output );
		
		//return do_shortcode( $output ); // already did this above
		return $output;
	}


}