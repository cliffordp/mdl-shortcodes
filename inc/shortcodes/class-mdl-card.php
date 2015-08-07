<?php

namespace MDL_Shortcodes\Shortcodes;

class MDL_Card extends Shortcode {

// TODO:
// consider adding min-height to Title Area (e.g. 96px (double h2 height) -- but mdl-card already has overall min-height: 200px
//
// http://www.getmdl.io/components/index.html#cards-section
// https://www.google.com/design/spec/components/cards.html
//
// Post IDs Examples:
// [mdl-card postid=480]
// [mdl-card postid=480 title="Override this post title with this text. Ha Ha!"]
//
// Fully Manual (no Post IDs) Examples:
// [mdl-card title="Title Here, Sir!" menu="add_alert" menulink="http://google.com/" menutarget="_blank" mediasize="0" supporting="Support your Title by displaying this text!" actions="Clickeroo" actionslink="http://apple.com/" actionstarget="_blank" actionsicon="face" shadow="2"]
//
// Grid Examples:
/*
	<div class="mdl-grid">
		[mdl-card postid=480,436,418,241 shadow=2 class="mdl-cell mdl-cell--3-col"]
	</div>
	
	OR
	use shortcode grid with no spacing between cells
	
	[mdl-grid spacing=false]
		[mdl-card postid=480,436,418,241 shadow=2 class="mdl-cell mdl-cell--3-col"]
	[/mdl-grid]
	
	
	NOTE: must add custom classes to Card if entering multiple Post IDs because that would just result in 4 cards within a single 3-wide cell (i.e. missing the other 9-wide) -- but you could choose to break it out into 4 different card shortcodes, like this:
	[mdl-grid]
		[mdl-cell size=3][mdl-card postid=480 shadow=2][/mdl-cell]
		[mdl-cell size=3][mdl-card postid=436 shadow=2][/mdl-cell]
		[mdl-cell size=3][mdl-card postid=418 shadow=2][/mdl-cell]
		[mdl-cell size=3][mdl-card postid=241 shadow=2][/mdl-cell]
	[/mdl-grid]
	
*/

