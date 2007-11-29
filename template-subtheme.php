<?php
// $Id$

/**
 * Return the path to the sub-theme directory or FALSE if there is no sub-theme.
 *
 * This function also fixes sub-themes that are mistakenly seen by PHPTemplate
 * as separate from their base theme.
 */
function path_to_subtheme() {
  $base_theme = 'zen';

  global $theme, $theme_key;
  static $theme_path;
  if (!isset($theme_path)) {
    if ($theme_key == $base_theme) {
      // This is not a sub-theme.
      $theme_path = FALSE;
    }
    else {
      // Extract current files from database.
      $themes = list_themes();

      // Sub-themes with their own page.tpl.php files are seen by PHPTemplate as
      // their own theme (seperate from Zen). So we need to re-connect those
      // sub-themes with the base Zen theme.
      if ($theme == $theme_key) {
        // Update database
        $parent_path = $themes[$base_theme]->filename;
        $subtheme_path = str_replace('page.tpl.php', 'style.css', $themes[$theme]->filename);
        db_query("UPDATE {system} SET description='%s', filename='%s' WHERE name='%s'", $parent_path, $subtheme_path, $theme);

        // Refresh Drupal internals.
        $theme = $base_theme;
        $themes = list_themes(TRUE);
      }

      $theme_path = dirname($themes[$theme_key]->filename);
    }
  }
  return $theme_path;
}

/*
 * This bit allows the sub-theme to have its own template.php.
 */
if (path_to_subtheme()) {
  // I'm being careful not to create variables in the global scope
  if (file_exists(path_to_subtheme() .'/template.php')) {
    include_once path_to_subtheme() .'/template.php';
  }
}

/*
 * These next functions allow sub-themes to have their own page.tpl.php,
 * node.tpl.php, node-type.tpl.php, etc.
 */
function _phptemplate_node($vars, $suggestions) {
  array_unshift($suggestions, 'node'); // Not sure why I need to do this.
  return _zen_default('node', $vars, $suggestions);
}

function _phptemplate_comment($vars, $suggestions) {
  array_unshift($suggestions, 'comment'); // Not sure why I need to do this.
  return _zen_default('comment', $vars, $suggestions);
}

function _phptemplate_page($vars, $suggestions) {
  return _zen_default('page', $vars, $suggestions);
}

function _phptemplate_block($vars, $suggestions) {
  return _zen_default('block', $vars, $suggestions);
}

function _phptemplate_box($vars, $suggestions) {
  return _zen_default('box', $vars, $suggestions);
}


/**
 * This is an exact copy of _phptemplate_default() with the addition of the
 * $theme_path and $parent_theme_path
 */
function _zen_default($hook, $variables, $suggestions = array(), $extension = '.tpl.php') {
  global $theme_engine;
  global $theme;
  global $theme_key;

  if ($theme_path = path_to_subtheme()) {
    $parent_theme_path = path_to_theme();
  }
  else {
    $theme_path = path_to_theme();
  }

  // Loop through any suggestions in FIFO order.
  $suggestions = array_reverse($suggestions);
  foreach ($suggestions as $suggestion) {
    if (!empty($suggestion) && file_exists($theme_path .'/'. $suggestion . $extension)) {
      $file = $theme_path .'/'. $suggestion . $extension;
      break;
    }
    elseif (isset($parent_theme_path) && !empty($suggestion) && file_exists($parent_theme_path .'/'. $suggestion . $extension)) {
      $file = $parent_theme_path .'/'. $suggestion . $extension;
      break;
    }
  }

  if (!isset($file)) {
    if (file_exists($theme_path ."/$hook$extension")) {
      $file = $theme_path ."/$hook$extension";
    }
    else {
      if (in_array($hook, array('node', 'block', 'box', 'comment'))) {
        $file = "themes/engines/$theme_engine/$hook$extension";
      }
      else {
        $variables['hook'] = $hook;
        watchdog('error', t('%engine.engine was instructed to override the %name theme function, but no valid template file was found.', array('%engine' => $theme_engine, '%name' => $hook)));
        $file = "themes/engines/$theme_engine/default$extension";
      }
    }
  }
  if (isset($file)) {
    return call_user_func('_'. $theme_engine .'_render', $file, $variables);
  }
}
