<?php
/**
 * Template Name: Kinokava ja tulevased filmid
 * Template Post Type: page
 *
 * Front page: next screening (hero), the programme with venue and day filters,
 * "Varsti kinos" and the ticket / Soovikino / donation cards.
 * The data comes from the Kinola events, see inc/programme.php.
 */

get_header();

$programme = hiiumaa_kino_programme();

while ( have_posts() ) :
	the_post();

	if ( $programme['screenings'] ) {
		get_template_part( 'template-parts/home/hero', null, array( 'programme' => $programme ) );
	}
	get_template_part( 'template-parts/home/programme', null, array( 'programme' => $programme ) );

	// Anything else written on the page in the editor (the [kinola_events] shortcode is replaced by the list above).
	$extra = trim( preg_replace( '/\[kinola_events[^\]]*\]/', '', get_the_content() ) );
	if ( '' !== trim( wp_strip_all_tags( $extra ) ) ) :
		?>
		<section class="page-extra">
			<div class="wrap entry-content"><?php echo apply_filters( 'the_content', $extra ); ?></div>
		</section>
		<?php
	endif;

	$upcoming_films = hiiumaa_kino_render_upcoming_films();
	if ( $upcoming_films ) :
		?>
		<section class="soon-section" aria-labelledby="soon-title">
			<div class="wrap">
				<div class="section-head">
					<div>
						<p class="eyebrow"><span class="ticks" aria-hidden="true"><i></i><i></i><i></i></span><?php esc_html_e( 'Tulekul', 'tailpress' ); ?></p>
						<h2 id="soon-title" class="section-title"><?php esc_html_e( 'Varsti kinos', 'tailpress' ); ?></h2>
					</div>
				</div>
				<?php echo $upcoming_films; ?>
			</div>
		</section>
		<?php
	endif;

	get_template_part( 'template-parts/home/info' );
endwhile;

get_footer();
