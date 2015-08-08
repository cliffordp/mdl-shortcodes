<?php

namespace MDL_Shortcodes\Shortcodes;

class MDL_Tab_Group extends Shortcode {

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
			'label'          => esc_html__( 'MDL Tab Group', 'mdl-shortcodes' ),
			'listItemImage'  => 'dashicons-archive',
			'add_button'	 => 'icon_only', // no UI so no add_button
			'inner_content' => array(
					//'value'			=> '[mdl-tab title="" active=""][/mdl-tab]',
					'description'	=> __( 'Enter [mdl-tab title="" active=""][/mdl-tab] shortcodes inside.', 'mdl-shortcodes' ),
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
		
		// wrap all the nested [mdl-tab] shortcodes' titles into one tab-bar div

/*
NOTES / EXAMPLES:

1) spread out in Editor

	[mdl-tab-group]
	
	[mdl-tab title="Boom" active="true"]
	<ul>
		<li>Boomer!</li>
	</ul>
	[/mdl-tab]
	
	[mdl-tab title="Soon"]
	<ul>
		<li>Sooner!</li>
	</ul>
	[/mdl-tab]
	
	[/mdl-tab-group]
	
	$content has lots of P tags already:
		</p>
		<p>[mdl-tab title="Boom" active="true"]</p>
		<ul>
		<li>Boomer!</li>
		</ul>
		<p>[/mdl-tab]</p>
		<p>[mdl-tab title="Soon"]</p>
		<ul>
		<li>Sooner!</li>
		</ul>
		<p>[/mdl-tab]</p>
		<p>
	
	do_shortcode( $content ) keeps those P tags:
		</p>
		<p><!-- MDL Tab --><!-- mdl-tab-title-start --><a href="#panel-Boom" class="mdl-tabs__tab is-active">Boom</a><!-- mdl-tab-title-end --><!-- mdl-tab-panel-start --><div id="panel-Boom" class="mdl-tabs__panel is-active"></p>
		<ul>
		<li>Boomer!</li>
		</ul>
		<p></div><!-- mdl-tab-panel-end --></p>
		<p><!-- MDL Tab --><!-- mdl-tab-title-start --><a href="#panel-Soon" class="mdl-tabs__tab">Soon</a><!-- mdl-tab-title-end --><!-- mdl-tab-panel-start --><div id="panel-Soon" class="mdl-tabs__panel"></p>
		<ul>
		<li>Sooner!</li>
		</ul>
		<p></div><!-- mdl-tab-panel-end --></p>
		<p>


2) all on one line in Editor

	[mdl-tab-group][mdl-tab title="Boom" active="true"]<ul><li>Boomer!</li></ul>[/mdl-tab][mdl-tab title="Soon"]<ul><li>Sooner!</li></ul>[/mdl-tab][/mdl-tab-group]
	
	$content has 
		[mdl-tab title="Boom" active="true"]
		<ul>
		<li>Boomer!</li>
		</ul>
		<p>[/mdl-tab][mdl-tab title="Soon"]
		<ul>
		<li>Sooner!</li>
		</ul>
		<p>[/mdl-tab]
	
	do_shortcode( $content ) is:
		<!-- MDL Tab --><!-- mdl-tab-title-start --><a href="#panel-Boom" class="mdl-tabs__tab is-active">Boom</a><!-- mdl-tab-title-end --><!-- mdl-tab-panel-start --><div id="panel-Boom" class="mdl-tabs__panel is-active">
		<ul>
		<li>Boomer!</li>
		</ul>
		<p></div><!-- mdl-tab-panel-end --><!-- MDL Tab --><!-- mdl-tab-title-start --><a href="#panel-Soon" class="mdl-tabs__tab">Soon</a><!-- mdl-tab-title-end --><!-- mdl-tab-panel-start --><div id="panel-Soon" class="mdl-tabs__panel">
		<ul>
		<li>Sooner!</li>
		</ul>
		<p></div><!-- mdl-tab-panel-end -->

*/

		// remove all wpautop P tags from $content before do_content()
		var_dump($content);
		$content = preg_replace( '/(<\/p>\s<p>\[mdl-tab)/', '[mdl-tab', $content );
		$content = preg_replace( '/(<p>\[\/mdl-tab]<\/p>\s<p>)/', '[/mdl-tab]', $content );
		var_dump($content);
		
		$content = do_shortcode( $content );
		plprint( $content );
		
		$start_tab = '<!-- MDL Tab -->';
		$start_title = '<!-- mdl-tab-title-start -->';
		$end_title = '<!-- mdl-tab-title-end -->';
		$start_panel = '<!-- mdl-tab-panel-start -->';
		$end_panel = '<!-- mdl-tab-panel-end -->';
		
		// array of all Tabs + Panels
		$tabs = explode( $start_tab, $content );
		
		plprint( $tabs, 'tabs' );
		
		$tab_titles = array();
		$tab_panels = array();
		foreach( $tabs as $tab ) {
			$tab_titles[] = parent::substr_getbykeys( $start_title, $end_title, $tab );
		}
		foreach( $tabs as $tab ) {
			$tab_panels[] = parent::substr_getbykeys( $start_panel, $end_panel, $tab );
		}
		
		$tab_titles = array_filter( $tab_titles );
		$tab_panels = array_filter( $tab_panels );
		
		plprint( $tab_titles, 'titles' );
		plprint( $tab_panels, 'panels' );
		
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
			
		$classes = 'mdl-tabs';
		
		if ( 'false' !== $effect ) {
			$classes .= sprintf( ' mdl-js-tabs mdl-js-%s-effect', $effect );
		}
		
		$output .= sprintf( '<div class="%s">%s</div>', $classes, $content );
		
		return do_shortcode( $output );
	}


}