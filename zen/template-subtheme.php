<?php
// $Id$

/*
 * Allow the sub-theme to include its parent's template.php files.
 */
if (path_to_zentheme()) {
  global $theme;

  $themes = list_themes();
  $parent = $theme;
  while (!empty($themes[$parent]->base_theme)) {
    $parent = $themes[$parent]->base_theme;
    $parent_path = dirname($themes[$parent]->filename);
    // Be careful not to create variables in the global scope
    if (file_exists($parent_path .'/template.php')) {
      include_once $parent_path .'/template.php';
    }
  }
}


/**
 * Return the path to the main zen theme directory or FALSE if this is the main theme.
 */
function path_to_zentheme() {
  static $theme_path;
  if (!isset($theme_path)) {
    global $theme;
    if ($theme == 'zen') {
      $theme_path = FALSE;
    }
    else {
      $theme_path = drupal_get_path('theme', 'zen');
    }
  }
  return $theme_path;
}
