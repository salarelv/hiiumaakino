<?php
/**
 * Front page programme (hero, film list, filters) built from the Kinola events,
 * plus the small helpers the refreshed header and footer use.
 */

use Kinola\KinolaWp\Event_Query;
use Kinola\KinolaWp\Film;
use Kinola\KinolaWp\Helpers;

/* ---------- Estonian date words (the same ones assets/refresh.js uses) ---------- */

function hiiumaa_kino_weekday_short( DateTimeInterface $d ) {
	return array( 'P', 'E', 'T', 'K', 'N', 'R', 'L' )[ (int) $d->format( 'w' ) ];
}

function hiiumaa_kino_weekday_long( DateTimeInterface $d ) {
	return array( 'pühapäev', 'esmaspäev', 'teisipäev', 'kolmapäev', 'neljapäev', 'reede', 'laupäev' )[ (int) $d->format( 'w' ) ];
}

function hiiumaa_kino_month_short( DateTimeInterface $d ) {
	return array( 'jaan', 'veebr', 'märts', 'apr', 'mai', 'juuni', 'juuli', 'aug', 'sept', 'okt', 'nov', 'dets' )[ (int) $d->format( 'n' ) - 1 ];
}

/**
 * "Täna" / "Homme" for today and tomorrow (site time zone), otherwise null.
 * refresh.js redoes this in the visitor's browser, so cached pages stay right.
 */
function hiiumaa_kino_relative_day( DateTimeInterface $d ) {
	$today = current_datetime();
	$key   = $d->format( 'Y-m-d' );
	if ( $key === $today->format( 'Y-m-d' ) ) {
		return __( 'Täna', 'tailpress' );
	}
	if ( $key === $today->modify( '+1 day' )->format( 'Y-m-d' ) ) {
		return __( 'Homme', 'tailpress' );
	}
	return null;
}

/** "Täna", "Homme" or "R 27.11". */
function hiiumaa_kino_day_label( DateTimeInterface $d ) {
	return hiiumaa_kino_relative_day( $d ) ?: hiiumaa_kino_weekday_short( $d ) . ' ' . $d->format( 'j.m' );
}

/* ---------- Films ---------- */

/** Kinola media URLs take a width parameter; ask for a smaller image where a full-size one is not needed. */
function hiiumaa_kino_resize_image( $url, $width ) {
	if ( ! $url ) {
		return '';
	}
	if ( false !== strpos( $url, 'media.kinola.ee' ) ) {
		return add_query_arg( array( 'width' => $width, 'quality' => 85 ), $url );
	}
	return $url;
}

/** CSS modifier for the age rating badge. */
function hiiumaa_kino_age_class( $rating ) {
	$map = array( 'PERE' => 'family', 'K-12' => 'k12', 'MS-12' => 'ms12' );
	return $map[ strtoupper( trim( (string) $rating ) ) ] ?? 'other';
}

/** Everything the front page shows about one film. */
function hiiumaa_kino_film_data( Film $film ) {
	$gallery  = $film->get_field( 'gallery', false );
	$backdrop = '';
	if ( is_array( $gallery ) && $gallery ) {
		$last     = end( $gallery );
		$backdrop = is_array( $last ) ? ( $last['src'] ?? '' ) : '';
	}
	$poster = $film->get_field( 'poster' );

	$language = array();
	if ( $film->get_field( 'languages' ) ) {
		/* translators: %s: language name(s), lower case */
		$language[] = sprintf( __( '%s keeles', 'tailpress' ), mb_strtolower( $film->get_field( 'languages' ) ) );
	}
	if ( $film->get_field( 'subtitles' ) ) {
		/* translators: %s: subtitle language(s), lower case */
		$language[] = sprintf( __( '%s subtiitrid', 'tailpress' ), mb_strtolower( $film->get_field( 'subtitles' ) ) );
	}
	$runtime = $film->get_field( 'runtime' ) ? $film->get_field( 'runtime' ) . ' min' : '';
	$rating  = (string) $film->get_field( 'rating' );

	$data = array(
		'id'       => $film->get_remote_id(),
		'title'    => $film->get_field( 'title' ) ?: $film->get_title(),
		'url'      => $film->get_local_url(),
		'poster'   => hiiumaa_kino_resize_image( $poster, 480 ),
		'backdrop' => hiiumaa_kino_resize_image( $backdrop ?: $poster, 1600 ),
		'runtime'  => $runtime,
		'language' => implode( ', ', $language ),
		'rating'   => $rating,
		'desc'     => wp_trim_words( wp_strip_all_tags( (string) $film->get_field( 'description' ) ), 60, '…' ),
	);
	// Short meta line under the film title: "88 min · eesti keeles · MS-12".
	$data['meta'] = implode( ' · ', array_filter( array( $data['runtime'], $data['language'], $data['rating'] ) ) );

	return apply_filters( 'hiiumaa_kino_film_data', $data, $film );
}

/* ---------- Screenings ---------- */

/** "Kärdla kinosaal" -> "Kärdla": the chip label for a venue. */
function hiiumaa_kino_venue_short_name( $name ) {
	$short = strtok( trim( $name ), ' ' );
	return apply_filters( 'hiiumaa_kino_venue_short_name', $short ?: $name, $name );
}

/**
 * All upcoming screenings, grouped for the front page.
 *
 * @return array{screenings: array, films: array, venues: array, days: array}
 */
