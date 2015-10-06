<?php

namespace MDL_Shortcodes\Shortcodes;

/**
 * Base class for all shortcodes to extend
 * Ensures each shortcode implements a consistent pattern
 */
abstract class Shortcode {

	/**
	 * Get the "tag" used for the shortcode. This will be stored in post_content
	 *
	 * @return string
	 */
	public static function get_shortcode_tag( $prepend = '', $append = '' ) {
		$parts = explode( '\\', get_called_class() );
		$shortcode_tag = array_pop( $parts );
		$shortcode_tag = strtolower( str_replace( '_', '-', $shortcode_tag ) );
		if( $prepend ) {
			$shortcode_tag = sprintf( '%s-%s', $prepend, $shortcode_tag );
		}
		if( $append ) {
			$shortcode_tag = sprintf( '%s-%s', $shortcode_tag, $append );
		}
		return apply_filters( 'mdl_shortcodes_shortcode_tag_filter', $shortcode_tag, get_called_class() );
	}

	/**
	 * Allow subclasses to register their own action
	 * Fires after the shortcode has been registered on init
	 *
	 * @return null
	 */
	public static function setup_actions() {
		// No base actions are necessary
	}

	public static function get_shortcode_ui_args() {
		return array();
	}

	/**
	 * Turn embed code into a proper shortcode
	 *
	 * @param string $content
	 * @return string $content
	 */
	public static function reversal( $content ) {
		return $content;
	}

	/**
	 * Render the shortcode. Remember to always return, not echo
	 *
	 * @param array $atts Shortcode attributes
	 * @param string $content Any inner content for the shortcode (optional)
	 * @return string
	 */
	public static function callback( $atts, $content = '' ) {
		return '';
	}




	
	
	// HELPER FUNCTIONS
	
	
	
	
	
