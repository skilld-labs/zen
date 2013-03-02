<?php
/**
 * @file
 * Overridden template for Panels Everywhere's navigation pane.
 *
 * This utilizes the following variables that are normally found in
 * page.tpl.php:
 * - $main_menu
 * - $secondary_menu
 * - $breadcrumb
 *
 * Additional items can be added via theme_preprocess_pane_messages().
 */
?>
<?php if (!empty($main_menu)): ?>
  <nav id="main-menu" role="navigation">
    <?php print $main_menu; ?>
  </nav>
<?php endif; ?>

<?php if (!empty($secondary_menu)): ?>
  <nav id="secondary-menu" role="navigation">
    <?php print $secondary_menu; ?>
  </nav>
<?php endif; ?>

<?php print $breadcrumb; ?>
