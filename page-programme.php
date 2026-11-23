<?php
/**
 * Template Name: Kinokava ja tulevased filmid
 * Template Post Type: page
 */

get_header();
?>

<div class="container container-wide mx-auto my-8 cinema-home">
	<?php while ( have_posts() ) : ?>
		<?php
		the_post();
		$upcoming_films = hiiumaa_kino_render_upcoming_films();
		?>
		<div class="cinema-home__layout<?php echo $upcoming_films ? ' cinema-home__layout--with-upcoming' : ''; ?>">
			<div class="cinema-home__programme">
				<?php get_template_part( 'template-parts/content', get_post_format() ); ?>
			</div>
			<?php if ( $upcoming_films ) : ?>
				<aside class="cinema-home__upcoming" aria-labelledby="upcoming-films-title">
					<h2 id="upcoming-films-title"><?php esc_html_e( 'Tulekul', 'tailpress' ); ?></h2>
					<?php echo $upcoming_films; ?>
				</aside>
			<?php endif; ?>
		</div>
	<?php endwhile; ?>
</div>

<?php get_footer(); ?>
