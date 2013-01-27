<?php
/**
 * jrConway Responsive Blog 1.0 functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, jrconwayresponsiveblog_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * @package Wordpress
 * @subpackage jrConway.Blog
 * @since jrConway Responsive Blog 1.0
 */

/**
 * Sets up the content width value based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 625;

/**
 * Sets up theme defaults and registers the various WordPress features that
 * jrConway Responsive Blog supports.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To add a Visual Editor stylesheet.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links,
 * 	custom background, and post formats.
 * @uses register_nav_menu() To add support for navigation menus.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since jrConway Responsive Blog 1.0
 */
function jrconwayblog_setup() {
	/*
	 * Makes Template available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 */
	load_theme_textdomain( 'jrconwayblog', get_template_directory() . '/languages' );

	/*
	 * Import WP Less
	 *
	 * This will be used for all available stylesheets.
	 */
	require_once( 'wp-less/wp-less.php' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// This theme supports a variety of post formats.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote', 'status' ) );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Primary Menu', 'jrconwayblog' ) );

	/*
	 * This theme supports custom background color and image, and here
	 * we also set up the default background color.
	 */
	add_theme_support( 'custom-background', array(
		'default-color' => 'e6e6e6',
	) );

	// This theme uses a custom image size for featured images, displayed on "standard" posts.
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 624, 9999 ); // Unlimited height, soft crop
}
add_action( 'after_setup_theme', 'jrconwayblog_setup' );

/**
 * Adds support for a custom header image.
 */
require( get_template_directory() . '/inc/custom-header.php' );

/**
 * Enqueues scripts and styles for front-end.
 *
 * @since jrConway Responsive Blog 1.0
 */
function jrconwayblog_scripts_styles() {
	global $wp_styles;

	/*
	 * Adds JavaScript to pages with the comment form to support
	 * sites with threaded comments (when in use).
	 */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/*
	 * Adds JavaScript for handling the navigation menu hide-and-show behavior.
	 */
	wp_enqueue_script( 'jrconwayblog-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0', true );

	/*
	 * Loads our special font CSS file.
	 *
	 * The use of Open Sans by default is localized. For languages that use
	 * characters not supported by the font, the font can be disabled.
	 *
	 * To disable in a child theme, use wp_dequeue_style()
	 * function mytheme_dequeue_fonts() {
	 *     wp_dequeue_style( 'jrconwayblog-fonts' );
	 * }
	 * add_action( 'wp_enqueue_scripts', 'mytheme_dequeue_fonts', 11 );
	 */

	/* translators: If there are characters in your language that are not supported
	   by Open Sans, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Open Sans font: on or off', 'jrconwayblog' ) ) {
		$subsets = 'latin,latin-ext';

		/* translators: To add an additional Open Sans character subset specific to your language, translate
		   this to 'greek', 'cyrillic' or 'vietnamese'. Do not translate into your own language. */
		$subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)', 'jrconwayblog' );

		if ( 'cyrillic' == $subset )
			$subsets .= ',cyrillic,cyrillic-ext';
		elseif ( 'greek' == $subset )
			$subsets .= ',greek,greek-ext';
		elseif ( 'vietnamese' == $subset )
			$subsets .= ',vietnamese';

		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => 'Open+Sans:400italic,700italic,400,700',
			'subset' => $subsets,
		);
		wp_enqueue_style( 'jrconwayblog-fonts', add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ), array(), null );
	}

	/*
	 * Loads our main stylesheet.
	 */
	wp_enqueue_style( 'jrconwayblog-style', get_stylesheet_uri() );
	wp_enqueue_style( 'jrc-styles', get_template_directory_uri() . '/css/style.less');

	/*
	 * Loads the Internet Explorer specific stylesheet.
	 */
	wp_enqueue_style( 'jrconwayblog-ie', get_template_directory_uri() . '/css/ie.css', array( 'jrconwayblog-style' ), '20121010' );
	$wp_styles->add_data( 'jrconwayblog-ie', 'conditional', 'lt IE 9' );

	/*
	 * Loads all required Javascript files.
	 */
	wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/js/libs/modernizr-2.0.6.min.js', array( 'jquery' ), '2.0.6' );
	wp_enqueue_script( 'gumby', get_template_directory_uri() . '/js/libs/gumby.min.js', array( 'jquery' ), '1.1' );
	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array( 'jquery' ), '2.2.1' );
}
add_action( 'wp_enqueue_scripts', 'jrconwayblog_scripts_styles' );