	public static function get_shortcode_ui_args() {
		
		// build WP_Query args for 'attr' => 'postid'
		$postid_query_args = array(
			'post_status' => array( 'publish' ),
			'post_type' => 'any', // default is 'post'
			//'post__in' => parent::mdl_get_posts_w_fimage_set(), // post IDs of all posts with featured image set
		);
		
		return array(
			'label'          => esc_html__( 'MDL Card', 'mdl-shortcodes' ),
			'listItemImage'  => 'dashicons-format-aside',
			'add_button'	 => 'icon_only',
			'attrs'          => array(
				array(
					'label'  => esc_html__( 'Post(s) to display', 'mdl-shortcodes' ),
					'attr'   => 'postid',
					//'query'  => array( 'post_type' => parent::mdl_get_post_types_support_fimages() ),
					'query'  => $postid_query_args,
					'type'   => 'post_select',
					'multiple' => true, // allow user to select more than 1 post
					'description'  => esc_html__( 'Drop down list is all Published posts of all post types. This shortcode argument can accept one or more comma-separated post IDs.', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Title Text', 'mdl-shortcodes' ),
					'attr'   => 'title',
					'type'   => 'text',
					'description'  => esc_html__( 'Required if postid argument is not set, otherwise is an optional override of Post Title.', 'mdl-shortcodes' ),
					'meta' => array(
						'placeholder' => esc_html__( 'Example: An Excellent Decision', 'mdl-shortcodes' ),
					),
				),
				array(
					'label'  => esc_html__( 'Menu Button Icon', 'mdl-shortcodes' ),
					'attr'   => 'menu',
					'type'   => 'select',
					'options' => parent::mdl_icons_selection_array(),
					'description'  => parent::mdl_icon_description_text( 'Icon in upper-right corner of Card. ' ),
				),
				array(
					'label'  => esc_html__( 'Menu Button Link', 'mdl-shortcodes' ),
					'attr'   => 'menulink',
					'type'   => 'url',
					'description'  => esc_html__( 'If not set, Menu Icon will not be displayed.', 'mdl-shortcodes' ),
					'meta' => array(
						'placeholder' => __( 'http://' ),
					),
				),
				array(
					'label'  => esc_html__( 'Menu Button Link Target', 'mdl-shortcodes' ),
					'attr'   => 'menutarget',
					'type'   => 'select',
					'options' => parent::mdl_targets_selection_array(),
					'description'  => esc_html__( 'Ignored if no Menu Button Link', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Title Heading Tag', 'mdl-shortcodes' ),
					'attr'   => 'htag',
					'type'   => 'select',
					'options' => parent::mdl_allowed_htags_array( 'select' ),
					'description'  => esc_html__( 'Default is h2. Allows h1-h6.', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Title Color', 'mdl-shortcodes' ),
					'attr'   => 'titlecolor',
					'type'   => 'select',
					'options' => parent::mdl_color_palette_classes_selection_array( 'true', 'text' ),
					'description'  => parent::mdl_color_description_text( 'Title and Menu Button color. Default: White. ', '' ),
				),
				array(
					'label'  => esc_html__( 'Title Area Background Color', 'mdl-shortcodes' ),
					'attr'   => 'titlebgcolor',
					'type'   => 'select',
					'options' => parent::mdl_color_palette_classes_selection_array( 'true', 'background' ),
					'description'  => parent::mdl_color_description_text( 'Default: Accent. ', ' (Disallowed from choosing same color as Title Color.)' ),
				),
/*
				array(
					'label'  => esc_html__( 'Title Border', 'mdl-shortcodes' ),
					'attr'   => 'titleborder',
					'type'   => 'select',
					'options' => parent::mdl_true_false_selection_array( 'true', 'false', 'true' ),
					'description'  => esc_html__( 'Display Border around the Title Area. Default: True.', 'mdl-shortcodes' ),
				),
*/
/*
				array(
					'label'  => esc_html__( 'Subtitle', 'mdl-shortcodes' ),
					'attr'   => 'subtitle',
					'type'   => 'text',
					'description'  => esc_html__( 'NOT RECOMMENDED TO USE because it does not look good, which is probably why there are no demos for it.', 'mdl-shortcodes' ),
					'meta' => array(
						//'placeholder' => esc_html__( 'Example: An Excellent Choice', 'mdl-shortcodes' ),
					),
				),
*/
				array(
					'label'  => esc_html__( 'Media Image', 'mdl-shortcodes' ),
					'attr'   => 'mediaid',
					'type'   => 'attachment',
					'libraryType'	=> array( 'image' ),
					'addButton'		=> esc_html__( 'Select Image' ),
					'frameTitle'	=> esc_html__( 'Select Image' ),
					'description'  => esc_html__( 'The Item ID / Post ID of the Media Attachment (image) you want to use as the Card Media area. Will override Title Area Background Color. Will override Post Featured Image if postid argument is set.', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Media Image Size', 'mdl-shortcodes' ),
					'attr'   => 'mediasize',
					'type'   => 'select',
					'options' => parent::mdl_wp_image_sizes_selection_array(),
					'description'  => esc_html__( 'Image size to use. Ignored unless Media Image is selected. Default: thumbnail' ),
				),
				array(
					'label'  => esc_html__( 'Media Image Placement', 'mdl-shortcodes' ),
					'attr'   => 'mediaplacement',
					'type'   => 'select',
					'options' => array(
						''			=> esc_html__( 'Title Area Background', 'mdl-shortcodes' ),
						'card'		=> esc_html__( 'Entire Card Background', 'mdl-shortcodes' ),
						'mediaarea'	=> esc_html__( 'Separate Media Area (below Title Area)', 'mdl-shortcodes' ),
					),
					'description'  => esc_html__( 'If Media Image is set, choose to display it as the Title Area background (Default) or in a separate Media Area below the Title Area.', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Media Area Image Padding', 'mdl-shortcodes' ),
					'attr'   => 'mediapadding',
					'type'   => 'text',
					'description'  => esc_html__( 'Padding around Media Image in separate Media Area. Ignored unless mediaplacement argument is set to display separate Media Area. Default is 10px.', 'mdl-shortcodes' ),
					'meta' => array(
						'placeholder' => esc_html__( 'Example: 5%', 'mdl-shortcodes' ),
					),
				),
				array(
					'label'  => esc_html__( 'Supporting Text', 'mdl-shortcodes' ),
					'attr'   => 'supporting',
					'type'   => 'textarea',
					'description'  => esc_html__( 'If postid argument is set, this is an optional override of Post Excerpt. If this is not set, Supporting Text area will not be displayed.', 'mdl-shortcodes' ),
					'meta' => array(
						'placeholder' => esc_html__( 'Excerpt text here...', 'mdl-shortcodes' ),
					),
				),
				array(
					'label'  => esc_html__( 'Actions Text', 'mdl-shortcodes' ),
					'attr'   => 'actions',
					'type'   => 'text',
					'description'  => __( 'Defaults to "Read More" Button Text if postid argument is set or actionslink argument is set. If neither and this argument is set, it will display as regular text.', 'mdl-shortcodes' ),
					'meta' => array(
						'placeholder' => esc_html__( 'Example: Click Here', 'mdl-shortcodes' ),
					),
				),
				array(
					'label'  => esc_html__( 'Actions Button Link', 'mdl-shortcodes' ),
					'attr'   => 'actionslink',
					'type'   => 'url',
					'description'  => esc_html__( 'If postid argument is set, this is an optional override of Post Permalink. If this is not set, Actions Text will not become a Button.', 'mdl-shortcodes' ),
					'meta' => array(
						'placeholder' => __( 'http://' ),
					),
				),
				array(
					'label'  => esc_html__( 'Actions Button Link Target', 'mdl-shortcodes' ),
					'attr'   => 'actionstarget',
					'type'   => 'select',
					'options' => parent::mdl_targets_selection_array(),
					'description'  => esc_html__( 'Ignored if no Link URL', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Actions Area Right-Side Icon', 'mdl-shortcodes' ),
					'attr'   => 'actionsicon',
					'type'   => 'select',
					'options' => parent::mdl_icons_selection_array(),
					'description'  => parent::mdl_icon_description_text( 'Icon on right side of Actions Area.' ),
				),
				array(
					'label'  => esc_html__( 'Actions Border', 'mdl-shortcodes' ),
					'attr'   => 'actionsborder',
					'type'   => 'select',
					'options' => parent::mdl_true_false_selection_array( 'true', 'false', 'true' ),
					'description'  => esc_html__( 'Display Border around the Actions Area. Default: True.', 'mdl-shortcodes' ),
				),
				array(
					'label'  => esc_html__( 'Card Shadow Size', 'mdl-shortcodes' ),
					'attr'   => 'shadow',
					'type'   => 'select',
					'options' => array(
						''		=> esc_html__( 'None / Zero', 'mdl-shortcodes' ),
						'2'		=> esc_html__( '2', 'mdl-shortcodes' ),
						'3'		=> esc_html__( '3', 'mdl-shortcodes' ),
						'4'		=> esc_html__( '4', 'mdl-shortcodes' ),
						'6'		=> esc_html__( '6', 'mdl-shortcodes' ),
						'8'		=> esc_html__( '8', 'mdl-shortcodes' ),
						'16'	=> esc_html__( '16', 'mdl-shortcodes' ),
					),
					'description'  => esc_html__( 'Size of shadow in Density-Independent Pixels (dp). Default: None.', 'mdl-shortcodes' ),
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
			'postid'		=> '',
			'title'			=> '',
			'menu'			=> '',
			'menulink'		=> '',
			'menutarget'	=> '',
			'htag'			=> '', // h1-h6
			'titlecolor'	=> '',
			'titlebgcolor'	=> '',
			//'titleborder'	=> '', // border before the supporting area
			'subtitle'		=> '',
			'mediaid'		=> '',
			'mediasize'		=> '',
			'mediaplacement'=> '',
			'mediapadding'	=> '', // valid CSS value (25px, 5%, 0, auto, -2em, ...)
			'supporting'	=> '',
			'actions'		=> '',
			'actionslink'	=> '',
			'actionstarget'	=> '',
			'actionsicon'	=> '',
			'actionsborder'	=> '', // border after the supporting area
			'shadow'		=> '',
			'class'			=> '', // e.g. 'mdl-cell mdl-cell--4-col' (then manually wrap this shortcode's output in .mdl-grid ) --> will result in 3 columns of cards
		);
		
		$atts = shortcode_atts( $defaults, $atts, self::get_shortcode_tag() );
		
		// No [mdl-button] so no Menu Button and no Actions Button so really no point in creating a Card
		if( ! shortcode_exists( 'mdl-button' ) ) {
			return '';
		}
		
		$using_post_ids = 'false';
		
		$post_ids = $atts['postid'];
		$post_ids = parent::mdl_only_integers_commas( $post_ids );
		
		if( ! empty( $post_ids ) ) {
			
			$post_ids = array_map( 'intval', array_filter( explode( ',' , $post_ids ), 'is_numeric' ) ); //array of post IDs stored as integers
			
			// No Post ID Array
			if( ! is_array( $post_ids ) ) {
				return '';
			}
			
			$post_ids = array_filter( $post_ids ); //remove empties from array so get valid COUNT
			
			$post_ids = array_unique( $post_ids, SORT_NUMERIC ); // compares items numerically, does not actually SORT or ORDER
			
			// No Valid Post IDs
			if( empty( $post_ids ) || 1 > count( $post_ids ) ) {
				return '';
			}
			
			$using_post_ids = 'true'; // $post_ids is good so let's continue...
		}
		
		
		$title			=	$atts['title'];
		$menu			=	strtolower( $atts['menu'] );
		$menulink		=	esc_url( $atts['menulink'] );
		$menutarget		=	parent::mdl_url_target( $atts['menutarget'] );
		$htag			=	parent::mdl_htag( $atts['htag'], 'h2' );
		$title_color	=	strtolower( $atts['titlecolor'] );
		$title_bgcolor	=	strtolower( $atts['titlebgcolor'] );
		//$title_border	=	strtolower( $atts['titleborder'] );
		$subtitle		=	$atts['subtitle'];
		$media_id		=	intval( $atts['mediaid'] );
		$media_size		=	strtolower( $atts['mediasize'] );
		$media_placement=	strtolower( $atts['mediaplacement'] );
		$media_padding	=	parent::mdl_restrict_css_unit_value( $atts['mediapadding'], '10px' );
		$supporting		=	$atts['supporting'];
		$actions		=	$atts['actions'];
		$actionslink	=	esc_url( $atts['actionslink'] );
		$actionstarget	=	parent::mdl_url_target( $atts['actionstarget'] );
		$actions_icon	=	strtolower( $atts['actionsicon'] );
		$actions_border	=	strtolower( $atts['actionsborder'] );
		$shadow			=	$atts['shadow'];
		if( method_exists( 'MDL_Shortcodes\Shortcodes\Shortcode', 'mdl_sanitize_html_classes' ) ) {
			$class = parent::mdl_sanitize_html_classes( $atts['class'] );
		} else {
			$class = sanitize_html_class( $atts['class'] );
		}
		
		// WARNING: we are NOT sanitizing $title, $subtitle, $supporting, or $actions to allow for HTML.
		
		
		// Invalid Menu Icon Name
		if ( ! array_key_exists( $menu, parent::mdl_icons_selection_array( 'false' ) ) ) {
			$menu = '';
		}
		
		// No Menu Link so no Menu Icon
		if ( empty( $menulink ) ) {
			$menu = '';
		}
		
		if ( ! empty( $menu ) ) {
			$menu = sprintf( '[mdl-button icon="%s" url="%s"', $menu, $menulink );
			if( $menutarget ) {
				$menu .= sprintf( ' target="%s"', $menutarget );
			}
			$menu .= ']';
		}
		
		$title_color = sanitize_html_class( strtolower( $atts['titlecolor'] ) );
		if ( ! array_key_exists( $title_color, parent::mdl_color_palette_classes_selection_array( 'false', 'text' ) ) ) {
			$title_color = '';
		}
		
		$title_bgcolor = sanitize_html_class( strtolower( $atts['titlebgcolor'] ) ); // not mdl_sanitize_html_classes() because only allowing one class
		if ( ! array_key_exists( $title_bgcolor, parent::mdl_color_palette_classes_selection_array( 'false', 'background' ) ) ) {
			$title_bgcolor = 'mdl-color--accent';
		}
		
		// disallow Title Area Background color being same as Title Area Text color
		// if are the same, set to default
		if ( 'true' == parent::mdl_text_background_colors_same( $title_color, $title_bgcolor ) ) {
			$title_color = '';
			//$title_bgcolor = '';
		}

		//$title_border = parent::mdl_truefalse( $title_border, 'true' );
		
		
		$allowed_media_placement = array( 'titlearea', 'card', 'mediaarea' );
		
		if( empty( $media_id ) || 1 > $media_id ) { // intval() on free form text would set $media to 0, and disallow 0 and negatives
			$media_id = '';
		}
		
		if( ! in_array( $media_size, parent::mdl_wp_image_sizes_selection_array( 'false' ) ) ) {
			$media_size = 'thumbnail'; // get_the_post_thumbnail() default is 'post-thumbnail' which is large, unlike 'thumbnail'
		}
		
		if( ! in_array( $media_placement, $allowed_media_placement ) ) {
			$media_placement = 'titlearea';
		}
		
		if( 'mediaarea' !== $media_placement ) {
			$media_padding = '';
		}
		
				
		// Invalid Actions Icon Name
		if ( ! array_key_exists( $actions_icon, parent::mdl_icons_selection_array( 'false' ) ) ) {
			$actions_icon = '';
		}
		
		$allowed_shadows = array( '2', '3', '4', '6', '8', '16' );
		if( ! in_array( $shadow, $allowed_shadows ) ) {
			$shadow = '';
		}
		
		$actions_border = parent::mdl_truefalse( $actions_border, 'true' );
		
		$cards = array();
		
		$output = '';
		
		if( 'true' == $using_post_ids ) {
			// https://developer.wordpress.org/reference/functions/get_post/
			// https://codex.wordpress.org/Class_Reference/WP_Post
			// https://codex.wordpress.org/Function_Reference/get_post_status
			// https://codex.wordpress.org/Function_Reference/get_post_thumbnail_id
			
			$allowed_post_statuses = array( 'publish' ); // maybe this ---- array( 'publish', 'future', 'private' );
			
			foreach ( $post_ids as $post_id ) {
				
				// Reset
				$post_data = $post_status = $post_title = $post_media_id = $post_supporting = $post_actions = $post_actionslink = $post_class = '';
				
				
				$post_data = get_post( $post_id );
				
				$post_status = get_post_status( $post_id );
				
				// sprintf( '<!-- Post ID %s is in a disallowed status -->', $post_id )
				if( ! in_array( $post_status, $allowed_post_statuses ) ) {
					break;
				}
				
				$post_title = $title ? $title : $post_data->post_title;
				
				$post_media_id = $media_id ? $media_id : '';
				if( empty( $post_media_id ) ) {
					$post_media_id = get_post_thumbnail_id( $post_id );
					if( empty( $post_media_id ) ) { // empty string or FALSE from get_post_thumbnail_id()
						$post_media_id = '';
					}
				}
				
				$post_supporting = $supporting ? $supporting : $post_data->post_excerpt;
				
				if( empty( $actionslink ) ) {
					$post_actionslink = esc_url( get_permalink( $post_id ) );
				}
				
				$post_actions = $actions;
				
				if( $post_actionslink && empty( $post_actions ) ) {
					$post_actions = 'Read More';
				}
				
				$post_class = sprintf('mdl-card-post-id-%d %s', $post_id, $class );
							
				$cards[] = array(
					'title'				=> $post_title, // different
					'menu'				=> $menu,
					//'menulink'		=> $menulink,
					//'menutarget'		=> $menutarget,
					'htag'				=> $htag,
					'titlecolor'		=> $title_color,
					'titlebgcolor'		=> $title_bgcolor,
					//'titleborder'		=> $title_border,
					'subtitle'			=> $subtitle,
					'mediaid'			=> $post_media_id, // different
					'mediasize'			=> $media_size,
					'mediaplacement'	=> $media_placement,
					'mediapadding'		=> $media_padding,
					'supporting'		=> $post_supporting, // different
					'actions'			=> $actions,
					'actionslink'		=> $post_actionslink, // different
					'actionstarget'		=> $actionstarget,
					'actionsicon'		=> $actions_icon,
					'actionsborder'		=> $actions_border,
					'shadow'			=> $shadow,
					'class'				=> $post_class, // different
				);
			} // end foreach
		} else {
			if( ! empty( $actionslink ) && empty( $actions ) ) {
				$actions = 'Read More';
			}
			
			$cards[] = array(
				'title'				=> $title,
				'menu'				=> $menu,
				//'menulink'		=> $menulink,
				//'menutarget'		=> $menutarget,
				'htag'				=> $htag,
				'titlecolor'		=> $title_color,
				'titlebgcolor'		=> $title_bgcolor,
				//'titleborder'		=> $title_border,
				'subtitle'			=> $subtitle,
				'mediaid'			=> $media_id,
				'mediasize'			=> $media_size,
				'mediaplacement'	=> $media_placement,
				'mediapadding'		=> $media_padding,
				'supporting'		=> $supporting,
				'actions'			=> $actions,
				'actionslink'		=> $actionslink,
				'actionstarget'		=> $actionstarget,
				'actionsicon'		=> $actions_icon,
				'actionsborder'		=> $actions_border,
				'shadow'			=> $shadow,
				'class'				=> $class,
			);
		}
		
		foreach( $cards as $card ) {
/*
			// Requires Title Text or Actions Text
			if( empty( $card['title'] ) && empty( $card['actions'] ) ) {
				return '';
			}
*/
			
			$img = wp_get_attachment_image_src( $card['mediaid'], $card['mediasize'] );
			$img_src = '';
			$img_bg_styling = '';
			$img_html = '';
			if( ! empty( $img ) ) {
				$img_src = $img[0]; // https://codex.wordpress.org/Function_Reference/wp_get_attachment_image_src
				$img_src = str_replace( 'http://', '//', $img_src ); // make protocol-relative
				
				$img_bg_styling = sprintf( ' style="background: url(\'%s\') center / cover;"', $img_src );
				
				$img_html = wp_get_attachment_image( $card['mediaid'], $card['mediasize'] );
			}
			
			
			$output .= '<!-- MDL Card -->';
			
			$card_class = $card['class'];
			
			if( $card['shadow'] ) {
				$card_class = sprintf( 'mdl-shadow--%sdp %s', $shadow, $card_class );
			}
			
			$card_class = 'mdl-card ' . $card_class;
			
			// Card outer div
			$output .= sprintf( '<div class="%s"', $card_class );
			if( $img_bg_styling && 'card' == $card['mediaplacement'] ) {
				$output .= $img_bg_styling;
			}
			$output .= '>';
			
			// Title Area
			$title_class = sprintf( 'mdl-card__title %s %s', $card['titlecolor'], $card['titlebgcolor'] );
			
			$output .= sprintf( '<div class="%s"', $title_class );
			if( $img_bg_styling && 'titlearea' == $card['mediaplacement'] ) {
				$output .= sprintf( '%s', $img_bg_styling );
			}
			$output .= sprintf( '><%1$s>%2$s</%1$s>', $card['htag'], $card['title'] );
			
			if( $card['subtitle'] ) {
				$output .= sprintf( '<div class="mdl-card__subtitle-text">%s</div>', $card['subtitle'] );
			}
			
			$output .= '</div>';
			
			// Media Area
			$media_area_class = 'mdl-card__media mdl-typography--text-center'; // added mdl-typography--text-center to text-align:center the img
			
			if( $img_html && 'mediaarea' == $card['mediaplacement'] ) {
				$output .= sprintf('<div class="%s">%s</div>', $media_area_class, $img_html );
			}
			
			// Supporting Text Area
			if( $card['supporting'] ) {
				$output .= sprintf('<div class="mdl-card__supporting-text">%s</div>', $card['supporting'] );
			}
			
			// Actions Area
			if( $card['actions'] ) {
				if ( ! empty( $card['actionslink'] ) ) { // clickable button
					$actions_content = sprintf( '[mdl-button text="%s" url="%s"', $card['actions'], $card['actionslink'] );
					if( $card['actionstarget'] ) {
						$actions_content .= sprintf( ' target="%s"', $card['actionstarget'] );
					}
					$actions_content .= ']';
				} else {
					$actions_content = $card['actions']; // just regular text (non-button)
				}
				
				$actions_class = 'mdl-card__actions';
				$actions_styling = '';
				if( 'true' == $card['actionsborder'] ) {
					$actions_class .= ' mdl-card--border';
				}
				
				if( ! empty( $card['actionsicon'] ) ) {
					$actions_styling .= ' style="display: flex; align-items: center;"';
					$actions_content .= sprintf( '<div class="mdl-layout-spacer"></div><i class="material-icons">%s</i>', $card['actionsicon'] ); // display a right-aligned icon
				}
				
				$output .= sprintf( '<div class="%s"%s>%s</div>', $actions_class, $actions_styling, $actions_content );
			}
			
			// Menu Button created above
			if( $card['menu'] ) {				
				$output .= sprintf( '<div class="mdl-card__menu">%s</div>', $card['menu'] );
			}
						
			// close Card outer div
			$output .= '</div>';
		}
		
		return do_shortcode( $output );
	}
	
}
