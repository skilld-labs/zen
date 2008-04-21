<?php
// $Id$

if (is_null(theme_get_setting('zen_block_editing'))) {

  /*
   * init_theme() loads the base theme's template.php before the sub-themes'
   * template.php. So we need to ensure that the sub-themes' theme settings init
   * are loaded first.
   */
  if ($theme != 'zen') {
    global $theme;
    $themes = list_themes();
    $ancestor = $theme;
    while ($ancestor && isset($themes[$ancestor]->base_theme)) {
      $file = dirname($themes[$ancestor]->filename) .'/template.php';
      if (file_exists($file)) {
        include_once "./$file";
      }
      $ancestor = $themes[$ancestor]->base_theme;
    }
  }

  if ($theme == 'zen' || is_null(theme_get_setting('zen_block_editing'))) {
    global $theme_key;

    /*
     * The default values for the theme variables. Make sure $defaults exactly
     * matches the $defaults in the theme-settings.php file.
     */
    $defaults = array(
      'zen_block_editing' => 1,
      'zen_breadcrumb' => 'yes',
      'zen_breadcrumb_separator' => ' › ',
      'zen_breadcrumb_home' => 1,
      'zen_breadcrumb_trailing' => 1,
      'zen_layout' => 'border-politics-liquid',
      'zen_wireframes' => 0,
    );

    // Get default theme settings.
    $settings = theme_get_settings($theme_key);
    // Don't save the toggle_node_info_ variables.
    if (module_exists('node')) {
      foreach (node_get_types() as $type => $name) {
        unset($settings['toggle_node_info_' . $type]);
      }
    }
    // Save default theme settings.
    variable_set(
      str_replace('/', '_', 'theme_'. $theme_key .'_settings'),
      array_merge($defaults, $settings)
    );
    // Force refresh of Drupal internals.
    theme_get_setting('', TRUE);
  }
}