/**
 * Creates a nicely formatted and more specific title element text
 * for output in head of document, based on current view.
 *
 * @since jrConway Responsive Blog 1.0
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string Filtered title.
 */
function jrconwayblog_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'jrconwayblog' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'jrconwayblog_wp_title', 10, 2 );

/**
 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
 *
 * @since jrConway Responsive Blog 1.0
 */
function jrconwayblog_page_menu_args( $args ) {
	if ( ! isset( $args['show_home'] ) )
		$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'jrconwayblog_page_menu_args' );

/**
 * Registers our page widget areas.
 *
 * @since jrConway Responsive Blog 1.0
 */
function jrconwayblog_widgets_init() {
	/**
	  * Sidebar Widgets
	  *
	  * These are your typical, every day sidebar widgets. Instead of the usual set up,
	  * we'll enable the ability for a three column layout. There will be page templates
	  * for left sidebar, right sidebar, and two sidebars. These sidebars will be used for
	  * each respective side.
	  */
	register_sidebar( array(
		'name' => __( 'Right Sidebar', 'jrconwayblog' ),
		'id' => 'sidebar-1',
		'description' => __( 'Right sidebar for the page. Can be used in right sidebar and three-column layouts.', 'jrconwayblog' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Left Sidebar', 'jrconwayblog' ),
		'id' => 'sidebar-2',
		'description' => __( 'Left sidebar for the page. Can be used in left sidebar and three-column layouts.', 'jrconwayblog' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	/**
	  * Header Widgets
	  *
	  * Header widgets are defined second to avoid the default widgets being inserted into them.
	  *
	  * This could technically go into Theme Options, but what if uses want to do more
	  * advanced features with the header area? We'll still define a header upload in the
	  * common location, but if that isn't enabled it'll default to this.
	  */
	register_sidebar( array(
		'name' => __( 'Header Image', 'jrconwayblog' ),
		'id' => 'header-1',
		'description' => __( 'Header area for the site logo to go.', 'jrconwayblog' ),
		'before_widget' => '<header id="%1$s" class="widget %2$s">',
		'after_widget' => '</header>',
		'before_title' => '',
		'after_title' => '',
	) );
	register_sidebar( array(
		'name' => __( 'Header Sidebar', 'jrconwayblog' ),
		'id' => 'sidebar-1',
		'description' => __( 'Header sidebar to the right of the header image.', 'jrconwayblog' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	/**
	  * Copyright Widgets
	  *
	  * The copyright area is a narrow bar at the bottom of the page separate from the main
	  * footer widgets. For this reason we create separate footer widgets.
	  *
	  * This could technically go into Theme Options, but what if uses want to do more
	  * advanced features with the copyright area? Let's allow them to change what's in here
	  * using widgets instead of forcing a simple line of text.
	  */
	register_sidebar( array(
		'name' => __( 'Copyright Footer', 'jrconwayblog' ),
		'id' => 'copyright-1',
		'description' => __( 'Footer area for the copyright.', 'jrconwayblog' ),
		'before_widget' => '<small id="%1$s" class="widget %2$s">',
		'after_widget' => '</small>',
		'before_title' => '<h6 class="copyright-title">',
		'after_title' => '</h6>',
	) );
	register_sidebar( array(
		'name' => __( 'Copyright Sidebar', 'jrconwayblog' ),
		'id' => 'copyright-2',
		'description' => __( 'Sidebar for the copyright to enable two-column copyright area', 'jrconwayblog' ),
		'before_widget' => '<small id="%1$s" class="widget %2$s">',
		'after_widget' => '</small>',
		'before_title' => '<h6 class="copyright-title">',
		'after_title' => '</h6>',
	) );

	/**
	  * Footer Widgets
	  *
	  * Lastly we have our footer widgets. There are four footer widgets, and these will
	  * automatically be resized to fit based on how many widget areas are enabled.
	  *
	  * There will be a theme options page set up, though, to change the effects of how
	  * these will work. For example, these widget areas could go above the copyright or
	  * below the copyright. They also could be set to auto-resize based on how many widget
	  * areas are enabled, or just to sit at the center of the footer area with a fixed width.
	  */
	register_sidebar( array(
		'name' => __( 'Footer 1', 'jrconwayblog' ),
		'id' => 'footer-1',
		'description' => __( 'First widget area of the site footer.', 'jrconwayblog' ),
		'before_widget' => '<footer id="%1$s" class="widget %2$s">',
		'after_widget' => '</footer>',
		'before_title' => '<h4 class="footer-title">',
		'after_title' => '</h4>',
	) );
	register_sidebar( array(
		'name' => __( 'Footer 2', 'jrconwayblog' ),
		'id' => 'footer-2',
		'description' => __( 'Second widget area of the site footer.', 'jrconwayblog' ),
		'before_widget' => '<footer id="%1$s" class="widget %2$s">',
		'after_widget' => '</footer>',
		'before_title' => '<h4 class="footer-title">',
		'after_title' => '</h4>',
	) );
	register_sidebar( array(
		'name' => __( 'Footer 3', 'jrconwayblog' ),
		'id' => 'footer-3',
		'description' => __( 'Third widget area of the site footer.', 'jrconwayblog' ),
		'before_widget' => '<footer id="%1$s" class="widget %2$s">',
		'after_widget' => '</footer>',
		'before_title' => '<h4 class="footer-title">',
		'after_title' => '</h4>',
	) );
	register_sidebar( array(
		'name' => __( 'Footer 4', 'jrconwayblog' ),
		'id' => 'footer-4',
		'description' => __( 'Fourth widget area of the site footer.', 'jrconwayblog' ),
		'before_widget' => '<footer id="%1$s" class="widget %2$s">',
		'after_widget' => '</footer>',
		'before_title' => '<h4 class="footer-title">',
		'after_title' => '</h4>',
	) );
}
add_action( 'widgets_init', 'jrconwayblog_widgets_init' );

if ( ! function_exists( 'jrconwayblog_content_nav' ) ) :
/**
 * Displays navigation to next/previous pages when applicable.
 *
 * @since jrConway Responsive Blog 1.0
 */
function jrconwayblog_content_nav( $html_id ) {
	global $wp_query;

	$html_id = esc_attr( $html_id );

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $html_id; ?>" class="navigation" role="navigation">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'jrconwayblog' ); ?></h3>
			<div class="nav-previous alignleft"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'jrconwayblog' ) ); ?></div>
			<div class="nav-next alignright"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'jrconwayblog' ) ); ?></div>
		</nav><!-- #<?php echo $html_id; ?> .navigation -->
	<?php endif;
}
endif;

if ( ! function_exists( 'jrconwayblog_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own jrconwayblog_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since jrConway Responsive Blog 1.0
 */
function jrconwayblog_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'jrconwayblog' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'jrconwayblog' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<header class="comment-meta comment-author vcard">
				<?php
					echo get_avatar( $comment, 44 );
					printf( '<cite class="fn">%1$s %2$s</cite>',
						get_comment_author_link(),
						// If current post author is also comment author, make it known visually.
						( $comment->user_id === $post->post_author ) ? '<span> ' . __( 'Post author', 'jrconwayblog' ) . '</span>' : ''
					);
					printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						/* translators: 1: date, 2: time */
						sprintf( __( '%1$s at %2$s', 'jrconwayblog' ), get_comment_date(), get_comment_time() )
					);
				?>
			</header><!-- .comment-meta -->

			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'jrconwayblog' ); ?></p>
			<?php endif; ?>

			<section class="comment-content comment">
				<?php comment_text(); ?>
				<?php edit_comment_link( __( 'Edit', 'jrconwayblog' ), '<p class="edit-link">', '</p>' ); ?>
			</section><!-- .comment-content -->

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'jrconwayblog' ), 'after' => ' <span>&darr;</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}
endif;

if ( ! function_exists( 'jrconwayblog_entry_meta' ) ) :
/**
 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
 *
 * Create your own jrconwayblog_entry_meta() to override in a child theme.
 *
 * @since jrConway Responsive Blog 1.0
 */
function jrconwayblog_entry_meta() {
	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_category_list( __( ', ', 'jrconwayblog' ) );

	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', 'jrconwayblog' ) );

	$date = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>',
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'jrconwayblog' ), get_the_author() ) ),
		get_the_author()
	);

	// Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
	if ( $tag_list ) {
		$utility_text = __( 'This entry was posted in %1$s and tagged %2$s on %3$s<span class="by-author"> by %4$s</span>.', 'jrconwayblog' );
	} elseif ( $categories_list ) {
		$utility_text = __( 'This entry was posted in %1$s on %3$s<span class="by-author"> by %4$s</span>.', 'jrconwayblog' );
	} else {
		$utility_text = __( 'This entry was posted on %3$s<span class="by-author"> by %4$s</span>.', 'jrconwayblog' );
	}

	printf(
		$utility_text,
		$categories_list,
		$tag_list,
		$date,
		$author
	);
}
endif;

