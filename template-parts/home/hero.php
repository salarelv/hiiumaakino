<?php
/**
 * Hero: the next screening, and on desktop the four after it ("Lähipäevadel").
 * refresh.js moves it on to the next screening when this one has started (cached pages).
 *
 * @var array $args { programme: array }
 */

$programme = $args['programme'];
$next      = $programme['screenings'][0];
$film      = $programme['films'][ $next['film_id'] ];
$start     = $next['start'];
$relative  = hiiumaa_kino_relative_day( $start );
$when      = ( $relative ?: ucfirst( hiiumaa_kino_weekday_long( $start ) ) . ', ' . $start->format( 'j' ) . '. ' . hiiumaa_kino_month_short( $start ) )
	. ' ' . __( 'kell', 'tailpress' ) . ' ' . $start->format( 'H:i' );
?>
<section class="hero" aria-labelledby="hero-title" data-hero-start="<?php echo esc_attr( $next['iso'] ); ?>">
	<?php if ( $film['backdrop'] ) : ?>
		<img class="hero__backdrop" src="<?php echo esc_url( $film['backdrop'] ); ?>" alt="" fetchpriority="high">
	<?php endif; ?>
	<div class="wrap hero__inner">
		<div class="hero__text">
			<p class="hero__eyebrow"><span class="ticks" aria-hidden="true"><i></i><i></i><i></i></span><?php esc_html_e( 'Järgmine seanss', 'tailpress' ); ?></p>
			<h1 id="hero-title" class="hero__title"><a href="<?php echo esc_url( $film['url'] ); ?>"><?php echo esc_html( $film['title'] ); ?></a></h1>
			<p class="hero__when"><strong data-hero-when><?php echo esc_html( $when ); ?></strong> <span class="hero__sep" aria-hidden="true">·</span> <span data-hero-venue><?php echo esc_html( $next['venue'] ); ?></span></p>
			<p class="hero__meta" data-hero-meta><?php echo esc_html( $film['meta'] ); ?></p>
			<p class="hero__desc" data-hero-desc><?php echo esc_html( $film['desc'] ); ?></p>
			<div class="hero__actions">
				<a class="btn btn--primary btn--lg" data-hero-buy href="<?php echo esc_url( $next['checkout'] ); ?>"><?php esc_html_e( 'Osta pilet', 'tailpress' ); ?></a>
				<a class="btn btn--ghost-light btn--lg" data-hero-info href="<?php echo esc_url( $film['url'] ); ?>"><?php esc_html_e( 'Filmist lähemalt', 'tailpress' ); ?></a>
			</div>
		</div>
		<?php $soon = array_slice( $programme['screenings'], 1, 4 ); ?>
		<?php if ( $soon ) : ?>
			<aside class="hero__next" aria-labelledby="next-title">
				<h2 id="next-title" class="hero__next-title"><?php esc_html_e( 'Lähipäevadel', 'tailpress' ); ?></h2>
				<ol class="next-list">
					<?php foreach ( $soon as $s ) : ?>
						<li><a class="next-item" href="<?php echo esc_url( $s['checkout'] ); ?>"><time datetime="<?php echo esc_attr( $s['iso'] ); ?>"><span data-rel-day="<?php echo esc_attr( $s['day'] ); ?>"><?php echo esc_html( hiiumaa_kino_day_label( $s['start'] ) ); ?></span> <b><?php echo esc_html( $s['start']->format( 'H:i' ) ); ?></b></time><span class="next-item__title"><?php echo esc_html( $programme['films'][ $s['film_id'] ]['title'] ); ?></span><span class="next-item__venue"><?php echo esc_html( $s['venue'] ); ?></span></a></li>
					<?php endforeach; ?>
				</ol>
				<a class="hero__next-all" href="#kava"><?php esc_html_e( 'Kogu kava', 'tailpress' ); ?></a>
			</aside>
		<?php endif; ?>
	</div>
</section>
