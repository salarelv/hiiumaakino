<?php
/**
 * Kinola films without attached events or a release date before today.
 */

use Kinola\KinolaWp\Event_Query;
use Kinola\KinolaWp\Film;
use Kinola\KinolaWp\Helpers;
use Kinola\KinolaWp\View;

function hiiumaa_kino_get_upcoming_films() {
	if ( ! class_exists( Event_Query::class ) || ! class_exists( Film::class ) ) {
		return array();
	}

	$scheduled_film_ids = array();
	foreach ( ( new Event_Query() )->get() as $event ) {
		$film_id = $event->get_field( Film::FIELD_ID );
		if ( $film_id ) {
			$scheduled_film_ids[] = $film_id;
		}
	}

	$args = array(
		'post_type'      => Helpers::get_films_post_type(),
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	);

	if ( $scheduled_film_ids ) {
		$args['meta_query'] = array(
			array(
				'key'     => Film::FIELD_ID,
				'value'   => array_values( array_unique( $scheduled_film_ids ) ),
				'compare' => 'NOT IN',
			),
		);
	}

	$films = array();
	$seen_film_ids = array();
	$today = current_datetime()->format( 'Y-m-d' );
	foreach ( ( new WP_Query( $args ) )->posts as $film_post ) {
		$film = new Film( $film_post );
		$film_id = $film->get_remote_id();
		if ( ! $film_id || isset( $seen_film_ids[ $film_id ] ) || $film->get_field( 'events', false ) ) {
			continue;
		}

		$release_date = $film->get_field( 'release_date' );
		if ( $release_date && $release_date < $today ) {
			continue;
		}

		$seen_film_ids[ $film_id ] = true;
		$films[] = $film;
	}

	usort( $films, function ( $first, $second ) {
		$first_date = $first->get_field( 'release_date' ) ?: '9999-12-31';
		$second_date = $second->get_field( 'release_date' ) ?: '9999-12-31';

		return strcmp( $first_date, $second_date ) ?: strnatcasecmp( $first->get_title(), $second->get_title() );
	} );

	return $films;
}

function hiiumaa_kino_render_upcoming_films() {
	if ( ! class_exists( View::class ) ) {
		return '';
	}

	$films = hiiumaa_kino_get_upcoming_films();
	if ( ! $films ) {
		return '';
	}

	return View::get_rendered_template( 'upcoming-films', array(
		'films' => $films,
	) );
}
