<?php
/**
 * @file
 * Overridden template for Panels Everywhere's navigation pane.
 *
 * This utilizes the following variables thata re normally found in
 * page.tpl.php:
 * - $logo
 * - $front_page
 * - $site_name
 * - $site_slogan
 *
 * Additional items can be added via theme_preprocess_pane_header().
 */
?>
<?php if ($logo): ?>
  <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" class="header--logo" id="logo"><img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" class="header--logo-image" /></a>
<?php endif; ?>

<?php if ($site_name || $site_slogan): ?>
  <div class="header--name-and-slogan" id="name-and-slogan">
    <?php if ($site_name): ?>
      <h1 class="header--site-name" id="site-name">
        <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" class="header--site-link" rel="home"><span><?php print $site_name; ?></span></a>
      </h1>
    <?php endif; ?>

    <?php if ($site_slogan): ?>
      <h2 class="header--site-slogan" id="site-slogan"><?php print $site_slogan; ?></h2>
    <?php endif; ?>
  </div>
<?php endif; ?>
