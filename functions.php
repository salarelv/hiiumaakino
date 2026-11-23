<?php
require_once get_template_directory() . '/inc/upcoming-films.php';
require_once get_template_directory() . '/inc/programme.php';

/**
 * Theme setup.
 */
function tailpress_setup() {
	add_theme_support( 'title-tag' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'tailpress' ),
			'footer'  => __( 'Footer Menu', 'tailpress' ),
		)
	);

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		)
	);

    add_theme_support( 'custom-logo' );
	add_theme_support( 'post-thumbnails' );

	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );

	add_theme_support( 'editor-styles' );
	add_editor_style( 'css/editor-style.css' );
}

add_action( 'after_setup_theme', 'tailpress_setup' );

/**
 * Enqueue theme assets.
 *
 * assets/refresh.css and assets/refresh.js (header, footer, front page) load everywhere.
 * The other pages still use the old Tailwind build (css/app.css) for their content.
 */
function tailpress_enqueue_scripts() {
	$theme   = wp_get_theme();
	$dir     = get_template_directory();
	$uri     = get_template_directory_uri();
	$is_home = hiiumaa_kino_is_programme_page();
	$deps    = array();

	if ( ! $is_home ) {
		wp_enqueue_style( 'tailpress', tailpress_asset( 'css/app.css' ), array(), filemtime( $dir . '/css/app.css' ) );
		wp_enqueue_style( 'satoshi', tailpress_asset( 'fonts/Satoshi/stylesheet.css' ), array(), $theme->get( 'Version' ) );
		$deps = array( 'tailpress', 'satoshi' );
	}

	wp_enqueue_style( 'hiiumaa-kino-refresh', $uri . '/assets/refresh.css', $deps, filemtime( $dir . '/assets/refresh.css' ) );
	wp_enqueue_script( 'hiiumaa-kino-refresh', $uri . '/assets/refresh.js', array(), filemtime( $dir . '/assets/refresh.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
}

/** Is this the front page programme ("Kinokava ja tulevased filmid" template)? */
function hiiumaa_kino_is_programme_page() {
	return is_page_template( 'page-programme.php' );
}

/**
 * The front page has its own filters, so it does not need the Kinola filter form assets
 * (kinola.css, select2, kinola.js and jQuery).
 */
add_filter( 'kinola/assets/css', function ( $load ) {
	return hiiumaa_kino_is_programme_page() ? false : $load;
} );
add_filter( 'kinola/assets/select2', function ( $load ) {
	return hiiumaa_kino_is_programme_page() ? false : $load;
} );
add_action( 'wp_enqueue_scripts', function () {
	if ( hiiumaa_kino_is_programme_page() ) {
		wp_dequeue_script( 'kinola' );
	}
}, 100 );

add_action( 'wp_enqueue_scripts', 'tailpress_enqueue_scripts' );

/**
 * Get asset path.
 *
 * @param string  $path Path to asset.
 *
 * @return string
 */
function tailpress_asset( $path ) {
	if ( wp_get_environment_type() === 'production' ) {
		return get_stylesheet_directory_uri() . '/' . $path;
	}

	return add_query_arg( 'time', time(),  get_stylesheet_directory_uri() . '/' . $path );
}

/**
 * Adds option 'li_class' to 'wp_nav_menu'.
 *
 * @param string  $classes String of classes.
 * @param mixed   $item The current item.
 * @param WP_Term $args Holds the nav menu arguments.
 *
 * @return array
 */
function tailpress_nav_menu_add_li_class( $classes, $item, $args, $depth ) {
	if ( isset( $args->li_class ) ) {
		$classes[] = $args->li_class;
	}

	if ( isset( $args->{"li_class_$depth"} ) ) {
		$classes[] = $args->{"li_class_$depth"};
	}

	return $classes;
}

add_filter( 'nav_menu_css_class', 'tailpress_nav_menu_add_li_class', 10, 4 );

/**
 * Adds option 'submenu_class' to 'wp_nav_menu'.
 *
 * @param string  $classes String of classes.
 * @param mixed   $item The current item.
 * @param WP_Term $args Holds the nav menu arguments.
 *
 * @return array
 */
function tailpress_nav_menu_add_submenu_class( $classes, $args, $depth ) {
	if ( isset( $args->submenu_class ) ) {
		$classes[] = $args->submenu_class;
	}

	if ( isset( $args->{"submenu_class_$depth"} ) ) {
		$classes[] = $args->{"submenu_class_$depth"};
	}

	return $classes;
}

/**
	* Adds our template to the page dropdown for v4.7+
	*
	*/
function add_new_template( $posts_templates ) {
	$th_templates = array(
		//'templates/wf-webmap-template.php' => 'Floorplan',
	);

	$posts_templates = array_merge( $posts_templates, $th_templates );
	return $posts_templates;
}


add_filter('theme_page_templates', 'add_new_template');

add_filter( 'nav_menu_submenu_css_class', 'tailpress_nav_menu_add_submenu_class', 10, 3 );

// The logo markup is built in hiiumaa_kino_logo() (inc/programme.php).

function change_kinola_dir() {
	$dir[] = get_template_directory() . "/kinola-templates/";
    return $dir;
}

add_filter('kinola/template_directories', 'change_kinola_dir');