/**
 * Extends the default WordPress body class to denote:
 * 1. Using a full-width layout, when no active widgets in the sidebar
 *    or full-width template.
 * 2. Front Page template: thumbnail in use and number of sidebars for
 *    widget areas.
 * 3. White or empty background color to change the layout and spacing.
 * 4. Custom fonts enabled.
 * 5. Single or multiple authors.
 *
 * @since jrConway Responsive Blog 1.0
 *
 * @param array Existing class values.
 * @return array Filtered class values.
 */
function jrconwayblog_body_class( $classes ) {
	$background_color = get_background_color();

	if ( ! is_active_sidebar( 'sidebar-1' ) || is_page_template( 'page-templates/full-width.php' ) )
		$classes[] = 'full-width';

	if ( is_page_template( 'page-templates/front-page.php' ) ) {
		$classes[] = 'template-front-page';
		if ( has_post_thumbnail() )
			$classes[] = 'has-post-thumbnail';
		if ( is_active_sidebar( 'sidebar-2' ) && is_active_sidebar( 'sidebar-3' ) )
			$classes[] = 'two-sidebars';
	}

	if ( empty( $background_color ) )
		$classes[] = 'custom-background-empty';
	elseif ( in_array( $background_color, array( 'fff', 'ffffff' ) ) )
		$classes[] = 'custom-background-white';

	// Enable custom font class only if the font CSS is queued to load.
	if ( wp_style_is( 'jrconwayblog-fonts', 'queue' ) )
		$classes[] = 'custom-font-enabled';

	if ( ! is_multi_author() )
		$classes[] = 'single-author';

	return $classes;
}
add_filter( 'body_class', 'jrconwayblog_body_class' );

