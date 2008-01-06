<?php
// $Id$

/**
 * @file
 * File which contains theme overrides for the Zen theme.
 *
 * ABOUT
 *
 * The template.php file is one of the most useful files when creating or
 * modifying Drupal themes. You can add new regions for block content, modify or
 * override Drupal's theme functions, intercept or make additional variables
 * available to your theme, and create custom PHP logic. For more information,
 * please visit the Theme Developer's Guide on Drupal.org:
 * http://drupal.org/theme-guide
 */


/*
 * To make this file easier to read, we split up the code into managable parts.
 * Theme developers are likely to only be interested in functions that are in
 * this main template.php file.
 */

// Sub-theme support
include_once 'template-subtheme.php';

// Initialize theme settings
include_once 'theme-settings-init.php';

// Tabs and menu functions
include_once 'template-menus.php';


/**
 * Declare the available regions implemented by this theme.
 *
 * Regions are areas in your theme where you can place blocks. The default
 * regions used in themes are "left sidebar", "right sidebar", "header", and
 * "footer", although you can create as many regions as you want. Once declared,
 * they are made available to the page.tpl.php file as a variable. For instance,
 * use <?php print $header ?> for the placement of the "header" region in
 * page.tpl.php.
 *
 * By going to the administer > site building > blocks page you can choose
 * which regions various blocks should be placed. New regions you define here
 * will automatically show up in the drop-down list by their human readable name.
 *
 * @return
 *   An array of regions. The first array element will be used as the default
 *   region for themes. Each array element takes the format:
 *   variable_name => t('human readable name')
 */
function zen_regions() {
  // Allow a sub-theme to add/alter variables
  global $theme_key;
  if ($theme_key != 'zen') {
    $function = str_replace('-', '_', $theme_key) .'_regions';
    if (function_exists($function)) {
      return $function();
    }
  }

  return array(
    'left' => t('left sidebar'),
    'right' => t('right sidebar'),
    'navbar' => t('navigation bar'),
    'content_top' => t('content top'),
    'content_bottom' => t('content bottom'),
    'header' => t('header'),
    'footer' => t('footer'),
    'closure_region' => t('closure'),
  );
}


/*
 * OVERRIDING THEME FUNCTIONS
 *
 * The Drupal theme system uses special theme functions to generate HTML output
 * automatically. Often we wish to customize this HTML output. To do this, we
 * have to override the theme function. You have to first find the theme
 * function that generates the output, and then "catch" it and modify it here.
 * The easiest way to do it is to copy the original function in its entirety and
 * paste it here, changing the prefix from theme_ to phptemplate_ or zen_. For
 * example:
 *
 *   original: theme_breadcrumb()
 *   theme override: zen_breadcrumb()
 *
 * See the following example. In this function, we want to change all of the
 * breadcrumb separator characters from >> to a custom string.
 */


/**
 * Return a themed breadcrumb trail.
 *
 * @param $breadcrumb
 *   An array containing the breadcrumb links.
 * @return
 *   A string containing the breadcrumb output.
 */
function phptemplate_breadcrumb($breadcrumb) {
  $show_breadcrumb = theme_get_setting('zen_breadcrumb');
  $show_breadcrumb_home = theme_get_setting('zen_breadcrumb_home');
  $breadcrumb_separator = theme_get_setting('zen_breadcrumb_separator');
  $trailing_separator = theme_get_setting('zen_breadcrumb_trailing') ? $breadcrumb_separator : '';

  // Determine if we are to display the breadcrumb
  if ($show_breadcrumb == 'yes' || $show_breadcrumb == 'admin' && arg(0) == 'admin') {
    if (!$show_breadcrumb_home) {
      // Optionally get rid of the homepage link
      array_shift($breadcrumb);
    }
    if (!empty($breadcrumb)) {
      // Return the breadcrumb with separators
      return '<div class="breadcrumb">'. implode($breadcrumb_separator, $breadcrumb) ."$trailing_separator</div>";
    }
  }
  // Otherwise, return an empty string
  return '';
}


/*
 * CREATE OR MODIFY VARIABLES FOR YOUR THEME
 *
 * The most powerful function available to themers is _phptemplate_variables().
 * It allows you to pass newly created variables to different template (tpl.php)
 * files in your theme. Or even unset ones you don't want to use.
 *
 * It works by switching on the hook, or name of the theme function, such as:
 *   - page
 *   - node
 *   - comment
 *   - block
 *
 * By switching on this hook you can send different variables to page.tpl.php
 * file, node.tpl.php (and any other derivative node template file, like
 * node-forum.tpl.php), comment.tpl.php, and block.tpl.php.
 */


