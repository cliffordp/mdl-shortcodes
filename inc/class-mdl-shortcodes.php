<?php

/**
 * Manages registered shortcodes
 */
class MDL_Shortcodes {

	private static $instance;
	
	// YES Shortcake UI, NO Duplicates
	private $internal_shortcode_classes_w_ui = array(
		'MDL_Shortcodes\Shortcodes\MDL_Icon',
		'MDL_Shortcodes\Shortcodes\MDL_Badge',
		'MDL_Shortcodes\Shortcodes\MDL_Button',
		'MDL_Shortcodes\Shortcodes\MDL_Card',
		'MDL_Shortcodes\Shortcodes\MDL_Tab',
	);
	
	// NO Shortcake UI, NO Duplicates
	private $internal_shortcode_classes_wo_ui = array(
		'MDL_Shortcodes\Shortcodes\MDL_Tab_Group',
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
		
		MDL_Shortcodes\Shortcodes\Shortcode::mdl_enqueue_stylesheet();
		MDL_Shortcodes\Shortcodes\Shortcode::mdl_enqueue_icons();
		MDL_Shortcodes\Shortcodes\Shortcode::mdl_enqueue_js();
		
		
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
		plprint($ui_shortcodes);
		
		wp_enqueue_script( 'jquery' ); // if not already, we need it
		
		// Initialize
		$html = '';
		$script = '';
		
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
			// example icon_only result: <a href="#" id="insert-mdl-card-button" class="button insert-mdl-card add-mdl-card" data-editor="content" title="Insert MDL Card"><span class="dashicons wp-media-buttons-icon dashicons-format-aside"></span></a>
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

} // closing MDL_Shortcodes class