<?php /* Template Name: Lai */ ?>
<?php get_header(); ?>

<div class="container container-wide mx-auto my-8">

	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>

			<?php get_template_part( 'template-parts/content-wide', get_post_format() ); ?>

		<?php endwhile; ?>

	<?php endif; ?>

</div>

<?php
get_footer();

