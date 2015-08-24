<?php

/**
 * Manages registered shortcodes
 */
class MDL_Shortcodes {

	private static $instance;
	
	private static $mdl_customizer_flag = 'mdl-shortcodes-customizer';
	
	private static $mdl_customizer_colors_section = 'mdl_shortcodes_colors_section';
	
	// YES Shortcake UI, NO Duplicates
	private $internal_shortcode_classes_w_ui = array(
		'MDL_Shortcodes\Shortcodes\MDL_Icon',
		'MDL_Shortcodes\Shortcodes\MDL_Badge',
		'MDL_Shortcodes\Shortcodes\MDL_Button',
		'MDL_Shortcodes\Shortcodes\MDL_Card',
		'MDL_Shortcodes\Shortcodes\MDL_Tab',
		'MDL_Shortcodes\Shortcodes\MDL_Menu',
		'MDL_Shortcodes\Shortcodes\MDL_Tooltip',
	);
	
	// NO Shortcake UI, NO Duplicates
	private $internal_shortcode_classes_wo_ui = array(
		'MDL_Shortcodes\Shortcodes\MDL_Tab_Group',
		'MDL_Shortcodes\Shortcodes\MDL_Nav',
	);
	
	// YES Shortcake UI, YES Duplicates
	// PROBLEM is that the "Insert Post Element" UI will display 27 (original + 26 duplicates) of the SAME THING (no -a, -b, etc text to help the user)
	private $internal_shortcode_classes_w_ui_dups = array(
		'MDL_Shortcodes\Shortcodes\MDL_Cell',
	);
	
	// NO Shortcake UI, YES Duplicates
	private $internal_shortcode_classes_wo_ui_dups = array(
		'MDL_Shortcodes\Shortcodes\MDL_Grid',
	);
	
