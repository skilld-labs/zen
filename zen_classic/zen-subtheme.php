<?php
// $Id$

/*
 * If your sub-theme has its own page.tpl.php file, copy this file to your
 * sub-theme's directory and include it using this line of code:
 *
 *   include_once('zen-subtheme.php');
 *
 * Sub-themes with their own page.tpl.php files are seen by PHPTemplate as their
 * own theme (seperate from Zen). So we need to re-connect those sub-themes with
 * the base Zen theme.
 */
global $theme, $theme_key;
if ($theme != 'zen' && $theme == $theme_key) {

  // Extract current files from database.
  $themes = list_themes();

  // Update database
  $parent_path = $themes['zen']->filename;
  $subtheme_path = str_replace('page.tpl.php', 'style.css', $themes[$theme]->filename);
  db_query("UPDATE {system} SET description='%s', filename='%s' WHERE name='%s'", $parent_path, $subtheme_path, $theme);

  // Refresh Drupal internals.
  $theme = 'zen';
  $themes = list_themes(TRUE);

  $zen_path = dirname($parent_path);
  if (file_exists($zen_path .'/template.php')) {
    include_once($zen_path .'/template.php');
  }
}
