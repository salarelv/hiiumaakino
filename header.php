<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#06347d">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="preload" href="<?php echo esc_url( get_template_directory_uri() . '/fonts/Satoshi/fonts/Satoshi-Variable.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
	<script>document.documentElement.classList.add('js')</script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#<?php echo is_page_template( 'page-programme.php' ) ? 'kava' : 'main'; ?>"><?php echo is_page_template( 'page-programme.php' ) ? esc_html__( 'Liigu kinokava juurde', 'tailpress' ) : esc_html__( 'Liigu sisu juurde', 'tailpress' ); ?></a>

<?php do_action( 'tailpress_site_before' ); ?>
<?php do_action( 'tailpress_header' ); ?>

<header class="site-header">
	<div class="wrap site-header__inner">
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?>>
			<?php echo hiiumaa_kino_logo( 'site-logo__img', '160px' ); ?>
		</a>
		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-menu">
				<span class="nav-toggle__bars" aria-hidden="true"><i></i><i></i><i></i></span>
				<span class="nav-toggle__label"><?php esc_html_e( 'Menüü', 'tailpress' ); ?></span>
			</button>
			<?php
			wp_nav_menu(
				array(
					'theme_location'  => 'primary',
					'container'       => 'nav',
					'container_id'    => 'primary-menu',
					'container_class' => 'primary-nav',
					'container_aria_label' => __( 'Peamenüü', 'tailpress' ),
					'items_wrap'      => '<ul>%3$s</ul>',
					'depth'           => 1,
					'fallback_cb'     => false,
				)
			);
			?>
		<?php endif; ?>
	</div>
</header>

<?php do_action( 'tailpress_content_start' ); ?>

<main id="main" class="site-main">