	/**
	 * from plprint() from PageLines DMS 2
	 * Debugging, prints nice array.
	 * Sends to the footer in all cases.
	 * 
	 */
	public static function mdl_print( $data, $title = false, $echo = false) {
	
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
	
	
	// adapted from PageLines DMS 2
	public static function mdl_get_post_types_support_fimages( $fallback = 'any', $type = 'wp_query_post_type_array' ){
		
		$pt_objects = get_post_types( array( 'public' => true ), 'objects' );
		
		$pts = array();
		
		// build array like 'post' => 'Posts'
		foreach( $pt_objects as $key => $pt ){
			if( post_type_supports( $key, 'thumbnail' ) && $pt->public ){
				$pts[ $key ] = $pt->label;
			}
		}
		
		if( empty( $pts ) && '' != $fallback ) {
			$pts = array( $fallback );
		}
		
		if( $type == 'labels_csv' ) {
			$output = implode( ',', $pts );
		} elseif( $type == 'names_csv' ) {
			$output = implode( ',', array_keys( $pts ) );
		} elseif( $type == 'names_array' ) {
			$output = array_keys( $pts );
		} elseif( $type == 'labels_array' ) {
			$output = $pts;
		} else { // $type == 'wp_query_post_type_array' -- to be put inside array( 'post_type' => xxx )
			$output = array_keys( $pts );
			//$output = array( 'page', 'post' ) );
		}
		//var_dump($output);
		
		return $output;
	}
	
	
	public static function mdl_get_posts_w_fimage_set( $posts_per_page = -1, $post_type = 'any', $post_status = 'publish', $fields = 'ids' ) {
		
		$args = array(
			'posts_per_page'	=> $posts_per_page,
			'post_type'			=> $post_type,
			'post_status'		=> $post_status,
			'meta_query'		=> array(
				array( 'key' => '_thumbnail_id' ), // has a featured image set
			),
			'fields'			=> $fields, // just want the Post IDs, not the default array of post objects
		);
		
		$post_ids = get_posts( $args );
		
		/* Example var_dump()
		array (size=3)
		  0 => int 483
		  1 => int 480
		  2 => int 436
		*/
		
		if( empty( $post_ids ) ) {
			//$post_ids = 
		}
		
		return $post_ids;
	}
	
	
	public static function mdl_nav_menus_selection_array( $prepend_empty = 'true', $args = 'hide_empty=1' ) {
		$navs = get_terms( 'nav_menu', $args );
		
		$allowed_options = array();
		
		if( ! empty( $navs ) ) {
			foreach( $navs as $nav => $object ) {
				$allowed_options[ $object->term_id ] = $object->name . ' (ID: ' . $object->term_id . ')';
			}
		}
		
		if( 'true' == $prepend_empty ) {
			$allowed_options = array( '' => '' )+$allowed_options;
		}
		
		return $allowed_options;
	}
	
	
	public static function mdl_nav_types_selection_array ( $prepend_empty = 'true' ) {
		$allowed_options = array(
			'transparent'		=> esc_html__( 'Transparent Header, Collapsible Drawer', 'mdl-shortcodes' ),
			'none-fixed'		=> esc_html__( 'No Header, Fixed Drawer', 'mdl-shortcodes' ),
			'fixed'				=> esc_html__( 'Fixed Header, Collapsible Drawer', 'mdl-shortcodes' ),
			'fixed-fixed'		=> esc_html__( 'Fixed Header, Fixed Drawer', 'mdl-shortcodes' ),
			'scrolling'			=> esc_html__( 'Scrolling Header, Collapsible Drawer', 'mdl-shortcodes' ),
			'waterfall'			=> esc_html__( 'Waterfall Header, Collapsible Drawer', 'mdl-shortcodes' ),
			//'scrollabletabs'	=> esc_html__( 'Horizontally-Scrollable tabs Header, Collapsible Drawer', 'mdl-shortcodes' ), // demo at https://github.com/google/material-design-lite/issues/1380#issuecomment-130383886 --> http://codepen.io/surma/pen/RPOREb -- but only scrollable at 1025px and wider on that demo... The buttons disappear because we can assume that you are on mobile and have a touch interface.
			//'fixedtabs'			=> esc_html__( 'Fixed tabs Header, Collapsible Drawer', 'mdl-shortcodes' ),
		);
		
		if( 'true' == $prepend_empty ) {
			$allowed_options = array( '' => '' )+$allowed_options;
		}
		
		return $allowed_options;
	}
	
	
	public static function mdl_menu_positions_selection_array( $prepend_empty = 'true' ) {
		$allowed_options = array(
			''				=> esc_html__( 'Lower Left', 'mdl-shortcodes' ),
			'lower-right'	=> esc_html__( 'Lower Right', 'mdl-shortcodes' ),
			'top-left'		=> esc_html__( 'Top Left', 'mdl-shortcodes' ),
			'top-right'		=> esc_html__( 'Top Right', 'mdl-shortcodes' ),
		);
		
		if( 'true' == $prepend_empty ) {
			$allowed_options = array( '' => '' )+$allowed_options;
		}
		
		return $allowed_options;
	}
	
	
	// wp_get_nav_menu_items takes menu name, ID, or slug -- but shortcode only uses ID
	// just as simple to use wp_get_nav_menu_items and do it all custom instead of custom walker class on wp_nav_menu because we want all the classes on the A tags, not the LI tags
	public static function mdl_build_nav_menu_items( $menu = '', $link_class = '', $depth = -1 ) {
		$depth = -1; // hard-coded for now
		$depth = intval( $depth );
		
		if( empty( $menu ) ) {
			return false;
		}
		
		$items = wp_get_nav_menu_items( $menu );
		
		if( false === $items || is_wp_error( $items ) ) {
			return false;
		}
		
		// if $depth is not -1 or 1, should add 'menu-item-has-children' class by following https://core.trac.wordpress.org/browser/tags/4.2.2/src/wp-includes/nav-menu-template.php#L327
		
		$output = '<nav class="mdl-navigation">';
		
		if( ! empty( $items ) ) {
			foreach( $items as $key => $object ) {
				
				$child_class = '';
				//
				// NO multi-level menus at this time!
				//
				if( 1 == $depth && ! empty( $object->menu_item_parent ) ) {
					continue;
				}
				// -1 gets links at any depth and arranges them in a single, flat list
				if( -1 == $depth && ! empty( $object->menu_item_parent ) ) {
					$child_class = sprintf( ' menu-item-has-parent menu-item-parent-item-is-%d', $object->menu_item_parent );
				}
				
				$class = '';
				
				if( method_exists( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_sanitize_html_classes' ) ) {
					$link_class = self::mdl_sanitize_html_classes( $link_class );
				} else {
					// this will remove spaces so is faulty but better than not sanitizing at all
					$link_class = sanitize_html_class( $link_class );
				}
			
				if( $link_class ) {
					$class = $link_class . ' ';
				}
				$class .= sprintf( 'menu-item menu-item-type-%s menu-item-object-%s menu-item-%d%s',
					$object->type,
					$object->object,
					$object->ID,
					$child_class
				);
				$class .= implode( ' ', $object->classes );
				
				$target = '';
				if( $object->target ) {
					$target = sprintf( ' target="%s"', esc_attr( $object->target ) );
				}
				
				$attr_title = '';
				if( $object->attr_title ) {
					$attr_title = sprintf( ' title="%s"', esc_attr( $object->attr_title ) );
				}
				
				// https://codex.wordpress.org/Defining_Relationships_with_XFN
				$xfn = '';
				if( $object->xfn ) {
					$xfn = sprintf( ' rel="%s"', esc_attr( $object->xfn ) );
				}
				
				$description = '';
	/*
				if( $object->description ) {
					$description = sprintf( ' <span class="menu-item-description">%s</span>', $object->description );
				}
	*/
				
				$output .= sprintf( '<a class="mdl-navigation__link %s" href="%s"%s%s%s>%s</a>%s',
					$class,
					$object->url,
					$target,
					$attr_title,
					$xfn,
					$object->title,
					$description
				);
			} // end foreach
		}
		
		$output .= '</nav>';
		
		return $output;
	}
	
	
	// wp_get_nav_menu_items takes menu name, ID, or slug -- but shortcode only uses ID
	public static function mdl_build_menu_button_li_items( $menu = '', $li_class = 'mdl-menu__item', $depth = -1 ) {
		$depth = -1; // hard-coded for now
		$depth = intval( $depth );
			
		if( empty( $menu ) ) {
			return false;
		}
		
		$items = wp_get_nav_menu_items( $menu );
		
		if( false === $items || is_wp_error( $items ) ) {
			return false;
		}
		
		// if $depth is not -1 or 1, should add 'menu-item-has-children' class by following https://core.trac.wordpress.org/browser/tags/4.2.2/src/wp-includes/nav-menu-template.php#L327
		
		$output = '';
		
		if( ! empty( $items ) ) {
			foreach( $items as $key => $object ) {
				
				$child_class = '';
				//
				// NO multi-level menus at this time!
				//
				if( 1 == $depth && ! empty( $object->menu_item_parent ) ) {
					continue;
				}
				// -1 gets links at any depth and arranges them in a single, flat list
				if( -1 == $depth && ! empty( $object->menu_item_parent ) ) {
					$child_class = sprintf( ' menu-item-has-parent menu-item-parent-item-is-%d', $object->menu_item_parent );
				}
				
				if( method_exists( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_sanitize_html_classes' ) ) {
					$li_class = self::mdl_sanitize_html_classes( $li_class );
				} else {
					// this will remove spaces so is faulty but better than not sanitizing at all
					$li_class = sanitize_html_class( $li_class );
				}
			
				$link_classes = sprintf( 'menu-item menu-item-type-%s menu-item-object-%s menu-item-%d%s',
					$object->type,
					$object->object,
					$object->ID,
					$child_class
				);
				$link_classes .= implode( ' ', $object->classes );
				
				$target = '';
				if( $object->target ) {
					$target = sprintf( ' target="%s"', esc_attr( $object->target ) );
				}
				
				$attr_title = '';
				if( $object->attr_title ) {
					$attr_title = sprintf( ' title="%s"', esc_attr( $object->attr_title ) );
				}
				
				// https://codex.wordpress.org/Defining_Relationships_with_XFN
				$xfn = '';
				if( $object->xfn ) {
					$xfn = sprintf( ' rel="%s"', esc_attr( $object->xfn ) );
				}
				
				$description = '';
	/*
				if( $object->description ) {
					$description = sprintf( ' <span class="menu-item-description">%s</span>', $object->description );
				}
	*/
				
				$output .= sprintf( '<li class="%s"><a class="mdl-navigation__link %s" href="%s"%s%s%s>%s</a>%s</li>',
					$li_class,
					$link_classes,
					$object->url,
					$target,
					$attr_title,
					$xfn,
					$object->title,
					$description
				);
			} // end foreach
		}
		
		return $output;
	}
	
	
	/*
	public static function mdl_menu_wp_nav_menu( $menu_id = 0, $depth = -1 ) {
		$depth = -1; // hard-coded for now
		$depth = intval( $depth );
		
		add_filter('nav_menu_css_class' , 'special_nav_class' , 10 , 2);
		
		$args = array(
			'menu_id'			=> $menu_id,
			'echo'				=> false,
			'items_wrap'		=> '<ul id="%1$s" class="mdl-menu %2$s">%3$s</ul>', // add ul.mdl-menu
			'depth'				=> $depth,
		);
		
		$output = wp_nav_menu( $args );
		//$output = strip_tags( $output, '<a>' ); // we just want the links -- from https://css-tricks.com/snippets/wordpress/remove-li-elements-from-output-of-wp_nav_menu/
		
		if( false === $output ) {
			return false;
		}
		
		
		
		return $output;
	}
	*/
	
	
	// remove all but integers and commas from string -- from http://stackoverflow.com/a/5798519/893907
	// Example: $str = ", 3.3,,x,,, , 2 4b , , 3 , 2 4 ,,,,,";
	// results in: 33,24,3,24
	public static function mdl_only_integers_commas( $string = null ) {
		$value = preg_replace(
			array(
			'/[^\d,]/',    // Matches anything that's not a comma or number.
			'/(?<=,),+/',  // Matches consecutive commas.
			'/^,+/',       // Matches leading commas.
			'/,+$/'        // Matches trailing commas.
			),
			'',              // Remove all matched substrings.
			(string) $string //typecast as string -- string in, string out
		);
		
		return $value; // NULL and FALSE both return an empty string
	}
	
	
	// string is either 'true' or 'false'
	// and if it is not, set a default value
	public static function mdl_truefalse( $string, $default = '' ) {
		$string = trim ( strtolower( (string) $string ) );
		
		if ( array_key_exists( $string, self::mdl_true_false_selection_array( 'false' ) ) ) {
			return $string;
		} else {
			return $default;
		}
	}
	
	
	// h1-h6 array
	public static function mdl_allowed_htags_array( $return = 'select' ) {
		if( $return = 'select' ) {
			$output = array(
				''		=> esc_html__( 'h2 (Default)', 'mdl-shortcodes' ),
				'h1'	=> esc_html__( 'h1', 'mdl-shortcodes' ),
				'h3'	=> esc_html__( 'h3', 'mdl-shortcodes' ),
				'h4'	=> esc_html__( 'h4', 'mdl-shortcodes' ),
				'h5'	=> esc_html__( 'h5', 'mdl-shortcodes' ),
				'h6'	=> esc_html__( 'h6', 'mdl-shortcodes' ),
			);
		} else {
			$output = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
		}
		
		return $output;
	}
	
	
	// string is h1-h6
	// and if it is not, set a default value
	public static function mdl_htag( $string, $default = 'h2' ) {
		$string = trim( strtolower( (string) $string ) );
		
		$allowed_htags = self::mdl_allowed_htags_array( 'simple' );
		
		if ( in_array( $string, $allowed_htags) ) {
			return $string;
		} else {
			return $default;
		}
	}
	
	
	// string is something like a CSS length or position value (not perfect but works well enough -- e.g. +15exm does not validate but -20%x does)
	//
	// based on http://www.shamasis.net/2009/07/regular-expression-to-validate-css-length-and-position-values/
	// reference: http://www.w3schools.com/cssref/css_units.asp
	public static function mdl_restrict_css_unit_value( $string, $default = '' ) {
		$string = trim( strtolower( (string) $string ) );
		
		if ( false !== strpos( $string, 'auto')
			|| false !== strpos( $string, '0')
			|| preg_match( '/^[+-]?[0-9]+.?([0-9]+)?(em|ex|%|px|cm|mm|in|pt|pc|ch|rem|vh|vw|vmin|vmax)$/', $string )
		) {
			return $string;
		} else {
			return $default;
		}
	}
	
	
	// sanitize_html_classes() is from https://gist.github.com/justnorris/5387539
	//if ( ! function_exists( 'mdl_sanitize_html_classes' ) && function_exists( 'sanitize_html_class' ) ) {
		 // sanitize_html_class works just fine for a single class
		 // Some times le wild <span class="blue hedgehog"> appears, which is when you need this function,
		 // to validate both blue and hedgehog,
		 // Because sanitize_html_class doesn't allow spaces.
		 //
		 // @uses   sanitize_html_class
		 // @param  (mixed: string/array) $class   "blue hedgehog goes shopping" or array("blue", "hedgehog", "goes", "shopping")
		 // @param  (mixed) $fallback Anything you want returned in case of a failure
		 // @return (mixed: string / $fallback )
		public static function mdl_sanitize_html_classes( $class, $fallback = null ) {
			if ( empty( $class ) ) {
				return $fallback;
			}
			
			// Explode it, if it's a string
			if ( is_string( $class ) ) {
				$class = explode(' ', $class);
			}
			
			if ( is_array( $class ) && count( $class ) > 0 ) {
				$class = array_map('sanitize_html_class', $class);
				return implode(' ', $class);
			} else { 
				return sanitize_html_class( $class, $fallback );
			}
		}
	//}
	
	
	public static function mdl_wp_image_sizes_selection_array( $prepend_empty = 'true' ) {
		$allowed_options = get_intermediate_image_sizes(); // index-based array
		$allowed_options = array_combine( $allowed_options, $allowed_options ); // associative array
		
		if( 'true' == $prepend_empty ) {
			$allowed_options = array( '' => '' )+$allowed_options;
		}
				
		return $allowed_options;
	}
	
	
	public static function mdl_true_false_selection_array( $prepend_empty = 'true', $true = 'true', $false = 'true' ) {
		$allowed_options = array();
		
		if( 'true' == $prepend_empty ) {
			$allowed_options = array( '' => '' )+$allowed_options;
		}
		
		if( 'true' == $true ) {
			$allowed_options = $allowed_options + array( 'true' => esc_html__( 'True', 'mdl-shortcodes' ) );
		}
		
		if( 'true' == $false ) {
			$allowed_options = $allowed_options + array( 'false' => esc_html__( 'False', 'mdl-shortcodes' ) );
		}
			
		return $allowed_options;
	}
	
	
	public static function mdl_targets_selection_array( $prepend_empty = 'true' ) {
			
		$allowed_options = array(
			'_blank' => 'New Window or Tab (_blank)',
			'_self' => 'Same Frame (_self)',
			'_parent' => 'Parent Frame (_parent)',
			'_top' => 'Full Body of Window (_top)',
		);
		
		if( 'true' == $prepend_empty ) {
			$allowed_options = array( '' => '' )+$allowed_options;
		}
		
		return $allowed_options;
	}
	
	public static function mdl_url_target( $target = '', $fallback = '' ) {
		if ( empty( $target ) ) {
			return $fallback;
		}
		
		$target = strtolower( $target );
		
		if( substr( $target, 0, 1 ) !== '_' ) {
			$target = '_' . $target;
		}
		
		if( ! array_key_exists( $target, self::mdl_targets_selection_array() ) ) {
			$target = '';
		}
		
		if ( empty( $target ) ) {
			return $fallback;
		} else {
			return $target;
		}
	}
	
	
	// $item is filled automatically when used with array_walk_recursive
	public static function array_items_keep_each_from_start_end( $item, $start = '', $end = '' ) {
		$fail = false;
		
		if( ! isset( $item ) ) {
			$fail = true;
		}
		
		$end_length = strlen( $end );
		
		$start_position = strpos( $item, $start );
		
		if( false === $start_position ) {
			$fail = true;
		}
		
		$end_position = strpos( $item, $end );
		
		if( false === $end_position ) {
			$fail = true;
		} else {
			$end_position = $end_position + $end_length; // to keep $end in string
		}
		
		if( true === $fail ) {
			$item = '';
		} else {
			$item = strstr( $item, $start ); // trim before $start
			$item = substr( $item, 0, $end_position ); // trim after $end
		}
		
		return $item;
	}
	
	// substr_getbykeys() - Returns everything in a source string that exists between the first occurance of each of the two key substrings
	//          - only returns first match, and can be used in loops to iterate through large datasets
	//          - arg 1 is the first substring to look for
	//          - arg 2 is the second substring to look for
	//          - arg 3 is the source string the search is performed on.
	//          - arg 4 is boolean and allows you to determine if returned result should include the search keys.
	//          - arg 5 is boolean and can be used to determine whether search should be case-sensative or not.
	//
	// from http://stackoverflow.com/a/27611859
	public static function substr_getbykeys( $key1, $key2, $source, $returnkeys = false, $casematters = true ) {
		if ( $casematters === true ) {
			$start = strpos($source, $key1);
			$end = strpos($source, $key2);
		} else {
			$start = stripos($source, $key1);
			$end = stripos($source, $key2);
		}
		if ( false === $start || false === $end ) {
			return false;
		}
		if ( $start > $end ) {
			$temp = $start;
			$start = $end;
			$end = $temp;
		}
		if ( $returnkeys === true) {
			$length = ($end + strlen($key2)) - $start;
		} else {
			$start = $start + strlen($key1);
			$length = $end - $start;
		}
		return substr($source, $start, $length);
	}
	
	
	// DIV cannot be in P
	public static function mdl_cleanup_invalid_p_tags( $output = '' ) {
		$first_open_p = strpos( $output, '<p' );
		$first_closing_p = strpos( $output, '</p>' );
		
		// bail if no P tags to mess with
		if( false === $first_open_p && false === $first_closing_p ) {
			return $output;
		}
		
		if( false !== $first_open_p ) {
			$output = str_replace( '<p></div>', '</div>', $output );
		}
		
		if( false !== $first_closing_p ) {
			$output = str_replace( '</div></p>', '</div>', $output );
		}
		
		// since we're in a DIV and no DIV should be in an opened P tag, if there's a closing P tag before an opening one, remove the first closing one since it'd be invalid
		if( false !== $first_open_p && false !== $first_closing_p ) {
			if( $first_closing_p < $first_open_p ) {
				$output = substr_replace( $output, '', $first_closing_p, 0 );
			}
		}
		
		return $output;
	}
	
	
	
	
	//
	// implement Material Design Lite http://www.getmdl.io/started/
	//
	
	
	// MDL Typography choices
	public static function mdl_typography_text_array( $prepend_empty = 'true' ) {
		$allowed_options = array(
			'left'		=> esc_html__( 'Text-Align Left', 'mdl-shortcodes' ),
			'right'		=> esc_html__( 'Text-Align Right', 'mdl-shortcodes' ),
			'center'	=> esc_html__( 'Text-Align Center', 'mdl-shortcodes' ),
			'justify'	=> esc_html__( 'Text-Align Justify', 'mdl-shortcodes' ),
			'nowrap'	=> esc_html__( 'White-Space NoWrap', 'mdl-shortcodes' ),
			'lowercase'	=> esc_html__( 'Text-Transform Lowercase', 'mdl-shortcodes' ),
			'uppercase'	=> esc_html__( 'Text-Transform Uppercase', 'mdl-shortcodes' ),
			'capitalize' => esc_html__( 'Text-Transform Capitalize', 'mdl-shortcodes' ),
		);
		
		$output = $allowed_options;
		
		if( 'true' == $prepend_empty ) {
			$output = array( '' => '' )+$output;
		}
		
		return $output;
	}
	
	
	// MDL Effect
	public static function mdl_ripple_effect_array() {
		$allowed_options = array(
			''		=> esc_html__( 'Ripple', 'mdl-shortcodes' ),
			'false'	=> esc_html__( 'None', 'mdl-shortcodes' ),
		);
		
		$output = $allowed_options;
		
		return $output;
	}
	
	
	// MDL Cell Sizes
	public static function mdl_cell_sizes_array( /* $prepend_empty = 'true', */ $device = 'desktop'  ) {
		$allowed_options = array( 'desktop', 'tablet', 'phone' );
		if( ! in_array( $device, $allowed_options ) ) {
			$device = 'desktop';
		}
		
		$phone = array(
			0	=> esc_html__( '1', 'mdl-shortcodes' ),
			1	=> esc_html__( '2', 'mdl-shortcodes' ),
			2	=> esc_html__( '3', 'mdl-shortcodes' ),
			3	=> esc_html__( '4', 'mdl-shortcodes' ),
		);
		
		$tablet = array(
			4	=> esc_html__( '5', 'mdl-shortcodes' ),
			5	=> esc_html__( '6', 'mdl-shortcodes' ),
			6	=> esc_html__( '7', 'mdl-shortcodes' ),
			7	=> esc_html__( '8', 'mdl-shortcodes' ),
		);
		$tablet = array_merge( $phone, $tablet );
		
		$desktop = array(
			8	=> esc_html__( '9', 'mdl-shortcodes' ),
			9	=> esc_html__( '10', 'mdl-shortcodes' ),
			10	=> esc_html__( '11', 'mdl-shortcodes' ),
			11	=> esc_html__( '12', 'mdl-shortcodes' ),
		);
		$desktop = array_merge( $tablet, $desktop );
		
		
		$output = array();
		
		if( 'desktop' == $device ) {
			$output = $desktop;
		} elseif( 'tablet' == $device ) {
			$output = $tablet;
		} elseif( 'phone' == $device ) {
			$output = $phone;
		} else {
			//
		}
		
		// required due to PHP array looking like indexed array instead of associative array. Need a zero-key so when user selects "7" it doesn't output "6" to the WP Editor. -- this is also why there's no $prepend_empty on this function
		//
		//if( 'true' == $prepend_empty ) {
			$array_zero = array( '0' => esc_html__( 'Default', 'mdl-shortcodes' ) );
			$output = array_merge( $array_zero, $output );
		//}
		
		return $output;
	}
	
	
	// MDL Cell Flexbox Alignment
	public static function mdl_cell_flex_align_array( $prepend_empty = 'true'  ) {
		$allowed_options = array(
			'stretch'	=> esc_html__( 'Stretch', 'mdl-shortcodes' ),
			'top'		=> esc_html__( 'Top', 'mdl-shortcodes' ),
			'middle'	=> esc_html__( 'Middle', 'mdl-shortcodes' ),
			'bottom'	=> esc_html__( 'Bottom', 'mdl-shortcodes' ),
		);
		
		$output = $allowed_options;
		
		if( 'true' == $prepend_empty ) {
			$output = array( '' => '' )+$output;
		}
		
		return $output;
	}
	
	
	public static function mdl_icon_description_text( $text_before = '', $text_after = '' ) {
		return sprintf( esc_html__('%sValid Material Icon name from %s%s', 'mdl-shortcodes'), $text_before, 'https://www.google.com/design/icons/', $text_after );
	}
	
	public static function mdl_color_description_text( $text_before = '', $text_after = '' ) {
		return sprintf( esc_html__('%sPrimary and Accent colors are from your theme. Preview all color choices at %s%s', 'mdl-shortcodes'), $text_before, 'https://www.google.com/design/spec/style/color.html#color-color-palette', $text_after );
	}
	
	public static function mdl_classes_description_text( $text_before = '', $text_after = '' , $add_to = '' ) {
		return sprintf( esc_html__('%s(Advanced) Add custom CSS class(es) to %s.%s', 'mdl-shortcodes'), $text_before, $add_to, $text_after );
	}
	
	
	public static function mdl_text_background_colors_same( $text_color = '', $background_color = '' ) {
		$text_color = sanitize_html_class( $text_color );
		$background_color = sanitize_html_class( $background_color );
		
		if ( ! array_key_exists( $text_color, self::mdl_color_palette_classes_selection_array( 'false', 'text' ) ) ) {
			$text_color = '';
		}
		
		if ( ! array_key_exists( $background_color, self::mdl_color_palette_classes_selection_array( 'false', 'background' ) ) ) {
			$background_color = '';
		}
		
		if ( empty( $text_color ) && empty( $background_color ) ) {
			return 'true'; // err on side of caution -- likely did not enter arguments into the function -- with 'true', we hope to visually see colors aren't working as expected
		}
		
		if ( empty( $text_color ) || empty( $background_color ) ) {
			return 'false';
		}
		
		$text_color = str_replace( 'mdl-color-text--', '', $text_color );
		$background_color = str_replace( 'mdl-color--', '', $background_color );
		if ( $text_color == $background_color ) {
			return 'true';
		} else {
			return 'false';
		}
	}		
	
	
	public static function mdl_color_palette_classes_selection_array( $prepend_empty = 'true', $build = 'all' ) {
		$allowed_builds = array(
			'all', //everything
			'text', // both theme-text and common-text
			'background', // both theme-background and common-background
			'theme-text',
			'theme-background',
			'common-text',
			'common-background'
		);
		
		if ( ! in_array( $build, $allowed_builds ) ) {
			$build = 'all';
		}
		
		$allowed_options = array();
		
		if ( 'all' == $build || 'text' == $build || 'theme-text' == $build ) {
			$mdl_theme_text = array(
				'mdl-color-text--primary'			=> esc_html__( 'Primary', 'mdl-shortcodes' ),
				'mdl-color-text--primary-contrast'	=> esc_html__( 'Primary Contrast', 'mdl-shortcodes' ),
				'mdl-color-text--primary-dark'		=> esc_html__( 'Primary Dark', 'mdl-shortcodes' ),
				'mdl-color-text--accent'			=> esc_html__( 'Accent', 'mdl-shortcodes' ),
				'mdl-color-text--accent-contrast'	=> esc_html__( 'Accent Contrast', 'mdl-shortcodes' ),
			);
			
			$allowed_options = array_merge( $allowed_options, $mdl_theme_text );
		}
		
		if ( 'all' == $build || 'background' == $build || 'theme-background' == $build ) {
			$mdl_theme_background = array(
				'mdl-color--primary'				=> esc_html__( 'Primary color', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--primary-contrast'		=> esc_html__( 'Primary Contrast', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--primary-dark'			=> esc_html__( 'Primary Dark', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--accent'					=> esc_html__( 'Accent', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--accent-contrast'		=> esc_html__( 'Accent Contrast', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
			);
			
			$allowed_options = array_merge( $allowed_options, $mdl_theme_background );
		}
		
		if ( 'all' == $build || 'text' == $build || 'common-text' == $build ) {
			$mdl_text = array(
				// B&W
				'mdl-color-text--black'				=> esc_html__( 'Black', 'mdl-shortcodes' ),
				'mdl-color-text--white'				=> esc_html__( 'White', 'mdl-shortcodes' ),
				// Red
				'mdl-color-text--red'				=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--red-50'			=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--red-100'			=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--red-200'			=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--red-300'			=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--red-400'			=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--red-600'			=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--red-700'			=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--red-800'			=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--red-900'			=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				'mdl-color-text--red-A100'			=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ),
				'mdl-color-text--red-A200'			=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ),
				'mdl-color-text--red-A400'			=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ),
				'mdl-color-text--red-A700'			=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ),
				// Pink
				'mdl-color-text--pink'				=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--pink-50'			=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--pink-100'			=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--pink-200'			=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--pink-300'			=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--pink-400'			=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--pink-600'			=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--pink-700'			=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--pink-800'			=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--pink-900'			=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				'mdl-color-text--pink-A100'			=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ),
				'mdl-color-text--pink-A200'			=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ),
				'mdl-color-text--pink-A400'			=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ),
				'mdl-color-text--pink-A700'			=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ),
				// Purple
				'mdl-color-text--purple'			=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--purple-50'			=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--purple-100'		=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--purple-200'		=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--purple-300'		=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--purple-400'		=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--purple-600'		=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--purple-700'		=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--purple-800'		=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--purple-900'		=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				'mdl-color-text--purple-A100'		=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ),
				'mdl-color-text--purple-A200'		=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ),
				'mdl-color-text--purple-A400'		=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ),
				'mdl-color-text--purple-A700'		=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ),
				// Deep Purple
				'mdl-color-text--deep-purple'		=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--deep-purple-50'	=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--deep-purple-100'	=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--deep-purple-200'	=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--deep-purple-300'	=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--deep-purple-400'	=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--deep-purple-600'	=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--deep-purple-700'	=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--deep-purple-800'	=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--deep-purple-900'	=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				'mdl-color-text--deep-purple-A100'	=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ),
				'mdl-color-text--deep-purple-A200'	=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ),
				'mdl-color-text--deep-purple-A400'	=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ),
				'mdl-color-text--deep-purple-A700'	=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ),
				// Indigo
				'mdl-color-text--indigo'			=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--indigo-50'			=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--indigo-100'		=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--indigo-200'		=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--indigo-300'		=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--indigo-400'		=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--indigo-600'		=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--indigo-700'		=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--indigo-800'		=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--indigo-900'		=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				'mdl-color-text--indigo-A100'		=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ),
				'mdl-color-text--indigo-A200'		=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ),
				'mdl-color-text--indigo-A400'		=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ),
				'mdl-color-text--indigo-A700'		=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ),
				// Blue
				'mdl-color-text--blue'				=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--blue-50'			=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--blue-100'			=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--blue-200'			=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--blue-300'			=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--blue-400'			=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--blue-600'			=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--blue-700'			=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--blue-800'			=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--blue-900'			=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				'mdl-color-text--blue-A100'			=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ),
				'mdl-color-text--blue-A200'			=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ),
				'mdl-color-text--blue-A400'			=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ),
				'mdl-color-text--blue-A700'			=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ),
				// Light Blue
				'mdl-color-text--light-blue'		=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--light-blue-50'		=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--light-blue-100'	=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--light-blue-200'	=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--light-blue-300'	=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--light-blue-400'	=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--light-blue-600'	=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--light-blue-700'	=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--light-blue-800'	=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--light-blue-900'	=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				'mdl-color-text--light-blue-A100'	=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ),
				'mdl-color-text--light-blue-A200'	=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ),
				'mdl-color-text--light-blue-A400'	=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ),
				'mdl-color-text--light-blue-A700'	=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ),
				// Cyan
				'mdl-color-text--cyan'				=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--cyan-50'			=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--cyan-100'			=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--cyan-200'			=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--cyan-300'			=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--cyan-400'			=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--cyan-600'			=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--cyan-700'			=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--cyan-800'			=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--cyan-900'			=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				'mdl-color-text--cyan-A100'			=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ),
				'mdl-color-text--cyan-A200'			=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ),
				'mdl-color-text--cyan-A400'			=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ),
				'mdl-color-text--cyan-A700'			=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ),
				// Teal
				'mdl-color-text--teal'				=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--teal-50'			=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--teal-100'			=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--teal-200'			=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--teal-300'			=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--teal-400'			=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--teal-600'			=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--teal-700'			=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--teal-800'			=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--teal-900'			=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				'mdl-color-text--teal-A100'			=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ),
				'mdl-color-text--teal-A200'			=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ),
				'mdl-color-text--teal-A400'			=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ),
				'mdl-color-text--teal-A700'			=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ),
				// Green
				'mdl-color-text--green'				=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--green-50'			=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--green-100'			=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--green-200'			=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--green-300'			=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--green-400'			=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--green-600'			=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--green-700'			=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--green-800'			=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--green-900'			=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				'mdl-color-text--green-A100'		=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ),
				'mdl-color-text--green-A200'		=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ),
				'mdl-color-text--green-A400'		=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ),
				'mdl-color-text--green-A700'		=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ),
				// Light Green
				'mdl-color-text--light-green'		=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--light-green-50'	=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--light-green-100'	=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--light-green-200'	=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--light-green-300'	=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--light-green-400'	=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--light-green-600'	=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--light-green-700'	=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--light-green-800'	=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--light-green-900'	=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				'mdl-color-text--light-green-A100'	=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ),
				'mdl-color-text--light-green-A200'	=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ),
				'mdl-color-text--light-green-A400'	=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ),
				'mdl-color-text--light-green-A700'	=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ),
				// Lime
				'mdl-color-text--lime'				=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--lime-50'			=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--lime-100'			=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--lime-200'			=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--lime-300'			=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--lime-400'			=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--lime-600'			=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--lime-700'			=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--lime-800'			=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--lime-900'			=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				'mdl-color-text--lime-A100'			=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ),
				'mdl-color-text--lime-A200'			=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ),
				'mdl-color-text--lime-A400'			=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ),
				'mdl-color-text--lime-A700'			=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ),
				// Yellow
				'mdl-color-text--yellow'			=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--yellow-50'			=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--yellow-100'		=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--yellow-200'		=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--yellow-300'		=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--yellow-400'		=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--yellow-600'		=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--yellow-700'		=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--yellow-800'		=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--yellow-900'		=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				'mdl-color-text--yellow-A100'		=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ),
				'mdl-color-text--yellow-A200'		=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ),
				'mdl-color-text--yellow-A400'		=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ),
				'mdl-color-text--yellow-A700'		=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ),
				// Amber
				'mdl-color-text--amber'				=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--amber-50'			=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--amber-100'			=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--amber-200'			=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--amber-300'			=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--amber-400'			=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--amber-600'			=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--amber-700'			=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--amber-800'			=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--amber-900'			=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				'mdl-color-text--amber-A100'		=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ),
				'mdl-color-text--amber-A200'		=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ),
				'mdl-color-text--amber-A400'		=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ),
				'mdl-color-text--amber-A700'		=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ),
				// Orange
				'mdl-color-text--orange'			=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--orange-50'			=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--orange-100'		=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--orange-200'		=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--orange-300'		=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--orange-400'		=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--orange-600'		=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--orange-700'		=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--orange-800'		=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--orange-900'		=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				'mdl-color-text--orange-A100'		=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ),
				'mdl-color-text--orange-A200'		=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ),
				'mdl-color-text--orange-A400'		=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ),
				'mdl-color-text--orange-A700'		=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ),
				// Deep Orange
				'mdl-color-text--deep-orange'		=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--deep-orange-50'	=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--deep-orange-100'	=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--deep-orange-200'	=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--deep-orange-300'	=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--deep-orange-400'	=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--deep-orange-600'	=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--deep-orange-700'	=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--deep-orange-800'	=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--deep-orange-900'	=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				'mdl-color-text--deep-orange-A100'	=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ),
				'mdl-color-text--deep-orange-A200'	=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ),
				'mdl-color-text--deep-orange-A400'	=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ),
				'mdl-color-text--deep-orange-A700'	=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ),
				// Brown
				'mdl-color-text--brown'				=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--brown-50'			=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--brown-100'			=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--brown-200'			=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--brown-300'			=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--brown-400'			=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--brown-600'			=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--brown-700'			=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--brown-800'			=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--brown-900'			=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				// Grey
				'mdl-color-text--grey'				=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--grey-50'			=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--grey-100'			=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--grey-200'			=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--grey-300'			=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--grey-400'			=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--grey-600'			=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--grey-700'			=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--grey-800'			=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--grey-900'			=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
				// Blue Grey
				'mdl-color-text--blue-grey'			=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ),
				'mdl-color-text--blue-grey-50'		=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ),
				'mdl-color-text--blue-grey-100'		=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ),
				'mdl-color-text--blue-grey-200'		=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ),
				'mdl-color-text--blue-grey-300'		=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ),
				'mdl-color-text--blue-grey-400'		=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ),
				'mdl-color-text--blue-grey-600'		=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ),
				'mdl-color-text--blue-grey-700'		=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ),
				'mdl-color-text--blue-grey-800'		=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ),
				'mdl-color-text--blue-grey-900'		=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ),
			);
			
			$allowed_options = array_merge( $allowed_options, $mdl_text );
		}
		
		if ( 'all' == $build || 'background' == $build || 'common-background' == $build ) {
			$mdl_background = array(
				// B&W
				'mdl-color--black'					=> esc_html__( 'Black', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--white'					=> esc_html__( 'White', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Red
				'mdl-color--red'					=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--red-50'					=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--red-100'				=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--red-200'				=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--red-300'				=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--red-400'				=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--red-600'				=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--red-700'				=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--red-800'				=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--red-900'				=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--red-A100'				=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--red-A200'				=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--red-A400'				=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--red-A700'				=> esc_html__( 'Red', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Pink
				'mdl-color--pink'					=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--pink-50'				=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--pink-100'				=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--pink-200'				=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--pink-300'				=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--pink-400'				=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--pink-600'				=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--pink-700'				=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--pink-800'				=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--pink-900'				=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--pink-A100'				=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--pink-A200'				=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--pink-A400'				=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--pink-A700'				=> esc_html__( 'Pink', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Purple
				'mdl-color--purple'					=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--purple-50'				=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--purple-100'				=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--purple-200'				=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--purple-300'				=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--purple-400'				=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--purple-600'				=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--purple-700'				=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--purple-800'				=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--purple-900'				=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--purple-A100'			=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--purple-A200'			=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--purple-A400'			=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--purple-A700'			=> esc_html__( 'Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Deep Purple
				'mdl-color--deep-purple'			=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-purple-50'			=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-purple-100'		=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-purple-200'		=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-purple-300'		=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-purple-400'		=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-purple-600'		=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-purple-700'		=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-purple-800'		=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-purple-900'		=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-purple-A100'		=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-purple-A200'		=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-purple-A400'		=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-purple-A700'		=> esc_html__( 'Deep Purple', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Indigo
				'mdl-color--indigo'					=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--indigo-50'				=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--indigo-100'				=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--indigo-200'				=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--indigo-300'				=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--indigo-400'				=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--indigo-600'				=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--indigo-700'				=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--indigo-800'				=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--indigo-900'				=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--indigo-A100'			=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--indigo-A200'			=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--indigo-A400'			=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--indigo-A700'			=> esc_html__( 'Indigo', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Blue
				'mdl-color--blue'					=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-50'				=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-100'				=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-200'				=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-300'				=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-400'				=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-600'				=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-700'				=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-800'				=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-900'				=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-A100'				=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-A200'				=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-A400'				=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-A700'				=> esc_html__( 'Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Light Blue
				'mdl-color--light-blue'				=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-blue-50'			=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-blue-100'			=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-blue-200'			=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-blue-300'			=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-blue-400'			=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-blue-600'			=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-blue-700'			=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-blue-800'			=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-blue-900'			=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-blue-A100'		=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-blue-A200'		=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-blue-A400'		=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-blue-A700'		=> esc_html__( 'Light Blue', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Cyan
				'mdl-color--cyan'					=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--cyan-50'				=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--cyan-100'				=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--cyan-200'				=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--cyan-300'				=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--cyan-400'				=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--cyan-600'				=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--cyan-700'				=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--cyan-800'				=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--cyan-900'				=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--cyan-A100'				=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--cyan-A200'				=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--cyan-A400'				=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--cyan-A700'				=> esc_html__( 'Cyan', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Teal
				'mdl-color--teal'					=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--teal-50'				=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--teal-100'				=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--teal-200'				=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--teal-300'				=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--teal-400'				=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--teal-600'				=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--teal-700'				=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--teal-800'				=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--teal-900'				=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--teal-A100'				=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--teal-A200'				=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--teal-A400'				=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--teal-A700'				=> esc_html__( 'Teal', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Green
				'mdl-color--green'					=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--green-50'				=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--green-100'				=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--green-200'				=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--green-300'				=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--green-400'				=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--green-600'				=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--green-700'				=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--green-800'				=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--green-900'				=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--green-A100'				=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--green-A200'				=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--green-A400'				=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--green-A700'				=> esc_html__( 'Green', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Light Green
				'mdl-color--light-green'			=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-green-50'			=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-green-100'		=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-green-200'		=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-green-300'		=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-green-400'		=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-green-600'		=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-green-700'		=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-green-800'		=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-green-900'		=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-green-A100'		=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-green-A200'		=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-green-A400'		=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--light-green-A700'		=> esc_html__( 'Light Green', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Lime
				'mdl-color--lime'					=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--lime-50'				=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--lime-100'				=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--lime-200'				=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--lime-300'				=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--lime-400'				=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--lime-600'				=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--lime-700'				=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--lime-800'				=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--lime-900'				=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--lime-A100'				=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--lime-A200'				=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--lime-A400'				=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--lime-A700'				=> esc_html__( 'Lime', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Yellow
				'mdl-color--yellow'					=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--yellow-50'				=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--yellow-100'				=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--yellow-200'				=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--yellow-300'				=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--yellow-400'				=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--yellow-600'				=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--yellow-700'				=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--yellow-800'				=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--yellow-900'				=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--yellow-A100'			=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--yellow-A200'			=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--yellow-A400'			=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--yellow-A700'			=> esc_html__( 'Yellow', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Amber
				'mdl-color--amber'					=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--amber-50'				=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--amber-100'				=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--amber-200'				=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--amber-300'				=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--amber-400'				=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--amber-600'				=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--amber-700'				=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--amber-800'				=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--amber-900'				=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--amber-A100'				=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--amber-A200'				=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--amber-A400'				=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--amber-A700'				=> esc_html__( 'Amber', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Orange
				'mdl-color--orange'					=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--orange-50'				=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--orange-100'				=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--orange-200'				=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--orange-300'				=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--orange-400'				=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--orange-600'				=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--orange-700'				=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--orange-800'				=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--orange-900'				=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--orange-A100'			=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--orange-A200'			=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--orange-A400'			=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--orange-A700'			=> esc_html__( 'Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Deep Orange
				'mdl-color--deep-orange'			=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-orange-50'			=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-orange-100'		=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-orange-200'		=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-orange-300'		=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-orange-400'		=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-orange-600'		=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-orange-700'		=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-orange-800'		=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-orange-900'		=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-orange-A100'		=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-orange-A200'		=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-orange-A400'		=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--deep-orange-A700'		=> esc_html__( 'Deep Orange', 'mdl-shortcodes' ) . ' ' . esc_html__( 'A700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Brown
				'mdl-color--brown'					=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--brown-50'				=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--brown-100'				=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--brown-200'				=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--brown-300'				=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--brown-400'				=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--brown-600'				=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--brown-700'				=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--brown-800'				=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--brown-900'				=> esc_html__( 'Brown', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Grey
				'mdl-color--grey'					=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--grey-50'				=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--grey-100'				=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--grey-200'				=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--grey-300'				=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--grey-400'				=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--grey-600'				=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--grey-700'				=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--grey-800'				=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--grey-900'				=> esc_html__( 'Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				// Blue Grey
				'mdl-color--blue-grey'				=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '500', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-grey-50'			=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '50', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-grey-100'			=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '100', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-grey-200'			=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '200', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-grey-300'			=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '300', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-grey-400'			=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '400', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-grey-600'			=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '600', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-grey-700'			=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '700', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-grey-800'			=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '800', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
				'mdl-color--blue-grey-900'			=> esc_html__( 'Blue Grey', 'mdl-shortcodes' ) . ' ' . esc_html__( '900', 'mdl-shortcodes' ) . ' ' . esc_html__( 'as background', 'mdl-shortcodes' ),
			);
			
			$allowed_options = array_merge( $allowed_options, $mdl_background );
		}
		
		if( 'true' == $prepend_empty ) {
			$allowed_options = array( '' => '' )+$allowed_options;
		}
		
		return $allowed_options;
	}
	
	
	public static function mdl_icons_selection_array( $prepend_empty = 'true' ) {
		// Whitelist of all Material Design icon names
		// Current as of July 20, 2015 -- from https://github.com/google/material-design-icons/blob/master/iconfont/codepoints
		// To preview (and search through) all Material Design icons, visit https://www.google.com/design/icons/
		// TODO: possibly add support for IE9 or below:
			// For modern browsers: <i class="material-icons">description</i>
			// For IE9 or below: <i class="material-icons">&#xE873;</i>
		//
		// Icon names come from https://github.com/google/material-design-icons/blob/master/iconfont/MaterialIcons-Regular.ijmap
		// Might come in handy:
			// http://konklone.io/json/
			// http://stackoverflow.com/questions/15993038/how-to-traverse-json-object-locating-particular-property-and-pushing-its-content
				
		$allowed_options = array(
			'3d_rotation' => '3d Rotation',
			'access_alarm' => 'Access Alarm',
			'access_alarms' => 'Access Alarms',
			'access_time' => 'Access Time',
			'accessibility' => 'Accessibility',
			'account_balance' => 'Account Balance',
			'account_balance_wallet' => 'Account Balance Wallet',
			'account_box' => 'Account Box',
			'account_circle' => 'Account Circle',
			'adb' => 'Adb',
			'add' => 'Add',
			'add_alarm' => 'Add Alarm',
			'add_alert' => 'Add Alert',
			'add_box' => 'Add Box',
			'add_circle' => 'Add Circle',
			'add_circle_outline' => 'Add Circle Outline',
			'add_shopping_cart' => 'Add Shopping Cart',
			'add_to_photos' => 'Add To Photos',
			'adjust' => 'Adjust',
			'airline_seat_flat' => 'Airline Seat Flat',
			'airline_seat_flat_angled' => 'Airline Seat Flat Angled',
			'airline_seat_individual_suite' => 'Airline Seat Individual Suite',
			'airline_seat_legroom_extra' => 'Airline Seat Legroom Extra',
			'airline_seat_legroom_normal' => 'Airline Seat Legroom Normal',
			'airline_seat_legroom_reduced' => 'Airline Seat Legroom Reduced',
			'airline_seat_recline_extra' => 'Airline Seat Recline Extra',
			'airline_seat_recline_normal' => 'Airline Seat Recline Normal',
			'airplanemode_active' => 'Airplanemode Active',
			'airplanemode_inactive' => 'Airplanemode Inactive',
			'airplay' => 'Airplay',
			'alarm' => 'Alarm',
			'alarm_add' => 'Alarm Add',
			'alarm_off' => 'Alarm Off',
			'alarm_on' => 'Alarm On',
			'album' => 'Album',
			'android' => 'Android',
			'announcement' => 'Announcement',
			'apps' => 'Apps',
			'archive' => 'Archive',
			'arrow_back' => 'Arrow Back',
			'arrow_drop_down' => 'Arrow Drop Down',
			'arrow_drop_down_circle' => 'Arrow Drop Down Circle',
			'arrow_drop_up' => 'Arrow Drop Up',
			'arrow_forward' => 'Arrow Forward',
			'aspect_ratio' => 'Aspect Ratio',
			'assessment' => 'Assessment',
			'assignment' => 'Assignment',
			'assignment_ind' => 'Assignment Ind',
			'assignment_late' => 'Assignment Late',
			'assignment_return' => 'Assignment Return',
			'assignment_returned' => 'Assignment Returned',
			'assignment_turned_in' => 'Assignment Turned In',
			'assistant' => 'Assistant',
			'assistant_photo' => 'Assistant Photo',
			'attach_file' => 'Attach File',
			'attach_money' => 'Attach Money',
			'attachment' => 'Attachment',
			'audiotrack' => 'Audiotrack',
			'autorenew' => 'Autorenew',
			'av_timer' => 'Av Timer',
			'backspace' => 'Backspace',
			'backup' => 'Backup',
			'battery_alert' => 'Battery Alert',
			'battery_charging_full' => 'Battery Charging Full',
			'battery_full' => 'Battery Full',
			'battery_std' => 'Battery Std',
			'battery_unknown' => 'Battery Unknown',
			'beenhere' => 'Beenhere',
			'block' => 'Block',
			'bluetooth' => 'Bluetooth',
			'bluetooth_audio' => 'Bluetooth Audio',
			'bluetooth_connected' => 'Bluetooth Connected',
			'bluetooth_disabled' => 'Bluetooth Disabled',
			'bluetooth_searching' => 'Bluetooth Searching',
			'blur_circular' => 'Blur Circular',
			'blur_linear' => 'Blur Linear',
			'blur_off' => 'Blur Off',
			'blur_on' => 'Blur On',
			'book' => 'Book',
			'bookmark' => 'Bookmark',
			'bookmark_border' => 'Bookmark Border',
			'border_all' => 'Border All',
			'border_bottom' => 'Border Bottom',
			'border_clear' => 'Border Clear',
			'border_color' => 'Border Color',
			'border_horizontal' => 'Border Horizontal',
			'border_inner' => 'Border Inner',
			'border_left' => 'Border Left',
			'border_outer' => 'Border Outer',
			'border_right' => 'Border Right',
			'border_style' => 'Border Style',
			'border_top' => 'Border Top',
			'border_vertical' => 'Border Vertical',
			'brightness_1' => 'Brightness 1',
			'brightness_2' => 'Brightness 2',
			'brightness_3' => 'Brightness 3',
			'brightness_4' => 'Brightness 4',
			'brightness_5' => 'Brightness 5',
			'brightness_6' => 'Brightness 6',
			'brightness_7' => 'Brightness 7',
			'brightness_auto' => 'Brightness Auto',
			'brightness_high' => 'Brightness High',
			'brightness_low' => 'Brightness Low',
			'brightness_medium' => 'Brightness Medium',
			'broken_image' => 'Broken Image',
			'brush' => 'Brush',
			'bug_report' => 'Bug Report',
			'build' => 'Build',
			'business' => 'Business',
			'cached' => 'Cached',
			'cake' => 'Cake',
			'call' => 'Call',
			'call_end' => 'Call End',
			'call_made' => 'Call Made',
			'call_merge' => 'Call Merge',
			'call_missed' => 'Call Missed',
			'call_received' => 'Call Received',
			'call_split' => 'Call Split',
			'camera' => 'Camera',
			'camera_alt' => 'Camera Alt',
			'camera_enhance' => 'Camera Enhance',
			'camera_front' => 'Camera Front',
			'camera_rear' => 'Camera Rear',
			'camera_roll' => 'Camera Roll',
			'cancel' => 'Cancel',
			'card_giftcard' => 'Card Giftcard',
			'card_membership' => 'Card Membership',
			'card_travel' => 'Card Travel',
			'cast' => 'Cast',
			'cast_connected' => 'Cast Connected',
			'center_focus_strong' => 'Center Focus Strong',
			'center_focus_weak' => 'Center Focus Weak',
			'change_history' => 'Change History',
			'chat' => 'Chat',
			'chat_bubble' => 'Chat Bubble',
			'chat_bubble_outline' => 'Chat Bubble Outline',
			'check' => 'Check',
			'check_box' => 'Check Box',
			'check_box_outline_blank' => 'Check Box Outline Blank',
			'check_circle' => 'Check Circle',
			'chevron_left' => 'Chevron Left',
			'chevron_right' => 'Chevron Right',
			'chrome_reader_mode' => 'Chrome Reader Mode',
			'class' => 'Class',
			'clear' => 'Clear',
			'clear_all' => 'Clear All',
			'close' => 'Close',
			'closed_caption' => 'Closed Caption',
			'cloud' => 'Cloud',
			'cloud_circle' => 'Cloud Circle',
			'cloud_done' => 'Cloud Done',
			'cloud_download' => 'Cloud Download',
			'cloud_off' => 'Cloud Off',
			'cloud_queue' => 'Cloud Queue',
			'cloud_upload' => 'Cloud Upload',
			'code' => 'Code',
			'collections' => 'Collections',
			'collections_bookmark' => 'Collections Bookmark',
			'color_lens' => 'Color Lens',
			'colorize' => 'Colorize',
			'comment' => 'Comment',
			'compare' => 'Compare',
			'computer' => 'Computer',
			'confirmation_number' => 'Confirmation Number',
			'contact_phone' => 'Contact Phone',
			'contacts' => 'Contacts',
			'content_copy' => 'Content Copy',
			'content_cut' => 'Content Cut',
			'content_paste' => 'Content Paste',
			'control_point' => 'Control Point',
			'control_point_duplicate' => 'Control Point Duplicate',
			'create' => 'Create',
			'credit_card' => 'Credit Card',
			'crop' => 'Crop',
			'crop_16_9' => 'Crop 16 9',
			'crop_3_2' => 'Crop 3 2',
			'crop_5_4' => 'Crop 5 4',
			'crop_7_5' => 'Crop 7 5',
			'crop_din' => 'Crop Din',
			'crop_free' => 'Crop Free',
			'crop_landscape' => 'Crop Landscape',
			'crop_original' => 'Crop Original',
			'crop_portrait' => 'Crop Portrait',
			'crop_square' => 'Crop Square',
			'dashboard' => 'Dashboard',
			'data_usage' => 'Data Usage',
			'dehaze' => 'Dehaze',
			'delete' => 'Delete',
			'description' => 'Description',
			'desktop_mac' => 'Desktop Mac',
			'desktop_windows' => 'Desktop Windows',
			'details' => 'Details',
			'developer_board' => 'Developer Board',
			'developer_mode' => 'Developer Mode',
			'device_hub' => 'Device Hub',
			'devices' => 'Devices',
			'dialer_sip' => 'Dialer Sip',
			'dialpad' => 'Dialpad',
			'directions' => 'Directions',
			'directions_bike' => 'Directions Bike',
			'directions_boat' => 'Directions Boat',
			'directions_bus' => 'Directions Bus',
			'directions_car' => 'Directions Car',
			'directions_railway' => 'Directions Railway',
			'directions_run' => 'Directions Run',
			'directions_subway' => 'Directions Subway',
			'directions_transit' => 'Directions Transit',
			'directions_walk' => 'Directions Walk',
			'disc_full' => 'Disc Full',
			'dns' => 'Dns',
			'do_not_disturb' => 'Do Not Disturb',
			'do_not_disturb_alt' => 'Do Not Disturb Alt',
			'dock' => 'Dock',
			'domain' => 'Domain',
			'done' => 'Done',
			'done_all' => 'Done All',
			'drafts' => 'Drafts',
			'drive_eta' => 'Drive Eta',
			'dvr' => 'Dvr',
			'edit' => 'Edit',
			'eject' => 'Eject',
			'email' => 'Email',
			'equalizer' => 'Equalizer',
			'error' => 'Error',
			'error_outline' => 'Error Outline',
			'event' => 'Event',
			'event_available' => 'Event Available',
			'event_busy' => 'Event Busy',
			'event_note' => 'Event Note',
			'event_seat' => 'Event Seat',
			'exit_to_app' => 'Exit To App',
			'expand_less' => 'Expand Less',
			'expand_more' => 'Expand More',
			'explicit' => 'Explicit',
			'explore' => 'Explore',
			'exposure' => 'Exposure',
			'exposure_neg_1' => 'Exposure Neg 1',
			'exposure_neg_2' => 'Exposure Neg 2',
			'exposure_plus_1' => 'Exposure Plus 1',
			'exposure_plus_2' => 'Exposure Plus 2',
			'exposure_zero' => 'Exposure Zero',
			'extension' => 'Extension',
			'face' => 'Face',
			'fast_forward' => 'Fast Forward',
			'fast_rewind' => 'Fast Rewind',
			'favorite' => 'Favorite',
			'favorite_border' => 'Favorite Border',
			'feedback' => 'Feedback',
			'file_download' => 'File Download',
			'file_upload' => 'File Upload',
			'filter' => 'Filter',
			'filter_1' => 'Filter 1',
			'filter_2' => 'Filter 2',
			'filter_3' => 'Filter 3',
			'filter_4' => 'Filter 4',
			'filter_5' => 'Filter 5',
			'filter_6' => 'Filter 6',
			'filter_7' => 'Filter 7',
			'filter_8' => 'Filter 8',
			'filter_9' => 'Filter 9',
			'filter_9_plus' => 'Filter 9 Plus',
			'filter_b_and_w' => 'Filter B And W',
			'filter_center_focus' => 'Filter Center Focus',
			'filter_drama' => 'Filter Drama',
			'filter_frames' => 'Filter Frames',
			'filter_hdr' => 'Filter Hdr',
			'filter_list' => 'Filter List',
			'filter_none' => 'Filter None',
			'filter_tilt_shift' => 'Filter Tilt Shift',
			'filter_vintage' => 'Filter Vintage',
			'find_in_page' => 'Find In Page',
			'find_replace' => 'Find Replace',
			'flag' => 'Flag',
			'flare' => 'Flare',
			'flash_auto' => 'Flash Auto',
			'flash_off' => 'Flash Off',
			'flash_on' => 'Flash On',
			'flight' => 'Flight',
			'flight_land' => 'Flight Land',
			'flight_takeoff' => 'Flight Takeoff',
			'flip' => 'Flip',
			'flip_to_back' => 'Flip To Back',
			'flip_to_front' => 'Flip To Front',
			'folder' => 'Folder',
			'folder_open' => 'Folder Open',
			'folder_shared' => 'Folder Shared',
			'folder_special' => 'Folder Special',
			'font_download' => 'Font Download',
			'format_align_center' => 'Format Align Center',
			'format_align_justify' => 'Format Align Justify',
			'format_align_left' => 'Format Align Left',
			'format_align_right' => 'Format Align Right',
			'format_bold' => 'Format Bold',
			'format_clear' => 'Format Clear',
			'format_color_fill' => 'Format Color Fill',
			'format_color_reset' => 'Format Color Reset',
			'format_color_text' => 'Format Color Text',
			'format_indent_decrease' => 'Format Indent Decrease',
			'format_indent_increase' => 'Format Indent Increase',
			'format_italic' => 'Format Italic',
			'format_line_spacing' => 'Format Line Spacing',
			'format_list_bulleted' => 'Format List Bulleted',
			'format_list_numbered' => 'Format List Numbered',
			'format_paint' => 'Format Paint',
			'format_quote' => 'Format Quote',
			'format_size' => 'Format Size',
			'format_strikethrough' => 'Format Strikethrough',
			'format_textdirection_l_to_r' => 'Format Textdirection L To R',
			'format_textdirection_r_to_l' => 'Format Textdirection R To L',
			'format_underlined' => 'Format Underlined',
			'forum' => 'Forum',
			'forward' => 'Forward',
			'forward_10' => 'Forward 10',
			'forward_30' => 'Forward 30',
			'forward_5' => 'Forward 5',
			'fullscreen' => 'Fullscreen',
			'fullscreen_exit' => 'Fullscreen Exit',
			'functions' => 'Functions',
			'gamepad' => 'Gamepad',
			'games' => 'Games',
			'gesture' => 'Gesture',
			'get_app' => 'Get App',
			'gif' => 'Gif',
			'gps_fixed' => 'Gps Fixed',
			'gps_not_fixed' => 'Gps Not Fixed',
			'gps_off' => 'Gps Off',
			'grade' => 'Grade',
			'gradient' => 'Gradient',
			'grain' => 'Grain',
			'graphic_eq' => 'Graphic Eq',
			'grid_off' => 'Grid Off',
			'grid_on' => 'Grid On',
			'group' => 'Group',
			'group_add' => 'Group Add',
			'group_work' => 'Group Work',
			'hd' => 'Hd',
			'hdr_off' => 'Hdr Off',
			'hdr_on' => 'Hdr On',
			'hdr_strong' => 'Hdr Strong',
			'hdr_weak' => 'Hdr Weak',
			'headset' => 'Headset',
			'headset_mic' => 'Headset Mic',
			'healing' => 'Healing',
			'hearing' => 'Hearing',
			'help' => 'Help',
			'help_outline' => 'Help Outline',
			'high_quality' => 'High Quality',
			'highlight_off' => 'Highlight Off',
			'history' => 'History',
			'home' => 'Home',
			'hotel' => 'Hotel',
			'hourglass_empty' => 'Hourglass Empty',
			'hourglass_full' => 'Hourglass Full',
			'http' => 'Http',
			'https' => 'Https',
			'image' => 'Image',
			'image_aspect_ratio' => 'Image Aspect Ratio',
			'import_export' => 'Import Export',
			'inbox' => 'Inbox',
			'indeterminate_check_box' => 'Indeterminate Check Box',
			'info' => 'Info',
			'info_outline' => 'Info Outline',
			'input' => 'Input',
			'insert_chart' => 'Insert Chart',
			'insert_comment' => 'Insert Comment',
			'insert_drive_file' => 'Insert Drive File',
			'insert_emoticon' => 'Insert Emoticon',
			'insert_invitation' => 'Insert Invitation',
			'insert_link' => 'Insert Link',
			'insert_photo' => 'Insert Photo',
			'invert_colors' => 'Invert Colors',
			'invert_colors_off' => 'Invert Colors Off',
			'iso' => 'Iso',
			'keyboard' => 'Keyboard',
			'keyboard_arrow_down' => 'Keyboard Arrow Down',
			'keyboard_arrow_left' => 'Keyboard Arrow Left',
			'keyboard_arrow_right' => 'Keyboard Arrow Right',
			'keyboard_arrow_up' => 'Keyboard Arrow Up',
			'keyboard_backspace' => 'Keyboard Backspace',
			'keyboard_capslock' => 'Keyboard Capslock',
			'keyboard_hide' => 'Keyboard Hide',
			'keyboard_return' => 'Keyboard Return',
			'keyboard_tab' => 'Keyboard Tab',
			'keyboard_voice' => 'Keyboard Voice',
			'label' => 'Label',
			'label_outline' => 'Label Outline',
			'landscape' => 'Landscape',
			'language' => 'Language',
			'laptop' => 'Laptop',
			'laptop_chromebook' => 'Laptop Chromebook',
			'laptop_mac' => 'Laptop Mac',
			'laptop_windows' => 'Laptop Windows',
			'launch' => 'Launch',
			'layers' => 'Layers',
			'layers_clear' => 'Layers Clear',
			'leak_add' => 'Leak Add',
			'leak_remove' => 'Leak Remove',
			'lens' => 'Lens',
			'library_add' => 'Library Add',
			'library_books' => 'Library Books',
			'library_music' => 'Library Music',
			'link' => 'Link',
			'list' => 'List',
			'live_help' => 'Live Help',
			'live_tv' => 'Live Tv',
			'local_activity' => 'Local Activity',
			'local_airport' => 'Local Airport',
			'local_atm' => 'Local Atm',
			'local_bar' => 'Local Bar',
			'local_cafe' => 'Local Cafe',
			'local_car_wash' => 'Local Car Wash',
			'local_convenience_store' => 'Local Convenience Store',
			'local_dining' => 'Local Dining',
			'local_drink' => 'Local Drink',
			'local_florist' => 'Local Florist',
			'local_gas_station' => 'Local Gas Station',
			'local_grocery_store' => 'Local Grocery Store',
			'local_hospital' => 'Local Hospital',
			'local_hotel' => 'Local Hotel',
			'local_laundry_service' => 'Local Laundry Service',
			'local_library' => 'Local Library',
			'local_mall' => 'Local Mall',
			'local_movies' => 'Local Movies',
			'local_offer' => 'Local Offer',
			'local_parking' => 'Local Parking',
			'local_pharmacy' => 'Local Pharmacy',
			'local_phone' => 'Local Phone',
			'local_pizza' => 'Local Pizza',
			'local_play' => 'Local Play',
			'local_post_office' => 'Local Post Office',
			'local_printshop' => 'Local Printshop',
			'local_see' => 'Local See',
			'local_shipping' => 'Local Shipping',
			'local_taxi' => 'Local Taxi',
			'location_city' => 'Location City',
			'location_disabled' => 'Location Disabled',
			'location_off' => 'Location Off',
			'location_on' => 'Location On',
			'location_searching' => 'Location Searching',
			'lock' => 'Lock',
			'lock_open' => 'Lock Open',
			'lock_outline' => 'Lock Outline',
			'looks' => 'Looks',
			'looks_3' => 'Looks 3',
			'looks_4' => 'Looks 4',
			'looks_5' => 'Looks 5',
			'looks_6' => 'Looks 6',
			'looks_one' => 'Looks One',
			'looks_two' => 'Looks Two',
			'loop' => 'Loop',
			'loupe' => 'Loupe',
			'loyalty' => 'Loyalty',
			'mail' => 'Mail',
			'map' => 'Map',
			'markunread' => 'Markunread',
			'markunread_mailbox' => 'Markunread Mailbox',
			'memory' => 'Memory',
			'menu' => 'Menu',
			'merge_type' => 'Merge Type',
			'message' => 'Message',
			'mic' => 'Mic',
			'mic_none' => 'Mic None',
			'mic_off' => 'Mic Off',
			'mms' => 'Mms',
			'mode_comment' => 'Mode Comment',
			'mode_edit' => 'Mode Edit',
			'money_off' => 'Money Off',
			'monochrome_photos' => 'Monochrome Photos',
			'mood' => 'Mood',
			'mood_bad' => 'Mood Bad',
			'more' => 'More',
			'more_horiz' => 'More Horiz',
			'more_vert' => 'More Vert',
			'mouse' => 'Mouse',
			'movie' => 'Movie',
			'movie_creation' => 'Movie Creation',
			'music_note' => 'Music Note',
			'my_location' => 'My Location',
			'nature' => 'Nature',
			'nature_people' => 'Nature People',
			'navigate_before' => 'Navigate Before',
			'navigate_next' => 'Navigate Next',
			'navigation' => 'Navigation',
			'network_cell' => 'Network Cell',
			'network_locked' => 'Network Locked',
			'network_wifi' => 'Network Wifi',
			'new_releases' => 'New Releases',
			'nfc' => 'Nfc',
			'no_sim' => 'No Sim',
			'not_interested' => 'Not Interested',
			'note_add' => 'Note Add',
			'notifications' => 'Notifications',
			'notifications_active' => 'Notifications Active',
			'notifications_none' => 'Notifications None',
			'notifications_off' => 'Notifications Off',
			'notifications_paused' => 'Notifications Paused',
			'offline_pin' => 'Offline Pin',
			'ondemand_video' => 'Ondemand Video',
			'open_in_browser' => 'Open In Browser',
			'open_in_new' => 'Open In New',
			'open_with' => 'Open With',
			'pages' => 'Pages',
			'pageview' => 'Pageview',
			'palette' => 'Palette',
			'panorama' => 'Panorama',
			'panorama_fish_eye' => 'Panorama Fish Eye',
			'panorama_horizontal' => 'Panorama Horizontal',
			'panorama_vertical' => 'Panorama Vertical',
			'panorama_wide_angle' => 'Panorama Wide Angle',
			'party_mode' => 'Party Mode',
			'pause' => 'Pause',
			'pause_circle_filled' => 'Pause Circle Filled',
			'pause_circle_outline' => 'Pause Circle Outline',
			'payment' => 'Payment',
			'people' => 'People',
			'people_outline' => 'People Outline',
			'perm_camera_mic' => 'Perm Camera Mic',
			'perm_contact_calendar' => 'Perm Contact Calendar',
			'perm_data_setting' => 'Perm Data Setting',
			'perm_device_information' => 'Perm Device Information',
			'perm_identity' => 'Perm Identity',
			'perm_media' => 'Perm Media',
			'perm_phone_msg' => 'Perm Phone Msg',
			'perm_scan_wifi' => 'Perm Scan Wifi',
			'person' => 'Person',
			'person_add' => 'Person Add',
			'person_outline' => 'Person Outline',
			'person_pin' => 'Person Pin',
			'personal_video' => 'Personal Video',
			'phone' => 'Phone',
			'phone_android' => 'Phone Android',
			'phone_bluetooth_speaker' => 'Phone Bluetooth Speaker',
			'phone_forwarded' => 'Phone Forwarded',
			'phone_in_talk' => 'Phone In Talk',
			'phone_iphone' => 'Phone Iphone',
			'phone_locked' => 'Phone Locked',
			'phone_missed' => 'Phone Missed',
			'phone_paused' => 'Phone Paused',
			'phonelink' => 'Phonelink',
			'phonelink_erase' => 'Phonelink Erase',
			'phonelink_lock' => 'Phonelink Lock',
			'phonelink_off' => 'Phonelink Off',
			'phonelink_ring' => 'Phonelink Ring',
			'phonelink_setup' => 'Phonelink Setup',
			'photo' => 'Photo',
			'photo_album' => 'Photo Album',
			'photo_camera' => 'Photo Camera',
			'photo_library' => 'Photo Library',
			'photo_size_select_actual' => 'Photo Size Select Actual',
			'photo_size_select_large' => 'Photo Size Select Large',
			'photo_size_select_small' => 'Photo Size Select Small',
			'picture_as_pdf' => 'Picture As Pdf',
			'picture_in_picture' => 'Picture In Picture',
			'pin_drop' => 'Pin Drop',
			'place' => 'Place',
			'play_arrow' => 'Play Arrow',
			'play_circle_filled' => 'Play Circle Filled',
			'play_circle_outline' => 'Play Circle Outline',
			'play_for_work' => 'Play For Work',
			'playlist_add' => 'Playlist Add',
			'plus_one' => 'Plus One',
			'poll' => 'Poll',
			'polymer' => 'Polymer',
			'portable_wifi_off' => 'Portable Wifi Off',
			'portrait' => 'Portrait',
			'power' => 'Power',
			'power_input' => 'Power Input',
			'power_settings_new' => 'Power Settings New',
			'present_to_all' => 'Present To All',
			'print' => 'Print',
			'public' => 'Public',
			'publish' => 'Publish',
			'query_builder' => 'Query Builder',
			'question_answer' => 'Question Answer',
			'queue' => 'Queue',
			'queue_music' => 'Queue Music',
			'radio' => 'Radio',
			'radio_button_checked' => 'Radio Button Checked',
			'radio_button_unchecked' => 'Radio Button Unchecked',
			'rate_review' => 'Rate Review',
			'receipt' => 'Receipt',
			'recent_actors' => 'Recent Actors',
			'redeem' => 'Redeem',
			'redo' => 'Redo',
			'refresh' => 'Refresh',
			'remove' => 'Remove',
			'remove_circle' => 'Remove Circle',
			'remove_circle_outline' => 'Remove Circle Outline',
			'remove_red_eye' => 'Remove Red Eye',
			'reorder' => 'Reorder',
			'repeat' => 'Repeat',
			'repeat_one' => 'Repeat One',
			'replay' => 'Replay',
			'replay_10' => 'Replay 10',
			'replay_30' => 'Replay 30',
			'replay_5' => 'Replay 5',
			'reply' => 'Reply',
			'reply_all' => 'Reply All',
			'report' => 'Report',
			'report_problem' => 'Report Problem',
			'restaurant_menu' => 'Restaurant Menu',
			'restore' => 'Restore',
			'ring_volume' => 'Ring Volume',
			'room' => 'Room',
			'rotate_90_degrees_ccw' => 'Rotate 90 Degrees Ccw',
			'rotate_left' => 'Rotate Left',
			'rotate_right' => 'Rotate Right',
			'router' => 'Router',
			'satellite' => 'Satellite',
			'save' => 'Save',
			'scanner' => 'Scanner',
			'schedule' => 'Schedule',
			'school' => 'School',
			'screen_lock_landscape' => 'Screen Lock Landscape',
			'screen_lock_portrait' => 'Screen Lock Portrait',
			'screen_lock_rotation' => 'Screen Lock Rotation',
			'screen_rotation' => 'Screen Rotation',
			'sd_card' => 'Sd Card',
			'sd_storage' => 'Sd Storage',
			'search' => 'Search',
			'security' => 'Security',
			'select_all' => 'Select All',
			'send' => 'Send',
			'settings' => 'Settings',
			'settings_applications' => 'Settings Applications',
			'settings_backup_restore' => 'Settings Backup Restore',
			'settings_bluetooth' => 'Settings Bluetooth',
			'settings_brightness' => 'Settings Brightness',
			'settings_cell' => 'Settings Cell',
			'settings_ethernet' => 'Settings Ethernet',
			'settings_input_antenna' => 'Settings Input Antenna',
			'settings_input_component' => 'Settings Input Component',
			'settings_input_composite' => 'Settings Input Composite',
			'settings_input_hdmi' => 'Settings Input Hdmi',
			'settings_input_svideo' => 'Settings Input Svideo',
			'settings_overscan' => 'Settings Overscan',
			'settings_phone' => 'Settings Phone',
			'settings_power' => 'Settings Power',
			'settings_remote' => 'Settings Remote',
			'settings_system_daydream' => 'Settings System Daydream',
			'settings_voice' => 'Settings Voice',
			'share' => 'Share',
			'shop' => 'Shop',
			'shop_two' => 'Shop Two',
			'shopping_basket' => 'Shopping Basket',
			'shopping_cart' => 'Shopping Cart',
			'shuffle' => 'Shuffle',
			'signal_cellular_4_bar' => 'Signal Cellular 4 Bar',
			'signal_cellular_connected_no_internet_4_bar' => 'Signal Cellular Connected No Internet 4 Bar',
			'signal_cellular_no_sim' => 'Signal Cellular No Sim',
			'signal_cellular_null' => 'Signal Cellular Null',
			'signal_cellular_off' => 'Signal Cellular Off',
			'signal_wifi_4_bar' => 'Signal Wifi 4 Bar',
			'signal_wifi_4_bar_lock' => 'Signal Wifi 4 Bar Lock',
			'signal_wifi_off' => 'Signal Wifi Off',
			'sim_card' => 'Sim Card',
			'sim_card_alert' => 'Sim Card Alert',
			'skip_next' => 'Skip Next',
			'skip_previous' => 'Skip Previous',
			'slideshow' => 'Slideshow',
			'smartphone' => 'Smartphone',
			'sms' => 'Sms',
			'sms_failed' => 'Sms Failed',
			'snooze' => 'Snooze',
			'sort' => 'Sort',
			'sort_by_alpha' => 'Sort By Alpha',
			'space_bar' => 'Space Bar',
			'speaker' => 'Speaker',
			'speaker_group' => 'Speaker Group',
			'speaker_notes' => 'Speaker Notes',
			'speaker_phone' => 'Speaker Phone',
			'spellcheck' => 'Spellcheck',
			'star' => 'Star',
			'star_border' => 'Star Border',
			'star_half' => 'Star Half',
			'stars' => 'Stars',
			'stay_current_landscape' => 'Stay Current Landscape',
			'stay_current_portrait' => 'Stay Current Portrait',
			'stay_primary_landscape' => 'Stay Primary Landscape',
			'stay_primary_portrait' => 'Stay Primary Portrait',
			'stop' => 'Stop',
			'storage' => 'Storage',
			'store' => 'Store',
			'store_mall_directory' => 'Store Mall Directory',
			'straighten' => 'Straighten',
			'strikethrough_s' => 'Strikethrough S',
			'style' => 'Style',
			'subject' => 'Subject',
			'subtitles' => 'Subtitles',
			'supervisor_account' => 'Supervisor Account',
			'surround_sound' => 'Surround Sound',
			'swap_calls' => 'Swap Calls',
			'swap_horiz' => 'Swap Horiz',
			'swap_vert' => 'Swap Vert',
			'swap_vertical_circle' => 'Swap Vertical Circle',
			'switch_camera' => 'Switch Camera',
			'switch_video' => 'Switch Video',
			'sync' => 'Sync',
			'sync_disabled' => 'Sync Disabled',
			'sync_problem' => 'Sync Problem',
			'system_update' => 'System Update',
			'system_update_alt' => 'System Update Alt',
			'tab' => 'Tab',
			'tab_unselected' => 'Tab Unselected',
			'tablet' => 'Tablet',
			'tablet_android' => 'Tablet Android',
			'tablet_mac' => 'Tablet Mac',
			'tag_faces' => 'Tag Faces',
			'tap_and_play' => 'Tap And Play',
			'terrain' => 'Terrain',
			'text_format' => 'Text Format',
			'textsms' => 'Textsms',
			'texture' => 'Texture',
			'theaters' => 'Theaters',
			'thumb_down' => 'Thumb Down',
			'thumb_up' => 'Thumb Up',
			'thumbs_up_down' => 'Thumbs Up Down',
			'time_to_leave' => 'Time To Leave',
			'timelapse' => 'Timelapse',
			'timer' => 'Timer',
			'timer_10' => 'Timer 10',
			'timer_3' => 'Timer 3',
			'timer_off' => 'Timer Off',
			'toc' => 'Toc',
			'today' => 'Today',
			'toll' => 'Toll',
			'tonality' => 'Tonality',
			'toys' => 'Toys',
			'track_changes' => 'Track Changes',
			'traffic' => 'Traffic',
			'transform' => 'Transform',
			'translate' => 'Translate',
			'trending_down' => 'Trending Down',
			'trending_flat' => 'Trending Flat',
			'trending_up' => 'Trending Up',
			'tune' => 'Tune',
			'turned_in' => 'Turned In',
			'turned_in_not' => 'Turned In Not',
			'tv' => 'Tv',
			'undo' => 'Undo',
			'unfold_less' => 'Unfold Less',
			'unfold_more' => 'Unfold More',
			'usb' => 'Usb',
			'verified_user' => 'Verified User',
			'vertical_align_bottom' => 'Vertical Align Bottom',
			'vertical_align_center' => 'Vertical Align Center',
			'vertical_align_top' => 'Vertical Align Top',
			'vibration' => 'Vibration',
			'video_library' => 'Video Library',
			'videocam' => 'Videocam',
			'videocam_off' => 'Videocam Off',
			'view_agenda' => 'View Agenda',
			'view_array' => 'View Array',
			'view_carousel' => 'View Carousel',
			'view_column' => 'View Column',
			'view_comfy' => 'View Comfy',
			'view_compact' => 'View Compact',
			'view_day' => 'View Day',
			'view_headline' => 'View Headline',
			'view_list' => 'View List',
			'view_module' => 'View Module',
			'view_quilt' => 'View Quilt',
			'view_stream' => 'View Stream',
			'view_week' => 'View Week',
			'vignette' => 'Vignette',
			'visibility' => 'Visibility',
			'visibility_off' => 'Visibility Off',
			'voice_chat' => 'Voice Chat',
			'voicemail' => 'Voicemail',
			'volume_down' => 'Volume Down',
			'volume_mute' => 'Volume Mute',
			'volume_off' => 'Volume Off',
			'volume_up' => 'Volume Up',
			'vpn_key' => 'Vpn Key',
			'vpn_lock' => 'Vpn Lock',
			'wallpaper' => 'Wallpaper',
			'warning' => 'Warning',
			'watch' => 'Watch',
			'wb_auto' => 'Wb Auto',
			'wb_cloudy' => 'Wb Cloudy',
			'wb_incandescent' => 'Wb Incandescent',
			'wb_iridescent' => 'Wb Iridescent',
			'wb_sunny' => 'Wb Sunny',
			'wc' => 'Wc',
			'web' => 'Web',
			'whatshot' => 'Whatshot',
			'widgets' => 'Widgets',
			'wifi' => 'Wifi',
			'wifi_lock' => 'Wifi Lock',
			'wifi_tethering' => 'Wifi Tethering',
			'work' => 'Work',
			'wrap_text' => 'Wrap Text',
			'youtube_searched_for' => 'Youtube Searched For',
			'zoom_in' => 'Zoom In',
			'zoom_out' => 'Zoom Out',
		);
		
		if( 'true' == $prepend_empty ) {
			$allowed_options = array( '' => '' )+$allowed_options;
		}
		
		return $allowed_options;
	}
	
	
	// array of valid MDL color names
	public static function mdl_single_color_names( $build = 'all' ) {
		
		$can_accent = array(
			'yellow'		=> 'Yellow',
			'amber'			=> 'Amber',
			'orange'		=> 'Orange',
			'deep_orange'	=> 'Deep Orange',
			'red'			=> 'Red',
			'pink'			=> 'Pink',
			'purple'		=> 'Purple',
			'deep_purple'	=> 'Deep Purple',
			'indigo'		=> 'Indigo',
			'blue'			=> 'Blue',
			'light_blue'	=> 'Light Blue',
			'cyan'			=> 'Cyan',
			'teal'			=> 'Teal',
			'green'			=> 'Green',
			'light_green'	=> 'Light Green',
			'lime'			=> 'Lime',
		);
		
		$cannot_accent = array(
			'brown'			=> 'Brown',
			'blue_grey'		=> 'Blue Grey',
			'grey'			=> 'Grey',
		);
		
		
		if( 'accents' == $build ) {
			$color_names = $can_accent;
		} elseif( 'forbiddenaccents' == $build ) {
			$color_names = $cannot_accent;
		} else { // 'all' or 'primaries'
			$color_names = $can_accent + $cannot_accent;
		}
		
		return $color_names;
	}
	
	// is a valid MDL color name
	public static function mdl_is_valid_mdl_color( $string, $build = 'all' ) {
		$string = trim( strtolower( (string) $string ) );
		
		$colors = self::mdl_single_color_names( $build );
		
		if( empty( $colors ) ) {
			$colors = self::mdl_single_color_names();
		}
		
		if ( array_key_exists( $string, $colors ) ) {
			return true;
		} else {
			return false;
		}
	}
	
	
	public static function mdl_primary_color( $default = 'indigo' ) {
		$color = get_option( 'mdl_shortcodes_colors_setting' ); // array
		
		if( is_array( $color ) && array_key_exists( 'primary', $color ) ) {
			$color = $color['primary'];
		} else {
			$color = $default;
		}
		
		$color = strtolower( $color );
		
		if ( self::mdl_is_valid_mdl_color( $color ) ) {
			return $color;
		} else {
			return $default;
		}
	}
	
	public static function mdl_accent_color( $default = 'pink' ) {
		$color = get_option( 'mdl_shortcodes_colors_setting' ); // array
		
		if( is_array( $color ) && array_key_exists( 'accent', $color ) ) {
			$color = $color['accent'];
		} else {
			$color = $default;
		}
		
		$color = strtolower( $color );
		
		if ( self::mdl_is_valid_mdl_color( $color, 'accents' ) ) {
			return $color;
		} else {
			return $default;
		}
	}
	
	public static function mdl_version_number() {
		// from https://github.com/google/material-design-lite/releases or visible in stylesheet URI at http://www.getmdl.io/started/index.html#download
		$version = apply_filters( 'mdl_shortcodes_version_number_filter', '1.0.5' );
		
		return $version;
	}
	
	
	public static function mdl_google_hosted_stylesheet() {
		$hosted = apply_filters( 'mdl_shortcodes_google_hosted_stylesheet_filter', true );
		
		return $hosted;
	}
	
	public static function mdl_google_hosted_icons() {
		$hosted = apply_filters( 'mdl_shortcodes_google_hosted_icons_filter', true );
		
		return $hosted;
	}
	
	public static function mdl_google_hosted_js() {
		$hosted = apply_filters( 'mdl_shortcodes_google_hosted_js_filter', true );
		
		return $hosted;
	}
	
	public static function mdl_stylesheet_handle() {
		$handle = sprintf( 'mdl-%s', self::mdl_color_combo() );
		return $handle;
	}
	
	public static function mdl_icons_handle() {
		$handle = 'mdl-icons';
		return $handle;
	}
	
	public static function mdl_js_handle() {
		$handle = 'mdl-js';
		return $handle;
	}
	
	public static function mdl_stylesheet_src() {
		if ( self::mdl_google_hosted_stylesheet() ) {
			$src = sprintf( 'https://storage.googleapis.com/code.getmdl.io/%s/material.%s.min.css', self::mdl_version_number(), self::mdl_color_combo() );
		} else {
			$src = apply_filters( 'mdl_shortcodes_stylesheet_src_filter', '' ); //local source
		}
		
		return $src;
	}
	
	
	// 240 valid color combos as of 2015-07-28
	// to see full list, echo mdl_all_valid_stylesheet_combos_br() or view https://github.com/google/material-design-lite/issues/1206#issuecomment-125701040
	
	public static function mdl_all_valid_stylesheet_combos_array() {
		// reference: https://github.com/google/material-design-lite/blob/master/docs/_assets/customizer.js#L74-78
		
		$allowed_color_combos = array();
	    
		foreach( self::mdl_single_color_names() as $primary => $primary_item ) {
			foreach( self::mdl_single_color_names( 'accents' ) as $accent => $accent_item ) {
				if( $primary != $accent ) {
					$allowed_color_combos[$primary . '-' . $accent] = sprintf( '%s - %s', $primary_item, $accent_item );
				}
			}
		}
		
	    $allowed_color_combos = array_unique( $allowed_color_combos ); // shouldn't be needed, but just in case
	    
		return $allowed_color_combos;
	}
	
	
	public static function mdl_color_combo( $default = 'indigo-pink' ) {
		$combo = self::mdl_primary_color() . '-' . self::mdl_accent_color();
		
		if ( ! array_key_exists( $combo, self::mdl_all_valid_stylesheet_combos_array() ) ) {
			$combo = $default;
		}
		
		return $combo;	
	}
	
	public static function mdl_icons_src() {
		if ( self::mdl_google_hosted_icons() ) {
			$src = 'https://fonts.googleapis.com/icon?family=Material+Icons';
		} else {
			$src = apply_filters( 'mdl_shortcodes_icons_src_filter', '' ); //local source
		}
		
		return $src;
	}
	
	public static function mdl_js_src() {
		if ( self::mdl_google_hosted_js() ) {
			$src = sprintf( 'https://storage.googleapis.com/code.getmdl.io/%s/material.min.js', self::mdl_version_number() );
		} else {
			$src = apply_filters( 'mdl_shortcodes_js_src_filter', '' ); //local source
		}
		
		return $src;
	}
	
	// If Google-hosted, version should be NULL if we want Google's edge cached benefits -- but should keep up with https://github.com/google/material-design-icons/blob/master/package.json if used at all
	
	public static function mdl_register_stylesheet() {
		wp_register_style( self::mdl_stylesheet_handle(), self::mdl_stylesheet_src(), array(), null );
	}
	
	public static function mdl_enqueue_stylesheet() {
		wp_enqueue_style( self::mdl_stylesheet_handle(), self::mdl_stylesheet_src(), array(), null );
	}
	
	public static function mdl_register_icons() {
		wp_register_style( self::mdl_icons_handle(), self::mdl_icons_src(), array(), null );
	}
	
	public static function mdl_enqueue_icons() {
		wp_enqueue_style( self::mdl_icons_handle(), self::mdl_icons_src(), array(), null );
	}
	
	public static function mdl_register_js() {
		wp_register_script( self::mdl_js_handle(), self::mdl_js_src(), array(), null, true );
	}
	
	public static function mdl_enqueue_js() {
		wp_enqueue_script( self::mdl_js_handle(), self::mdl_js_src(), array(), null, true );
	}
	
	// first checking for class_exists( 'Shortcode_UI' ) does not work so just add styles/scripts to TinyMCE whether or not Shortcake plugin is active
	// but alternatively we could leverage get_shortcake_admin_dependencies() in inc/class-mdl-shortcodes.php -- may or may not check for is_admin()
	// add_filter( 'mce_css', 'mdl_tinymce_stylesheet_icons_func' );
	//add_filter( 'mce_css', array( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_tinymce_stylesheet_icons_func' ) );
	public static function mdl_tinymce_stylesheet_icons_func( $mce_css ) {
		// if ( ! empty( self::mdl_stylesheet_src() ) ) { // WP SVN thought there was a PHP fatal error "can't use function return value in write context in - on line 2546" -- not a crucial check anyway as it should not be empty()
			if ( ! empty( $mce_css ) ) {
				$mce_css .= ',';
			}
			$mce_css .= self::mdl_stylesheet_src();
		// }
		
		// if ( ! empty( self::mdl_icons_src() ) ) { // same WP SVN error as above
			if ( ! empty( $mce_css ) ) {
				$mce_css .= ',';
			}
			$mce_css .= self::mdl_icons_src();
		// }	
		
		return $mce_css;
	}
	
	// just load scripts when TinyMCE is present (which, unfortunately, does include Dashboard (Quick Draft / Quick Edit), Post Editing, Comments and possibly other wp-admin screens)
	public static function mdl_tinymce_scripts_func() {
		printf( '<link rel="stylesheet" id="%s-css" href="%s" type="text/css" media="all">', self::mdl_icons_handle(), self::mdl_icons_src() );
		printf( '<link rel="stylesheet" id="%s-css" href="%s" type="text/css" media="all">', self::mdl_stylesheet_handle(), self::mdl_stylesheet_src() );
		printf( '<script type="text/javascript" src="%s"></script>', self::mdl_js_src() );
	}
	
	
	//add_filter( 'mce_css', array( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_tinymce_css_php_func' ) );
	public static function mdl_tinymce_css_php_func( $mce_css ) {
		if ( defined( 'MDL_SHORTCODES_URL_ROOT' ) ) {
			if ( ! empty( $mce_css ) ) {
				$mce_css .= ',';
			}
			$mce_css .= MDL_SHORTCODES_URL_ROOT . 'assets/css/mdl-mce-styles.php';
		}
			
		return $mce_css;
	}
	
	
	
	

} // CLOSING abstract class Shortcode