function hiiumaa_kino_programme() {
	static $programme = null;
	if ( null !== $programme ) {
		return $programme;
	}
	$programme = array( 'screenings' => array(), 'films' => array(), 'venues' => array(), 'days' => array() );

	if ( ! class_exists( Event_Query::class ) ) {
		return $programme;
	}

	$film_cache = array();
	foreach ( ( new Event_Query() )->upcoming()->get() as $event ) {
		$film_id = $event->get_field( Film::FIELD_ID );
		if ( ! $film_id ) {
			continue;
		}
		if ( ! array_key_exists( $film_id, $film_cache ) ) {
			$film                   = Film::find_by_remote_id( $film_id );
			$film_cache[ $film_id ] = $film ? hiiumaa_kino_film_data( $film ) : null;
		}
		if ( ! $film_cache[ $film_id ] ) {
			continue;
		}

		$start = Helpers::format_datetime( $event->get_field( 'time' ) );
		$venue = $event->get_venue();
		$s     = array(
			'film_id'     => $film_id,
			'start'       => $start,
			'iso'         => $start->format( 'Y-m-d\TH:i' ),
			'day'         => $start->format( 'Y-m-d' ),
			'venue'       => $venue ? $venue->name : '',
			'venue_slug'  => $venue ? $venue->slug : '',
			'checkout'    => $event->get_checkout_url(),
		);
		$programme['screenings'][] = $s;

		if ( ! isset( $programme['films'][ $film_id ] ) ) {
			$programme['films'][ $film_id ] = $film_cache[ $film_id ] + array( 'screenings' => array() );
		}
		$programme['films'][ $film_id ]['screenings'][] = $s;

		if ( $s['venue_slug'] ) {
			if ( ! isset( $programme['venues'][ $s['venue_slug'] ] ) ) {
				$programme['venues'][ $s['venue_slug'] ] = array( 'name' => $s['venue'], 'short' => hiiumaa_kino_venue_short_name( $s['venue'] ), 'count' => 0 );
			}
			$programme['venues'][ $s['venue_slug'] ]['count']++;
		}
		if ( ! isset( $programme['days'][ $s['day'] ] ) ) {
			$programme['days'][ $s['day'] ] = $start;
		}
	}

	// Busiest venue first (Kärdla), the rest by name.
	uasort( $programme['venues'], function ( $a, $b ) {
		return ( $b['count'] <=> $a['count'] ) ?: strcoll( $a['name'], $b['name'] );
	} );

	return $programme;
}

/** "17. nov – 2. dets" */
function hiiumaa_kino_programme_range( array $screenings ) {
	if ( ! $screenings ) {
		return '';
	}
	$first = $screenings[0]['start'];
	$last  = end( $screenings )['start'];
	$fmt   = function ( $d ) {
		return $d->format( 'j' ) . '. ' . hiiumaa_kino_month_short( $d );
	};
	return $fmt( $first ) === $fmt( $last ) ? $fmt( $first ) : $fmt( $first ) . ' – ' . $fmt( $last );
}

/** "3 seanssi, 2 filmi, 4 asukohta üle saare" */
function hiiumaa_kino_programme_summary( array $p ) {
	$n = count( $p['screenings'] );
	$f = count( $p['films'] );
	$v = count( $p['venues'] );
	return sprintf(
		'%d %s, %d %s, %d %s %s',
		$n, 1 === $n ? 'seanss' : 'seanssi',
		$f, 1 === $f ? 'film' : 'filmi',
		$v, 1 === $v ? 'asukoht' : 'asukohta',
		__( 'üle saare', 'tailpress' )
	);
}

/* ---------- Header and footer helpers ---------- */

/** The custom logo (Customizer > Site Identity) as a plain <img>, falling back to the site name. */
function hiiumaa_kino_logo( $class, $sizes ) {
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		return wp_get_attachment_image( $logo_id, 'full', false, array(
			'class'   => $class,
			'sizes'   => $sizes,
			'alt'     => get_bloginfo( 'name' ),
			'loading' => false,
		) );
	}
	return '<span class="' . esc_attr( $class ) . ' site-logo__text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
}

/**
 * Menu item classes for the new header:
 * - current item gets "is-current";
 * - "menu-nupp" (the old green button class) works as "nav-cta".
 */
function hiiumaa_kino_menu_item_classes( $classes, $item, $args ) {
	if ( array_intersect( array( 'current-menu-item', 'current_page_item' ), $classes ) ) {
		$classes[] = 'is-current';
	}
	if ( in_array( 'menu-nupp', $classes, true ) ) {
		$classes[] = 'nav-cta';
	}
	return array_unique( $classes );
}
add_filter( 'nav_menu_css_class', 'hiiumaa_kino_menu_item_classes', 20, 3 );

/** Heart icon in front of the outlined button (the donation campaign link). */
function hiiumaa_kino_menu_item_title( $title, $item, $args ) {
	if ( empty( $args->hiiumaa_footer ) && in_array( 'nav-cta--ghost', (array) $item->classes, true ) ) {
		$title = '<svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>' . $title;
	}
	return $title;
}
add_filter( 'nav_menu_item_title', 'hiiumaa_kino_menu_item_title', 10, 3 );

/** When the footer borrows the main menu, leave out the home link and the outlined button. */
function hiiumaa_kino_footer_menu_items( $items, $args ) {
	if ( empty( $args->hiiumaa_footer ) || 'primary' !== $args->theme_location ) {
		return $items;
	}
	return array_filter( $items, function ( $item ) {
		$classes = (array) $item->classes;
		return ! in_array( 'nav-cta--ghost', $classes, true )
			&& untrailingslashit( $item->url ) !== untrailingslashit( home_url( '/' ) );
	} );
}
add_filter( 'wp_nav_menu_objects', 'hiiumaa_kino_footer_menu_items', 10, 2 );