	private static $registered_shortcode_w_ui_duplicate_tags = array();
	private $registered_shortcode_classes = array();
	private $registered_shortcodes = array();

	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new MDL_Shortcodes;
			self::$instance->setup_actions();
			self::$instance->setup_filters();
		}
		return self::$instance;
	}
	
	
	/**
	 * Autoload any of our shortcode classes
	 */
	public function autoload_shortcode_classes( $class ) {
		$class = ltrim( $class, '\\' );
		if ( 0 !== stripos( $class, 'MDL_Shortcodes\\Shortcodes' ) ) {
			return;
		}

		$parts = explode( '\\', $class );
		// Don't need "MDL_Shortcodes\Shortcodes\"
		array_shift( $parts );
		array_shift( $parts );
		$last = array_pop( $parts ); // File should be 'class-[...].php' where [...] is the actual shortcode
		$last = 'class-' . $last . '.php';
		$parts[] = $last;
		$file = dirname( __FILE__ ) . '/shortcodes/' . str_replace( '_', '-', strtolower( implode( $parts, '/' ) ) ); // why no underscore: https://github.com/fusioneng/shortcake-bakery/issues/12
		if ( file_exists( $file ) ) {
			require $file;
		}

	}
	
	
	/**
	 * Set up shortcode actions
	 */
	private function setup_actions() {
		spl_autoload_register( array( $this, 'autoload_shortcode_classes' ) );
		add_action( 'init', array( $this, 'action_init_register_shortcodes' ) );
		add_action( 'shortcode_ui_after_do_shortcode', function( $shortcode ) {
			return $this::get_shortcake_admin_dependencies();
		});
				
		add_action( 'customize_register', array( $this, 'mdl_customizer_options' ) );
		if( isset( $_GET[ self::$mdl_customizer_flag ] ) ) {
			add_filter( 'customize_register', array( $this, 'mdl_remove_customizer_controls' ) );
			//add_filter( 'customize_control_active', array( $this, 'mdl_control_filter' ), 10, 2 ); // could not get it to work so manually removed ones via remove_customizer_controls() method
		}
		
		// here in addition to action_init_register_shortcodes() so WP Customizer preview works even when not on a page that has a shortcode
		add_action( 'customize_preview_init', array( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_enqueue_stylesheet' ) );
		add_action( 'customize_preview_init', array( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_enqueue_icons' ) );
		add_action( 'customize_preview_init', array( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_enqueue_js' ) );
		
		add_action( 'admin_menu', array( $this, 'mdl_add_wp_admin_options_link' ) );
				
		add_action( 'after_wp_tiny_mce', array( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_tinymce_scripts_func' ) ); // note that Shortcake is only loosly coupled with TinyMCE -- the Shortcode UI still works if Visual editor is disabled
		add_action( 'media_buttons', array( $this, 'shortcode_ui_editor_one_click_insert_buttons' ), 200 ); // higher priority adds it to the right side of all the other buttons (Add Media, Gravity Forms, etc.)
	}
	
	
	/**
	 * Set up shortcode filters
	 */
	private function setup_filters() {
		add_filter( 'pre_kses', array( $this, 'filter_pre_kses' ) );
		
		
		add_filter( 'admin_enqueue_scripts', array( 'MDL_Shortcodes', 'admin_shortcake_hide_duplicate_shortcodes_style' ) );
		
		add_filter( 'mce_css', array( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_tinymce_stylesheet_icons_func' ) );
		// add_filter( 'mce_css', array( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_tinymce_css_php_func' ) );
	}
	
	
	/**
	 * Register all of the shortcodes
	 */
	public function action_init_register_shortcodes() {
		
		// global here instead of in do_shortcode_callback() because we want it to display site-wide, not just if a shortcode is in use
		MDL_Shortcodes\Shortcodes\Shortcode::mdl_enqueue_stylesheet();
		MDL_Shortcodes\Shortcodes\Shortcode::mdl_enqueue_icons();
		MDL_Shortcodes\Shortcodes\Shortcode::mdl_enqueue_js();
				
		$w_ui = apply_filters( 'mdl_shortcodes_shortcode_classes_w_ui', $this->internal_shortcode_classes_w_ui );
		$wo_ui = apply_filters( 'mdl_shortcodes_shortcode_classes_wo_ui', $this->internal_shortcode_classes_wo_ui );
		
		$this->registered_shortcode_classes = array_merge( $w_ui, $wo_ui );
		
		$this->registered_shortcode_classes = array_filter( $this->registered_shortcode_classes );
		
		foreach ( $this->registered_shortcode_classes as $class ) {
			$shortcode_tag = $class::get_shortcode_tag();
			$this->registered_shortcodes[ $shortcode_tag ] = $class;
			add_shortcode( $shortcode_tag, array( $this, 'do_shortcode_callback' ) );
			$class::setup_actions();
			
			// only do UI for those in $w_ui
			if( in_array( $class, $w_ui ) ) {
				$ui_args = $class::get_shortcode_ui_args();
				if ( ! empty( $ui_args ) && function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
					shortcode_ui_register_for_shortcode( $shortcode_tag, $ui_args );
				}
			}
		}
		
		if( method_exists( $this, 'action_init_register_duplicate_shortcodes' ) ) {
			$w_ui_dups = apply_filters( 'mdl_shortcodes_shortcode_classes_w_ui_dups', $this->internal_shortcode_classes_w_ui_dups );
			$wo_ui_dups = apply_filters( 'mdl_shortcodes_shortcode_classes_wo_ui_dups', $this->internal_shortcode_classes_wo_ui_dups );
			
			$this->action_init_register_duplicate_shortcodes( $w_ui_dups, true );
			$this->action_init_register_duplicate_shortcodes( $wo_ui_dups, false );
		}
	}
	
	
	/**
	 * Adapted from action_init_register_shortcodes(), above -- we want this function AFTER that one but BEFORE filter_pre_kses()
	 * 
	 * Register DUPLICATE shortcodes (and INITIAL shortcodes if necessary)
	 * And HIDE a-through-z shortcodes from Shortcake UI
	 * 
	 * Example: adds [mdl-grid] (if not already added) and [mdl-grid-a], [mdl-grid-b], ... [mdl-grid-z]
	 */
	public function action_init_register_duplicate_shortcodes( $shortcode_classes_to_duplicate = array(), $register_ui = true ) {
		
		if ( empty( $shortcode_classes_to_duplicate ) || ! is_array( $shortcode_classes_to_duplicate ) ) {
			return false;
		}
		
		$this->registered_shortcode_classes = array_merge( $this->registered_shortcode_classes, $shortcode_classes_to_duplicate );
		
		$this->registered_shortcode_classes = array_filter( $this->registered_shortcode_classes );
		
		// A through Z plus blank at the front (blank = the original, A-Z = the duplicates)
		$alpha_array = array( '', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', );
		
		
		foreach ( $shortcode_classes_to_duplicate as $class ) {
		
			foreach ( $alpha_array as $append ) {
				$shortcode_tag = $class::get_shortcode_tag( '', $append );
				if( shortcode_exists( $shortcode_tag ) ) {
					continue; // already registered via action_init_register_shortcodes() so move onto the next one
				}
				$this->registered_shortcodes[ $shortcode_tag ] = $class;
				add_shortcode( $shortcode_tag, array( $this, 'do_shortcode_callback' ) );
				$class::setup_actions();
				$ui_args = $class::get_shortcode_ui_args();
				if ( $register_ui && ! empty( $ui_args ) && function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
					shortcode_ui_register_for_shortcode( $shortcode_tag, $ui_args );
					
					// do not need to hide with wp-admin CSS if no UI anyway
					if( $append !== '' ) {
						self::$registered_shortcode_w_ui_duplicate_tags[] = $shortcode_tag;
					}
				}
			}
		}
	}
	
	
	/**
	 * Modify post content before kses is applied
	 * Used to trans
	 */
	public function filter_pre_kses( $content ) {

		foreach ( $this->registered_shortcode_classes as $shortcode_class ) {
			$content = $shortcode_class::reversal( $content );
		}
		return $content;
	}
	
	
	/**
	 * Do the shortcode callback
	 */
	public function do_shortcode_callback( $atts, $content = '', $shortcode_tag ) {
				
		if ( empty( $this->registered_shortcodes[ $shortcode_tag ] ) ) {
			return '';
		}
		
		$class = $this->registered_shortcodes[ $shortcode_tag ];
		return $class::callback( $atts, $content, $shortcode_tag );
	}
	
	
	/**
	 * Admin dependencies.
	 * Scripts required to make shortcake previews work correctly in the admin.
	 *
	 * @return string
	 */
	public static function get_shortcake_admin_dependencies() {
		if ( ! is_admin() ) {
			return;
		}
		
/*
		$r = '<script src="' . esc_url( includes_url( 'js/jquery/jquery.js' ) ) . '"></script>';
		$r .= '<script type="text/javascript" src="' . esc_url( MDL_SHORTCODES_URL_ROOT . 'assets/js/shortcake-bakery.js' ) . '"></script>';
		return $r;
*/
	}	
	
		
	/**
	* is_edit_page -- from http://wordpress.stackexchange.com/a/50045
	* function to check if the current page is a post edit page
	* 
	* @author Ohad Raz <admin@bainternet.info>
	* 
	* @param  string  $new_edit what page to check for accepts new - new post page, edit - edit post page, null for either
	* @return boolean
	*/
	public static function is_edit_page( $new_edit = null ){
		global $pagenow;
		//make sure we are on the backend
		if ( ! is_admin() ) {
			return false;
		}
		
		if( $new_edit == 'edit' ) {
			return in_array( $pagenow, array( 'post.php' ) );
		} elseif( $new_edit == 'new') { //check for new post page
			return in_array( $pagenow, array( 'post-new.php' ) );
		} else { //check for either new or edit
			return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
		}
	}
	
	
	public static function admin_shortcake_hide_duplicate_shortcodes_style(){
		$output = '';
		
		$reg_dups = array_filter( self::$registered_shortcode_w_ui_duplicate_tags ); // remove blanks
		
		if( ! empty( $reg_dups ) ) {
			$output .= '<style>';
			
			foreach( $reg_dups as $tag ) {
				if( $output && '<style>' !== $output ) {
					$output .= ', ';
				}
				$output .= sprintf( '.add-shortcode-list .shortcode-list-item[data-shortcode="%s"]', $tag ); // Shortcake UI
				$output .= sprintf( ', a#insert-%s-button.button', $tag ); // Quick Insert Buttons, from shortcode_ui_editor_one_click_insert_buttons()
			}
			
			if( $output ) {
				$output .= '{ display: none; }</style>';
				echo $output;
			}
		}
		return; // did echo above, only return if nothing echoed -- must echo or else admin_enqueue_scripts will not print anything
	}
	
	
	// adapted from https://github.com/fusioneng/Shortcake/issues/94#issuecomment-68020127
	function shortcode_ui_editor_one_click_insert_buttons( $editor_id = '' ) { // without $editor_id = '', 'content' gets printed before the button
		
		// Check if Shortcake is installed and activated
		if( ! method_exists( 'Shortcode_UI', 'get_instance' ) ) {
			return false;
		}
		
		// Get all registered UI shortcodes
		$ui_shortcodes = Shortcode_UI::get_instance()->get_shortcodes();
		if( empty( $ui_shortcodes ) ) {
			return false;
		}
		
		wp_enqueue_script( 'jquery' ); // if not already, we need it
		
		// Initialize
		$html = '';
		$script = '';
		
		//
		// manually add custom buttons (e.g. shortcodes without UI, group of shortcodes like grid/cell or tabs)
		//
		// MDL Grid of 4-4-4-4, 
		$html .= '<a href="#" id="insert-mdl-grid-84-3333-1101-button" class="button insert-mdl-grid-84-3333-1101-button add-mdl-grid-84-3333-1101-button" data-editor="content" title="Insert MDL Grids of 8-4, 3-3-3-3, and 1-10-1"><span class="dashicons wp-media-buttons-icon dashicons-welcome-view-site"></span></a>';
		$script .= 'jQuery("#insert-mdl-grid-84-3333-1101-button").on("click", function() { window.parent.send_to_editor(\'[mdl-grid][mdl-cell size=8]something here that will be 8 columns wide[/mdl-cell][mdl-cell]something here that will be 4 columns wide, since 4 is the default size[/mdl-cell][/mdl-grid][mdl-grid spacing=false][mdl-cell size=3]1st quarter[/mdl-cell][mdl-cell size=3]2nd quarter[/mdl-cell][mdl-cell size=3]third quarter[/mdl-cell][mdl-cell size=3]4th quarter[/mdl-cell][/mdl-grid][mdl-grid color="mdl-color-text--accent-contrast" bgcolor="mdl-color--red-700"][mdl-cell size=1][/mdl-cell][mdl-cell size=10]Ten wide with 1 column on each side for gutter effect. Lorem ipsum dolor sit amet, cum posse accumsan prodesset ne. Tale graeci cu ius, nec ne partem labores partiendo, id vel elitr primis veritus.[/mdl-cell][mdl-cell size=1][/mdl-cell][/mdl-grid]\'); });';
		
		// Add Button and jQuery for each UI shortcode with add_button attribute
		// to display only an icon:
			// 'add_button'	 => 'icon_only',
		// to display icon + label:
			// 'add_button'	 => 'yup',
			// anything that passes the empty() check
		foreach ( $ui_shortcodes as $shortcode => $atts ) {
		
			// Skip if add_button attribute isn't set
			if ( empty( $atts['add_button'] ) ) {
				continue;
			}
			
			$label = ' Insert ' . $atts['label'];
			if ( 'icon_only' == $atts['add_button'] ) {
				$label = '';
			}
						
			// If shortcode has an inner_content attribute (e.g. single shortcode [button] vs [button][/button])
			$enclosed_shortcode_tail = '';
			if ( isset( $atts['inner_content'] ) ) {
				$enclosed_shortcode_tail = sprintf( '[/%s]', $shortcode );
			}
			
			// Compile button HTML
			// EXAMPLE full button text result:
			// 
			// <a href="#" id="insert-mdl-icon-button" class="button insert-mdl-icon add-mdl-icon" data-editor="content" title="Insert MDL Material Design Icon"><span class="dashicons wp-media-buttons-icon dashicons-editor-textcolor"></span></a>
			//
			//
			// EXAMPLE icon_only result:
			// 
			// <a href="#" id="insert-mdl-card-button" class="button insert-mdl-card add-mdl-card" data-editor="content" title="Insert MDL Card"><span class="dashicons wp-media-buttons-icon dashicons-format-aside"></span></a>
			//
			//
			$html .= sprintf( '<a href="#" id="insert-%1$s-button" class="button insert-%1$s add-%1$s" data-editor="content" title="Insert %2$s"><span class="dashicons wp-media-buttons-icon %3$s"></span>%4$s</a>',
				$shortcode,
				$atts['label'],
				$atts['listItemImage'],
				$label
			);
			
			// Build shortcode will all possible attributes set to ""
			$att_markup = array();
			$has_content_att = false;
			
			foreach( $atts['attrs'] as $att_array ) {
				$att_markup[] = $att_array['attr'] . '=""';	// notice double-quotes
			} // end foreach
			
			// notice escaped single-quotes in send_to_editor -- they are required, else JS console error and will not insert into Editor
			// EXAMPLE full button text result:
			// 
/*
				jQuery("#insert-mdl-icon-button").on("click", function() {
					window.parent.send_to_editor('[mdl-icon icon="" color="" bgcolor="" class=""]');
				});
*/
			//
			//
			// EXAMPLE icon_only result (NO DIFFERENCES HERE, just a 2nd output example):
			// 
/*
				jQuery("#insert-mdl-card-button").on("click", function() {
					window.parent.send_to_editor('[mdl-card postid="" title="" menu="" menulink="" menutarget="" htag="" titlecolor="" titlebgcolor="" mediaid="" mediasize="" mediaplacement="" mediapadding="" supporting="" actions="" actionslink="" actionstarget="" actionsicon="" actionsborder="" shadow="" class=""]');
				});
*/
			//
			//
			$script .= sprintf( 'jQuery("#insert-%1$s-button").on("click", function() {
				window.parent.send_to_editor(\'[%1$s %2$s]%3$s\');
				});',
				$shortcode,
				implode( ' ', $att_markup ),
				$enclosed_shortcode_tail
			);
		} // end foreach
		
		$script = sprintf( '<script>%s</script>', $script );
		
		$html .= $script;
		
		echo $html;
	}
	
	
	/**
	 * duplicate of plprint() from PageLines DMS
	 * Debugging, prints nice array.
	 * Sends to the footer in all cases.
	 * 
	 */
	function mdl_print( $data, $title = false, $echo = false) {
	
		if ( ! current_user_can('manage_options') || ( defined( 'DOING_AJAX' ) && true == DOING_AJAX) )
			return;
	
		ob_start();
	
			echo '<div class="mdl_print-container"><pre class="mdl_print">';
	
			if ( $title )
				printf('<h3>%s</h3>', $title);
	
			echo esc_html( print_r( $data, true ) );
	
			echo '</pre></div>';
	
		$data = ob_get_clean();
	
		if ( $echo )
			echo $data;
		elseif ( false === $echo )
			add_action( 'shutdown', create_function( '', sprintf('echo \'%s\';', $data) ) );
		else
			return $data;
	}
	
	
	// help from https://www.youtube.com/watch?v=7usuZRBsyk8 --> https://speakerdeck.com/bftrick/using-the-wordpress-customizer-to-build-beautiful-plugin-settings-pages
	function mdl_add_wp_admin_options_link(){
		$url = 'customize.php';
		
		// get special MDL Demo / Template page
		$mdl_demo_page_url = get_permalink( 241 );
		
		// if we have the special page, go straight to it
		// if we don't have the special page, it'll just load the default customize.php (default is the home page)
		if( $mdl_demo_page_url ) {
			$url = add_query_arg( 'url', urlencode( $mdl_demo_page_url ), $url );
		}
		
		// get the page to return to (hit X on the Customizer)
		$url = add_query_arg( 'return', urlencode( admin_url( 'themes.php' ) ), $url );
		
		// add flag in the Customizer url so we know we're in MDL Shortcodes editor
		$url = add_query_arg( self::$mdl_customizer_flag, 'true', $url );
		
		// auto-open the MDL Shortcodes editor
		$url = add_query_arg( 'autofocus[section]', self::$mdl_customizer_colors_section, $url );
		
		//add_theme_page( 'MDL Shortcodes Option', 'MDL Shortcodes Options', 'edit_theme_options', $url );
		add_menu_page( 'MDL Shortcodes', 'MDL Shortcodes Options', 'manage_options', $url, '', 'dashicons-book-alt', 63 );
	}
	
	
	// if we catch that flag from above function, hide default Customizer controls
	public function mdl_remove_customizer_controls( $wp_customize ) {
		global $wp_customize;
		
// 		$wp_customize->remove_panel( 'widgets' );
		//$wp_customize->remove_panel( 'nav_menus' ); // WP 4.3+
		
		$wp_customize->remove_section( 'themes' );
/*
		$wp_customize->remove_section( 'title_tagline' );
		$wp_customize->remove_section( 'colors' );
		$wp_customize->remove_section( 'header_image' );
		$wp_customize->remove_section( 'background_image' );
		$wp_customize->remove_section( 'nav' ); // prior to WP 4.3
		$wp_customize->remove_section( 'static_front_page' );
*/
		
		return true;
	}
	
	
	// could not get it to function properly
	// if we catch that flag from above function, hide all Customizer controls that we didn't manually add to the Customizer (except the 'themes' section as of WP 4.2)
	// ref: https://developer.wordpress.org/themes/advanced-topics/customizer-api/#contextual-controls-sections-and-panels
	function mdl_control_filter( $active, $control ) {
		if( in_array( $control->section, array( self::$mdl_customizer_colors_section ) ) ) {
			return true;
		}
		
		return false;
	}
	
	
	function mdl_customizer_options( $wp_customize ) {
/*
		// Customizer Panel
		$wp_customize->add_panel(
			'mdl_shortcodes_panel',
			array(
				'title'			=> __('MDL Shortcodes Settings', 'mdl-shortcodes'),
				'description'	=> __('Material Design Lite (MDL) Shortcodes Settings', 'mdl-shortcodes'),
				'priority'		=> 10,
			)
		);
*/
		
		// Customizer Section
		$wp_customize->add_section(
			self::$mdl_customizer_colors_section,
			array(
				'title'			=> __('MDL Shortcodes Color Settings', 'mdl-shortcodes'),
				'description'	=> __('Color swatches are visible at <a href="http://www.getmdl.io/customize/" target="_blank">GetMDL.io</a> (link opens in a new window)<br>If Primary and Secondary are set to the same, the color combination will default back to Indigo-Pink.', 'mdl-shortcodes'),
				'priority'		=> 12,
				//'panel'			=> 'mdl_shortcodes_panel',
			)
		);
			
			// Primary Color Setting
			$wp_customize->add_setting( 'mdl_shortcodes_colors_setting[primary]', array(
				'default'	=> 'indigo',
				'type'		=> 'option',
			));
			
			$wp_customize->add_control( 'mdl_shortcodes_primary_color_control', array(
				'label'		=> __('MDL Primary Color', 'mdl-shortcodes'),
				'section'	=> self::$mdl_customizer_colors_section,
				'settings'	=> 'mdl_shortcodes_colors_setting[primary]',
				'type'		=> 'select',
				'choices'	=> MDL_Shortcodes\Shortcodes\Shortcode::mdl_single_color_names( 'all' ),
			));
			
			// Accent Color Setting
			$wp_customize->add_setting( 'mdl_shortcodes_colors_setting[accent]', array(
				'default'	=> 'pink',
				'type'		=> 'option',
			));
			
			$wp_customize->add_control( 'mdl_shortcodes_accent_color_control', array(
				'label'		=> __('MDL Accent Color', 'mdl-shortcodes'),
				'section'	=> self::$mdl_customizer_colors_section,
				'settings'	=> 'mdl_shortcodes_colors_setting[accent]',
				'type'		=> 'select',
				'choices'	=> MDL_Shortcodes\Shortcodes\Shortcode::mdl_single_color_names( 'accents' ),
			));
	}

} // closing MDL_Shortcodes class