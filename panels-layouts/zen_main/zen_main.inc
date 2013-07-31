<?php

$plugin = array(
  'title' => t('Zen’s default Panels layout'),
  'category' => t('Columns: 3'),
  'icon' => 'icon.png',
  'theme' => 'zen_main',
  'regions' => array(
    'navigation' => t('Navigation bar'),
    'highlighted' => t('Highlighted'),
    'content' => t('Content'),
    'sidebar_first' => t('First sidebar'),
    'sidebar_second' => t('Second sidebar'),
  ),
);

/**
 * Preprocess variables for zen-main.tpl.php.
 *
 * Because of Panels' inside-out rendering method we can only detect empty
 * sidebars at this stage. We then set a static variable so that we can add
 * the appropriate body classes later.
 */
function zen_preprocess_zen_main(&$variables) {
  // Put any body classes we want into this static variable.
  $classes = &drupal_static('zen_panels_classes_array', array());
  if (!empty($variables['content']['sidebar_first']) && !empty($variables['content']['sidebar_second'])) {
    $classes[] = 'two-sidebars';
  }
  elseif (!empty($variables['content']['sidebar_first'])) {
    $classes[] = 'one-sidebar sidebar-first';
  }
  elseif (!empty($variables['content']['sidebar_second'])) {
    $classes[] = 'one-sidebar sidebar-second';
  }
  else {
    $classes[] = 'no-sidebars';
  }
}
