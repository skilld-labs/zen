<?php
// $Id$

// Include base theme's settings file.
include_once path_to_theme() .'/theme-settings.php';

/**
 * Implementation of THEMEHOOK_settings() function.
 *
 * @param $saved_settings
 *   An array of saved settings for this theme.
 * @return
 *   A form array.
 */
function zen_classic_settings($saved_settings) {

  // The default values for the theme variables
  $defaults = array(
    'zen_classic_fixed' => 0,
    'zen_breadcrumb' => 'yes',
    'zen_breadcrumb_separator' => ' :: ',
    'zen_breadcrumb_home' => 1,
    'zen_breadcrumb_trailing' => 0,
  );

  // Merge the saved variables and their default values
  $settings = array_merge($defaults, $saved_settings);

  /*
   * Create the form using Form API
   */
  $form['zen_classic_fixed'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Use fixed width for theme'),
    '#default_value' => $settings['zen_classic_fixed'],
    '#description'   => 'The theme should be centered and fixed at 960 pixels wide.',
  );

  // Add the base theme's settings.
  $form += zen_settings($saved_settings, $defaults);

  // Return the form
  return $form;
}
