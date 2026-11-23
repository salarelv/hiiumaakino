<?php
/**
 * The programme: one card per film with its screenings, and the venue and day chips.
 * Filtering happens in assets/refresh.js; old ?venue=<slug> links still work.
 *
 * @var array $args { programme: array }
 */

$programme = $args['programme'];
$ticks     = '<span class="ticks" aria-hidden="true"><i></i><i></i><i></i></span>';
$arrow     = '<svg aria-hidden="true" viewBox="0 0 16 16"><path d="M6 3l5 5-5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
?>
<section id="kava" class="programme" aria-labelledby="kava-title" tabindex="-1">
	<div class="wrap">
		<div class="section-head">
			<div>
				<p class="eyebrow"><?php echo $ticks . esc_html( hiiumaa_kino_programme_range( $programme['screenings'] ) ?: __( 'Kinokava', 'tailpress' ) ); ?></p>
				<h2 id="kava-title" class="section-title"><?php esc_html_e( 'Kinokava', 'tailpress' ); ?></h2>
			</div>
			<?php if ( $programme['screenings'] ) : ?>
				<p class="section-head__note"><?php echo esc_html( hiiumaa_kino_programme_summary( $programme ) ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( ! $programme['screenings'] ) : ?>
			<div class="empty">
				<p class="empty__title"><?php esc_html_e( 'Praegu pole kavas ühtegi seanssi.', 'tailpress' ); ?></p>
				<p><?php esc_html_e( 'Uued filmid lisanduvad peagi – vaata allpool, mis on tulekul.', 'tailpress' ); ?></p>
			</div>
		<?php else : ?>

			<div class="filters" role="group" aria-label="<?php esc_attr_e( 'Filtreeri kava', 'tailpress' ); ?>">
				<div class="filters__row">
					<span class="filters__label" id="flt-venue"><?php esc_html_e( 'Asukoht', 'tailpress' ); ?></span>
					<div class="chips" role="group" aria-labelledby="flt-venue">
						<button type="button" class="chip" data-filter-venue="all" aria-pressed="true"><?php esc_html_e( 'Kõik', 'tailpress' ); ?></button>
						<?php foreach ( $programme['venues'] as $slug => $venue ) : ?>
							<button type="button" class="chip" data-filter-venue="<?php echo esc_attr( $slug ); ?>" aria-pressed="false" title="<?php echo esc_attr( $venue['name'] ); ?>"><?php echo esc_html( $venue['short'] ); ?> <span class="chip__count"><?php echo (int) $venue['count']; ?></span></button>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="filters__row">
					<span class="filters__label" id="flt-day"><?php esc_html_e( 'Päev', 'tailpress' ); ?></span>
					<div class="chips chips--scroll" role="group" aria-labelledby="flt-day">
						<button type="button" class="chip chip--day" data-filter-day="all" aria-pressed="true"><?php esc_html_e( 'Kõik päevad', 'tailpress' ); ?></button>
						<?php foreach ( $programme['days'] as $key => $d ) : ?>
							<button type="button" class="chip chip--day" data-filter-day="<?php echo esc_attr( $key ); ?>" aria-pressed="false"><span class="chip__wd" data-rel-day="<?php echo esc_attr( $key ); ?>" data-rel-style="short"><?php echo esc_html( hiiumaa_kino_relative_day( $d ) ?: hiiumaa_kino_weekday_short( $d ) ); ?></span> <span class="chip__date"><?php echo esc_html( $d->format( 'j.m' ) ); ?></span></button>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<p class="filters__status" role="status" aria-live="polite"></p>

			<div class="films">
				<?php foreach ( $programme['films'] as $film ) : ?>
					<?php
					$n      = count( $film['screenings'] );
					$venues = implode( ' ', array_unique( wp_list_pluck( $film['screenings'], 'venue_slug' ) ) );
					$meta   = array_filter( array( $film['runtime'], $film['language'] ) );
					?>
					<article class="film" data-venues="<?php echo esc_attr( $venues ); ?>" data-title="<?php echo esc_attr( $film['title'] ); ?>" data-href="<?php echo esc_url( $film['url'] ); ?>" data-meta="<?php echo esc_attr( $film['meta'] ); ?>" data-desc="<?php echo esc_attr( $film['desc'] ); ?>" data-backdrop="<?php echo esc_url( $film['backdrop'] ); ?>">
						<a class="film__poster" href="<?php echo esc_url( $film['url'] ); ?>" tabindex="-1" aria-hidden="true">
							<?php if ( $film['poster'] ) : ?>
								<img src="<?php echo esc_url( $film['poster'] ); ?>" alt="" width="480" height="720" loading="lazy" decoding="async">
							<?php endif; ?>
						</a>
						<div class="film__head">
							<h3 class="film__title"><a href="<?php echo esc_url( $film['url'] ); ?>"><?php echo esc_html( $film['title'] ); ?></a></h3>
							<?php if ( $meta ) : ?>
								<p class="film__meta"><?php foreach ( $meta as $m ) : ?><span><?php echo esc_html( $m ); ?></span><?php endforeach; ?></p>
							<?php endif; ?>
							<p class="film__tags">
								<?php if ( $film['rating'] ) : ?>
									<span class="age age--<?php echo esc_attr( hiiumaa_kino_age_class( $film['rating'] ) ); ?>" title="<?php esc_attr_e( 'Vanusepiirang', 'tailpress' ); ?>"><?php echo esc_html( $film['rating'] ); ?></span>
								<?php endif; ?>
								<span class="film__count"><?php echo esc_html( $n . ( 1 === $n ? ' seanss' : ' seanssi' ) ); ?></span>
							</p>
						</div>
						<ul class="showtimes" aria-label="<?php echo esc_attr( $film['title'] . ' – ' . __( 'seansid', 'tailpress' ) ); ?>">
							<?php foreach ( $film['screenings'] as $s ) : ?>
								<li class="showtime" data-venue="<?php echo esc_attr( $s['venue_slug'] ); ?>" data-day="<?php echo esc_attr( $s['day'] ); ?>" data-start="<?php echo esc_attr( $s['iso'] ); ?>">
									<a class="showtime__link" href="<?php echo esc_url( $s['checkout'] ); ?>">
										<time class="showtime__when" datetime="<?php echo esc_attr( $s['iso'] ); ?>"><span class="showtime__day" data-rel-day="<?php echo esc_attr( $s['day'] ); ?>"><?php echo esc_html( hiiumaa_kino_day_label( $s['start'] ) ); ?></span> <span class="showtime__time"><?php echo esc_html( $s['start']->format( 'H:i' ) ); ?></span></time>
										<span class="showtime__venue"><?php echo esc_html( $s['venue'] ); ?></span>
										<span class="showtime__cta"><?php esc_html_e( 'Osta pilet', 'tailpress' ); ?><?php echo $arrow; ?></span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</article>
				<?php endforeach; ?>
			</div>

			<div class="empty" hidden>
				<p class="empty__title"><?php esc_html_e( 'Selle valikuga seansse ei leitud.', 'tailpress' ); ?></p>
				<p><?php esc_html_e( 'Proovi mõnda teist päeva või asukohta.', 'tailpress' ); ?></p>
				<button type="button" class="btn btn--outline" data-filter-reset><?php esc_html_e( 'Näita kogu kava', 'tailpress' ); ?></button>
			</div>
		<?php endif; ?>
	</div>
</section>
