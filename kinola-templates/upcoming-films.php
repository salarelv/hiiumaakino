<?php
/**
 * "Varsti kinos": films imported from Kinola that have no screenings yet.
 * Rendered by hiiumaa_kino_render_upcoming_films() (inc/upcoming-films.php).
 *
 * @var \Kinola\KinolaWp\Film[] $films
 */
?>
<?php if ( $films ) : ?>
	<ul class="soon-list">
		<?php foreach ( $films as $film ) : ?>
			<?php
			$poster            = hiiumaa_kino_resize_image( $film->get_field( 'poster' ), 480 );
			$release_date      = $film->get_field( 'release_date' );
			$release_date_time = $release_date ? DateTimeImmutable::createFromFormat( '!Y-m-d', $release_date, wp_timezone() ) : false;
			?>
			<li class="soon">
				<a class="soon__link" href="<?php echo esc_url( $film->get_local_url() ); ?>">
					<?php if ( $poster ) : ?>
						<img class="soon__poster" src="<?php echo esc_url( $poster ); ?>" alt="" width="480" height="720" loading="lazy" decoding="async">
					<?php endif; ?>
					<span class="soon__body">
						<?php if ( $release_date_time ) : ?>
							<time class="soon__date" datetime="<?php echo esc_attr( $release_date_time->format( 'Y-m-d' ) ); ?>"><?php echo esc_html( sprintf( __( 'Alates %s', 'tailpress' ), hiiumaa_kino_weekday_short( $release_date_time ) . ', ' . $release_date_time->format( 'j' ) . '. ' . hiiumaa_kino_month_short( $release_date_time ) ) ); ?></time>
						<?php endif; ?>
						<span class="soon__title"><?php echo esc_html( $film->get_field( 'title' ) ?: $film->get_title() ); ?></span>
						<?php if ( $film->get_field( 'genres' ) ) : ?>
							<span class="soon__genres"><?php echo esc_html( $film->get_field( 'genres' ) ); ?></span>
						<?php endif; ?>
					</span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
