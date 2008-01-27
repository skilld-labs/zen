<?php
// $Id$

/*
 * Allow the sub-theme to include its parent's template.php files.
 */
if (path_to_zentheme()) {
  // Be careful not to create variables in the global scope
  if (file_exists(path_to_subtheme() .'/template.php')) {
    include_once path_to_subtheme() .'/template.php';
  }
}


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

      $theme_path = dirname($themes[$theme_key]->filename);
    }
  }
  return $theme_path;
}

/**
 * Allow sub-themes to have their own .tpl.php template files.
 *
 * This is an exact copy of _phptemplate_default() with the addition of the
 * $theme_path and $parent_theme_path
 */
function _zen_default($hook, $variables, $suggestions = array(), $extension = '.tpl.php') {
  global $theme_engine;

  if ($theme_path = path_to_subtheme()) {
    $parent_theme_path = path_to_theme();
  }
  else {
    $theme_path = path_to_theme();
  }

  // Loop through any suggestions in FIFO order.
  $suggestions = array_reverse($suggestions);
  foreach ($suggestions as $suggestion) {
    if (!empty($suggestion)) {
      if (file_exists($theme_path .'/'. $suggestion . $extension)) {
        $file = $theme_path .'/'. $suggestion . $extension;
        break;
      }
      elseif (!empty($parent_theme_path) && file_exists($parent_theme_path .'/'. $suggestion . $extension)) {
        $file = $parent_theme_path .'/'. $suggestion . $extension;
        break;
      }
    }
  }

  if (!isset($file)) {
    if (file_exists($theme_path ."/$hook$extension")) {
      $file = $theme_path ."/$hook$extension";
    }
    elseif (!empty($parent_theme_path) && file_exists($parent_theme_path ."/$hook$extension")) {
      $file = $parent_theme_path ."/$hook$extension";
    }
    else {
      if (in_array($hook, array('node', 'block', 'box', 'comment'))) {
        $file = path_to_engine() .'/'. $hook . $extension;
      }
      else {
        $variables['hook'] = $hook;
        watchdog('error', t('%engine.engine was instructed to override the %name theme function, but no valid template file was found.', array('%engine' => $theme_engine, '%name' => $hook)));
        $file = path_to_engine() .'/default'. $extension;
      }
    }
  }

  if (isset($file)) {
    return call_user_func('_'. $theme_engine .'_render', $file, $variables);
  }
}

/**
 * To allow themes to have their own template files, we have to override
 * _phptemplate_default() by dynamically defining _phptemplate_HOOK().
 */
function _zen_hook($hook) {
  if (!function_exists("_phptemplate_$hook")) {
  	// Only create a function if $hook contains letters and underscores.
  	if (!preg_match('/\W/', $hook)) {
      $declaration = <<<EOD
        function _phptemplate_$hook(\$vars, \$suggestions = array()) {
          return _zen_default('$hook', \$vars, \$suggestions);
        }
EOD;
      eval($declaration);
    }
    else {
      // Since we can't create an override function, we simply add a template
      // suggestion that contains the sub-theme directory.
  	  $vars['template_file'] = "$theme_key/$hook";
  	}
  }
}

/*
 * Rather than let _zen_hook() dynamically, and slowly, define override
 * functions for functions we will definitely need, we define them explicitly.
 */
function _phptemplate_page($vars, $suggestions) {
  return _zen_default('page', $vars, $suggestions);
}

function _phptemplate_node($vars, $suggestions) {
  return _zen_default('node', $vars, $suggestions);
}

function _phptemplate_comment($vars, $suggestions) {
  return _zen_default('comment', $vars, $suggestions);
}

function _phptemplate_block($vars, $suggestions) {
  return _zen_default('block', $vars, $suggestions);
}

function _phptemplate_box($vars, $suggestions) {
  return _zen_default('box', $vars, $suggestions);
}
