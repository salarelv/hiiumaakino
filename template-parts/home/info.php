<?php
/**
 * Ticket prices, Soovikino and the donation campaign.
 * Links point at the existing pages by their address (slug).
 */

$link = function ( $path ) {
	$page = get_page_by_path( $path );
	return $page ? get_permalink( $page ) : home_url( '/' . $path . '/' );
};
?>
<section class="info" aria-label="<?php esc_attr_e( 'Lisainfo', 'tailpress' ); ?>">
	<div class="wrap info__grid">
		<article class="info-card info-card--tickets">
			<h2 class="info-card__title"><?php esc_html_e( 'Piletid', 'tailpress' ); ?></h2>
			<dl class="prices">
				<div><dt><?php esc_html_e( 'Tavapilet', 'tailpress' ); ?></dt><dd>7 €</dd></div>
				<div><dt><?php esc_html_e( 'Sooduspilet', 'tailpress' ); ?> <small><?php esc_html_e( 'lapsed, õpilased, pensionärid', 'tailpress' ); ?></small></dt><dd>6 €</dd></div>
				<div><dt><?php esc_html_e( 'Dokumentaalfilmid', 'tailpress' ); ?> <small><?php esc_html_e( 'ja perekaardi või nimelise tooli omanikele', 'tailpress' ); ?></small></dt><dd>5 €</dd></div>
			</dl>
			<p class="info-card__note"><?php esc_html_e( 'Kärdla kinosaali seanssidele eelmüük veebis või kohapeal kuni 15 minutit enne algust.', 'tailpress' ); ?></p>
			<a class="text-link" href="<?php echo esc_url( $link( 'piletiinfo' ) ); ?>"><?php esc_html_e( 'Kogu piletiinfo', 'tailpress' ); ?></a>
		</article>
		<article class="info-card info-card--wish">
			<h2 class="info-card__title"><?php esc_html_e( 'Soovikino', 'tailpress' ); ?></h2>
			<p><?php esc_html_e( 'Sünnipäev, sõbrad, töö- või koolikaaslased? Korraldame privaatseansi Kärdla kinosaalis just teie valitud filmiga.', 'tailpress' ); ?></p>
			<a class="btn btn--primary" href="<?php echo esc_url( $link( 'soovikino' ) ); ?>"><?php esc_html_e( 'Esita soov', 'tailpress' ); ?></a>
		</article>
		<article class="info-card info-card--donate">
			<h2 class="info-card__title"><?php esc_html_e( 'Toeta rändkino', 'tailpress' ); ?></h2>
			<p><?php esc_html_e( 'Kogume annetusi rändkinotehnika serveri vahetuseks, et kino jõuaks ka edaspidi saare igasse nurka.', 'tailpress' ); ?></p>
			<a class="btn btn--outline" href="<?php echo esc_url( $link( 'projektid/annetuskampaania-randkinotehnika-serveri-vahetuseks-2024' ) ); ?>"><?php esc_html_e( 'Loe ja toeta', 'tailpress' ); ?></a>
		</article>
	</div>
</section>
