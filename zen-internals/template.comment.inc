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
  // Add an "unpublished" flag.
  $vars['unpublished'] = ($vars['comment']->status == COMMENT_NOT_PUBLISHED);

  // In Drupal 7, $date has been renamed to $created.
  $vars['created'] = $vars['date'];

  // If comment subjects are disabled, don't display them.
  if (variable_get('comment_subject_field_' . $vars['node']->type, 1) == 0) {
    $vars['title'] = '';
  }

  // Special classes for comments.
  if ($vars['comment']->new) {
    $vars['classes_array'][] = 'comment-new';
  }
  $vars['classes_array'][] = $vars['status'];
  $vars['classes_array'][] = $vars['zebra'];
  if ($vars['id'] == 1) {
    $vars['classes_array'][] = 'first';
  }
  if ($vars['id'] == $vars['node']->comment_count) {
    $vars['classes_array'][] = 'last';
  }
  if ($vars['comment']->uid == 0) {
    // Comment is by an anonymous user.
    $vars['classes_array'][] = 'comment-by-anon';
  }
  else {
    if ($vars['comment']->uid == $vars['node']->uid) {
      // Comment is by the node author.
      $vars['classes_array'][] = 'comment-by-author';
    }
    if ($vars['comment']->uid == $GLOBALS['user']->uid) {
      // Comment was posted by current user.
      $vars['classes_array'][] = 'comment-mine';
    }
  }
}
