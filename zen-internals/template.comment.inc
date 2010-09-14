<?php
// $Id$

/**
 * @file
 * Contains functions only needed when rendering comments.
 */

/**
 * Override or insert variables into the comment templates.
 *
 * @param $vars
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("comment" in this case.)
 */
function _zen_preprocess_comment(&$vars, $hook) {
  // If comment subjects are disabled, don't display them.
  if (variable_get('comment_subject_field_' . $vars['node']->type, 1) == 0) {
    $vars['title'] = '';
  }

  // Zebra striping.
  if ($vars['id'] == 1) {
    $vars['classes_array'][] = 'first';
  }
  if ($vars['id'] == $vars['node']->comment_count) {
    $vars['classes_array'][] = 'last';
  }
  $vars['classes_array'][] = $vars['zebra'];

  $vars['title_attributes_array']['class'][] = 'comment-title';
}