/**
 * Adjusts content_width value for full-width and single image attachment
 * templates, and when there are no active widgets in the sidebar.
 *
 * @since jrConway Responsive Blog 1.0
 */
function jrconwayblog_content_width() {
	if ( is_page_template( 'page-templates/full-width.php' ) || is_attachment() || ! is_active_sidebar( 'sidebar-1' ) ) {
		global $content_width;
		$content_width = 960;
	}
}
add_action( 'template_redirect', 'jrconwayblog_content_width' );

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @since jrConway Responsive Blog 1.0
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 * @return void
 */
function jrconwayblog_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
}
add_action( 'customize_register', 'jrconwayblog_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since jrConway Responsive Blog 1.0
 */
function jrconwayblog_customize_preview_js() {
	wp_enqueue_script( 'jrconwayblog-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), '20120827', true );
}
add_action( 'customize_preview_init', 'jrconwayblog_customize_preview_js' );


if ( ! function_exists( 'jrconwayblog_schema' ) ):
/**
 * Gets Site Schema From Theme Options
 *
 * @since JRConway Blog Template 1.0
 */
function jrconwayblog_schema() {
	// Get Theme Options
	//$theme_options = jrconwayblog_get_theme_options();
	$theme_options = array();

	// Output Schema
	if(!empty($theme_options['schema'])) {
		echo $theme_options['schema'];
	}
	else {
		echo "Blog";
	}
}
endif; // jrconwayblog_schema