<?php
/**
 * Films imported from Kinola that are absent from the programme.
 *
 * @var \Kinola\KinolaWp\Film[] $films
 */
?>
<?php if ( $films ) : ?>
	<ul class="cinema-upcoming-films">
		<?php foreach ( $films as $film ) : ?>
			<?php
			$poster = $film->get_field( 'poster' );
			$release_date = $film->get_field( 'release_date' );
			$release_date_time = $release_date ? DateTimeImmutable::createFromFormat( '!Y-m-d', $release_date, wp_timezone() ) : false;
			?>
			<li class="cinema-upcoming-film">
				<a class="cinema-upcoming-film__link" href="<?php echo esc_url( $film->get_local_url() ); ?>">
					<?php if ( $poster ) : ?>
						<img class="cinema-upcoming-film__poster" src="<?php echo esc_url( $poster ); ?>" alt="" width="80" height="120" loading="lazy" decoding="async" />
					<?php endif; ?>
					<span class="cinema-upcoming-film__details">
						<span class="cinema-upcoming-film__title"><?php echo esc_html( $film->get_field( 'title' ) ?: $film->get_title() ); ?></span>
						<?php if ( $film->get_field( 'genres' ) ) : ?>
							<span class="cinema-upcoming-film__genres"><?php echo esc_html( $film->get_field( 'genres' ) ); ?></span>
						<?php endif; ?>
						<?php if ( $release_date_time ) : ?>
							<time class="cinema-upcoming-film__release-date" datetime="<?php echo esc_attr( $release_date_time->format( 'Y-m-d' ) ); ?>"><strong><?php echo esc_html( wp_date( get_option( 'date_format' ), $release_date_time->getTimestamp() ) ); ?></strong></time>
						<?php endif; ?>
					</span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
