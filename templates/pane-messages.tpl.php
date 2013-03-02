<?php
/**
 * @file
 * Overridden template for Panels Everywhere's messages pane.
 *
 * This utilizes the following variables thata re normally found in
 * page.tpl.php:
 * - $tabs
 * - $messages
 * - $help
 * - $action_links
 *
 * Additional items can be added via theme_preprocess_pane_messages().
 */
?>

<?php print $messages; ?>
<?php print render($tabs); ?>
<?php print $help; ?>

<?php if ($action_links): ?>
  <ul class="action-links"><?php print render($action_links); ?></ul>
<?php endif; ?>
