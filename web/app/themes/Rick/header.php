<?php
$header_style = function_exists('rick_get_header_setting') ? rick_get_header_setting('header_style', 'header-brown') : 'header-brown';
$cta_enable   = function_exists('rick_get_header_setting') ? rick_get_header_setting('header_cta_enable', '1') : '1';
$cta_text     = function_exists('rick_get_header_setting') ? rick_get_header_setting('header_cta_text', '🍪 Pepernoot Beoordelen') : '🍪 Pepernoot Beoordelen';
$cta_url      = function_exists('rick_get_header_setting') ? rick_get_header_setting('header_cta_url', '/pepernoot-registreren/') : '/pepernoot-registreren/';
$brand_name   = function_exists('rick_get_header_setting') ? rick_get_header_setting('header_brand_name', get_bloginfo('name') ?: 'Rick Recepten') : (get_bloginfo('name') ?: 'Rick Recepten');
$brand_icon   = function_exists('rick_get_header_setting') ? rick_get_header_setting('header_brand_icon', '👨‍🍳') : '👨‍🍳';

// Relatieve link naar absolute URL converteren
if ( $cta_url && strpos($cta_url, 'http') !== 0 ) {
    $cta_url = home_url($cta_url);
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header floating-header <?php echo esc_attr($header_style); ?>">
	<div class="site-header__pill">
		<a class="site-brand" href="<?php echo esc_url(home_url('/')); ?>">
			<span class="site-brand__name"><?php echo esc_html($brand_name); ?></span>
		</a>

		<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" data-menu-toggle aria-label="<?php esc_attr_e('Menu openen of sluiten', 'rick'); ?>">
			<span class="menu-toggle__icon" aria-hidden="true"></span>
			<span class="menu-toggle__label sr-only"><?php esc_html_e('Menu', 'rick'); ?></span>
		</button>

		<nav class="site-nav" id="primary-navigation" aria-label="Hoofdnavigatie" data-primary-nav>
			<?php
			wp_nav_menu(array(
				'theme_location' => 'primary',
				'container'      => false,
				'fallback_cb'    => 'rick_primary_menu_fallback',
				'menu_class'     => 'primary-menu',
				'depth'          => 1,
			));
			?>

			<?php if ( $cta_enable && ! empty($cta_text) ) : ?>
				<div class="site-nav__mobile-cta">
					<a href="<?php echo esc_url($cta_url); ?>" class="header-cta-btn">
						<?php echo esc_html($cta_text); ?>
					</a>
				</div>
			<?php endif; ?>
		</nav>

		<?php if ( $cta_enable && ! empty($cta_text) ) : ?>
			<div class="site-header__cta">
				<a href="<?php echo esc_url($cta_url); ?>" class="header-cta-btn">
					<?php echo esc_html($cta_text); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</header>
<main>
