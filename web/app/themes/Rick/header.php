<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
	<div class="site-header__inner">
		<a class="site-brand" href="<?php echo esc_url(home_url('/')); ?>">
			<?php bloginfo('name'); ?>
		</a>
		<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" data-menu-toggle>
			<span class="menu-toggle__icon" aria-hidden="true"></span>
			<span class="menu-toggle__label"><?php esc_html_e('Menu', 'rick'); ?></span>
		</button>
		<nav class="site-nav" id="primary-navigation" aria-label="Hoofdnavigatie" data-primary-nav>
			<?php
			wp_nav_menu(array(
					'theme_location' => 'primary',
					'container' => false,
					'fallback_cb' => 'rick_primary_menu_fallback',
					'menu_class' => 'primary-menu',
					'depth' => 1,
			));
			?>
		</nav>
	</div>
</header>
<main>