/**
 * Intercept template variables
 *
 * @param $hook
 *   The name of the theme function being executed (name of the .tpl.php file)
 * @param $vars
 *   A copy of the array containing the variables for the hook.
 * @return
 *   The array containing additional variables to merge with $vars.
 */
function _phptemplate_variables($hook, $vars = array()) {
  // Get the currently logged in user
  global $user, $theme_key;

  // Set a new $is_admin variable. This is determined by looking at the
  // currently logged in user and seeing if they are in the role 'admin'. The
  // 'admin' role will need to have been created manually for this to work this
  // variable is available to all templates.
  $vars['is_admin'] = in_array('admin', $user->roles);

  switch ($hook) {
    case 'page':
      global $theme;

      // These next lines add additional CSS files and redefine
      // the $css and $styles variables available to your page template
      if ($theme == $theme_key) { // If we're in the main theme
        // Load the stylesheet for a liquid layout
        if (theme_get_setting('zen_layout') == 'border-politics-liquid') {
          drupal_add_css($vars['directory'] .'/layout-liquid.css', 'theme', 'all');
        }
        // Or load the stylesheet for a fixed width layout
        else {
          drupal_add_css($vars['directory'] .'/layout-fixed.css', 'theme', 'all');
        }
        drupal_add_css($vars['directory'] .'/html-elements.css', 'theme', 'all');
        drupal_add_css($vars['directory'] .'/tabs.css', 'theme', 'all');
        drupal_add_css($vars['directory'] .'/zen.css', 'theme', 'all');
        // Avoid IE5 bug that always loads @import print stylesheets
        $vars['head'] = zen_add_print_css($vars['directory'] .'/print.css');
      }
      // Optionally add the wireframes style.
      if (theme_get_setting('zen_wireframes')) {
        drupal_add_css($vars['directory'] .'/wireframes.css', 'theme', 'all');
      }
      $vars['css'] = drupal_add_css();
      $vars['styles'] = drupal_get_css();

      // Send a new variable, $logged_in, to page.tpl.php to tell us if the
      // current user is logged in or out. An anonymous user has a user id of 0.
      $vars['logged_in'] = ($user->uid > 0) ? TRUE : FALSE;

      // Classes for body element. Allows advanced theming based on context
      // (home page, node of certain type, etc.)
      $body_classes = array();
      $body_classes[] = ($vars['is_front']) ? 'front' : 'not-front';
      $body_classes[] = ($vars['logged_in']) ? 'logged-in' : 'not-logged-in';
      if ($vars['node']->type) {
        // If on an individual node page, put the node type in the body classes
        $body_classes[] = 'node-type-'. $vars['node']->type;
      }
      if ($vars['sidebar_left'] && $vars['sidebar_right']) {
        $body_classes[] = 'two-sidebars';
      }
      elseif ($vars['sidebar_left']) {
        $body_classes[] = 'one-sidebar sidebar-left';
      }
      elseif ($vars['sidebar_right']) {
        $body_classes[] = 'one-sidebar sidebar-right';
      }
      else {
        $body_classes[] = 'no-sidebars';
      }
      if (!$vars['is_front']) {
        // Add unique classes for each page and website section
        // First, remove base path and any query string.
        global $base_path;
        list(,$path) = explode($base_path, $_SERVER['REQUEST_URI'], 2);
        // If clean URLs are off, strip remainder of query string.
        list($path,) = explode('&', $path, 2);
        // Strip query string.
        list($path,) = explode('?', $path, 2);
        $path = rtrim($path, '/');
        // Construct the id name from the path, replacing slashes with dashes.
        $full_path = str_replace('/', '-', $path);
        // Construct the class name from the first part of the path only.
        list($section,) = explode('/', $path, 2);
        $body_classes[] = zen_id_safe('page-'. $full_path);
        $body_classes[] = zen_id_safe('section-'. $section);
      }
      $vars['body_classes'] = implode(' ', $body_classes); // implode with spaces

      // Allow a sub-theme to add/alter variables
      if (function_exists($theme_key .'_preprocess_page')) {
        $function = $theme_key .'_preprocess_page';
        $function($vars);
      }
      elseif (function_exists('phptemplate_preprocess_page')) {
        phptemplate_preprocess_page($vars);
      }

      break;

    case 'node':
      // Special classes for nodes
      $node_classes = array();
      if ($vars['sticky']) {
        $node_classes[] = 'sticky';
      }
      if (!$vars['node']->status) {
        $node_classes[] = 'node-unpublished';
      }
      if ($vars['node']->uid && $vars['node']->uid == $user->uid) {
        // Node is authored by current user
        $node_classes[] = 'node-mine';
      }
      if ($vars['teaser']) {
        // Node is displayed as teaser
        $node_classes[] = 'node-teaser';
      }
      // Class for node type: "node-type-page", "node-type-story", "node-type-my-custom-type", etc.
      $node_classes[] = 'node-type-'. $vars['node']->type;
      $vars['node_classes'] = implode(' ', $node_classes); // implode with spaces

      // Allow a sub-theme to add/alter variables
      if (function_exists($theme_key .'_preprocess_node')) {
        $function = $theme_key .'_preprocess_node';
        $function($vars);
      }
      elseif (function_exists('phptemplate_preprocess_node')) {
        phptemplate_preprocess_node($vars);
      }

      break;

    case 'comment':
      // We load the node object that the current comment is attached to
      $node = node_load($vars['comment']->nid);
      // If the author of this comment is equal to the author of the node, we
      // set a variable so we can theme this comment uniquely.
      $vars['author_comment'] = $vars['comment']->uid == $node->uid ? TRUE : FALSE;

      $comment_classes = array();

      // Odd/even handling
      static $comment_odd = TRUE;
      $comment_classes[] = $comment_odd ? 'odd' : 'even';
      $comment_odd = !$comment_odd;

      if ($vars['comment']->status == COMMENT_NOT_PUBLISHED) {
        $comment_classes[] = 'comment-unpublished';
      }
      if ($vars['author_comment']) {
        // Comment is by the node author
        $comment_classes[] = 'comment-by-author';
      }
      if ($vars['comment']->uid == 0) {
        // Comment is by an anonymous user
        $comment_classes[] = 'comment-by-anon';
      }
      if ($user->uid && $vars['comment']->uid == $user->uid) {
        // Comment was posted by current user
        $comment_classes[] = 'comment-mine';
      }
      $vars['comment_classes'] = implode(' ', $comment_classes);

      // If comment subjects are disabled, don't display 'em
      if (variable_get('comment_subject_field', 1) == 0) {
        $vars['title'] = '';
      }

      // Allow a sub-theme to add/alter variables
      if (function_exists($theme_key .'_preprocess_comment')) {
        $function = $theme_key .'_preprocess_comment';
        $function($vars);
      }
      elseif (function_exists('phptemplate_preprocess_comment')) {
        phptemplate_preprocess_comment($vars);
      }

      break;
  }

  // The following is a deprecated function included for backwards compatibility
  // with Zen 5.x-0.8 and earlier. New sub-themes should not use this function.
  if (function_exists('zen_variables')) {
    $vars = zen_variables($hook, $vars);
  }

  _zen_hook($hook); // Add support for sub-theme template files

  return $vars;
}

/**
 * Converts a string to a suitable html ID attribute.
 *
 * - Preceeds initial numeric with 'n' character.
 * - Replaces space and underscore with dash.
 * - Converts entire string to lowercase.
 * - Works for classes too!
 *
 * @param string $string
 *   The string
 * @return
 *   The converted string
 */
function zen_id_safe($string) {
  if (is_numeric($string{0})) {
    // If the first character is numeric, add 'n' in front
    $string = 'n'. $string;
  }
  return strtolower(preg_replace('/[^a-zA-Z0-9-]+/', '-', $string));
}

/**
 * Adds a print stylesheet to the page's $head variable.
 *
 * This is a work-around for a serious bug in IE5 in which it loads print
 * stylesheets for screen display when using an @import method, Drupal's default
 * method when using drupal_add_css().
 *
 * @param string $url
 *   The URL of the print stylesheet
 * @return
 *   All the rendered links for the $head variable
 */
function zen_add_print_css($url) {
  global $base_path;
  return drupal_set_html_head(
    '<link'.
    drupal_attributes(
      array(
        'rel' => 'stylesheet',
        'href' => $base_path . $url,
        'type' => 'text/css',
        'media' => 'print',
      )
    ) ." />\n"
  );
}
