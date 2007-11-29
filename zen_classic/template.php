<?php
// $Id$

/**
 * @file
 *
 * The Zen theme allows its sub-themes to have their own template.php files. The
 * only restriction with these files is that they cannot redefine any of the
 * functions that are already defined in Zen's main template.php file.
 *
 * Also remember that the "main" theme is still Zen, so your theme functions
 * should be named as such:
 *  theme_block()  becomes  zen_block()
 *  theme_feed_icon() becomes zen_feed_icon() as well
 *
 * For a sub-theme to define its own regions, use the function name
 *   THEMENAME_regions()
 * where THEMENAME is replaced with the name of your sub-theme (with dashes
 * replaced with underscores). For example, the zen_classic theme would define
 * a zen_classic_regions() function.
 *
 * For a sub-theme to add its own variables, use the function name
 *   zen_variables($hook, $vars)
 */


/*
 * Initialize theme settings
 */
include_once 'theme-settings-init.php';


/*
 * Sub-themes with their own page.tpl.php files are seen by PHPTemplate as their
 * own theme (seperate from Zen). So we need to re-connect those sub-themes
 * with the main Zen theme.
 */
include_once './'. drupal_get_path('theme', 'zen') .'/template.php';


/**
 * Declare the available regions implemented by this theme.
 *
 * @return
 *   An array of regions.
 */
function zen_classic_regions() {
  return array(
    'left' => t('left sidebar'),
    'right' => t('right sidebar'),
    'content_top' => t('content top'),
    'content_bottom' => t('content bottom'),
    'header' => t('header'),
    'footer' => t('footer'),
  );
}

/**
 * Intercept template variables
 *
 * @param $hook
 *   The name of the theme function being executed
 * @param $vars
 *   A sequential array of variables passed to the theme function.
 */
function zen_variables($hook, $vars) {
  $vars['subtheme_directory'] = path_to_subtheme();

  switch ($hook) {
    case 'page':
      // Add main Zen styles.
      drupal_add_css($vars['directory'] .'/tabs.css', 'theme', 'all');
      // Then add styles for this sub-theme.
      drupal_add_css($vars['subtheme_directory'] .'/layout.css', 'theme', 'all');
      drupal_add_css($vars['subtheme_directory'] .'/print.css', 'theme', 'print');
      drupal_add_css($vars['subtheme_directory'] .'/zen-classic.css', 'theme', 'all');
      drupal_add_css($vars['subtheme_directory'] .'/icons.css', 'theme', 'all');
      // Optionally add the fixed width CSS file.
      if (theme_get_setting('zen_classic_fixed')) {
        drupal_add_css($vars['subtheme_directory'] .'/zen-fixed.css', 'theme', 'all');
      }
      $vars['css'] = drupal_add_css();
      $vars['styles'] = drupal_get_css();
  }
  return $vars;
}
