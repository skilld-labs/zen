<?php
// $Id$

/**
 * Implementation of THEMEHOOK_settings() function.
 *
 * @param $saved_settings
 *   array An array of saved settings for this theme.
 * @return
 *   array A form array.
 */
function phptemplate_settings($saved_settings) {

  // The default values for the theme variables
  $defaults = array(
    'zen_classic_fixed' => 0,
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

  // Return the form
  return $form;
}
