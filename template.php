<?php

/**
 * About this file
 *
 * The template.php file is one of the most useful files when creating templates. It allows you to place
 * all of your custom theme logic, plus specific Drupal related theme functions into one place. You should
 * place any code in here that directly affects your theme -- changing menu structures, styling content,
 * theming pages differently based on content, and so forth.
 *
 */


/**
 * Overriding theme functions
 *
 * Overriding theme functions in Drupal is the best way to alter the way Drupal themes various sections of
 * a website. Scattered throughout Drupal core and also used in downloaded, contribute modules, is the use 
 * theme_some_function(). 
 * 
 * This is a special type of function in Drupal as it can be overridden by the theme, by placing a copy of
 * that function in this file. Copy the entire contents of the function, then simply rename it using the name
 * of the theme the template.php lives in.
 *  
 *   original:  theme_breadcrumb() 
 *   theme override:   zen_breadcrumb()
 *
 * See the following example. In this theme, we want to change all of the breacrumb separator links from 
 * a >> to an arrow -->
 */

 /**
  * Return a themed breadcrumb trail.
  *
  * @param $breadcrumb
  *   An array containing the breadcrumb links.
  * @return a string containing the breadcrumb output.
  */
 function zen_breadcrumb($breadcrumb) {
   if (!empty($breadcrumb)) {
     return '<div class="breadcrumb">'. implode(' &#8594; ', $breadcrumb) .'</div>';
   }
 }
 
 
/**
 * Creating new regions
 *
 * If you wish to create new regions for your theme to place block content into, you want to override this
 * theme function. By declaring the variable name and giving it a human readable name, you create new regions
 * for your theme that are then available on the administer > site building > blocks page. On this page you can 
 * then select which regions various blocks should be placed.
 */
 
/**
 * Declare the available regions implemented by this engine.
 *
 * @return
 *  An array of regions.  The first array element will be used as the default region for themes.
 */
function zen_regions() {
  return array(
       'left' => t('left sidebar'),
       'right' => t('right sidebar'),
       'content_top' => t('content top'),
       'content_bottom' => t('content bottom'),
       'header' => t('header'),
       'footer' => t('footer')
  );
}


/** 
 * Creating new variables for your theme
 *
 * The most powerful function available to themers is the _phptemplate_variables() function. It allows you
 * to pass newly created variables to different template files in your theme. Or even unset ones you don't want
 * to use.
 *
 * It works by switching on the hook, which can be:
 *  - page
 *  - node
 *  - comment
 *  - block
 *
 * By switching on this hook you can send different variables to your main page.tpl.php file, your node.tpl.php
 * (and any other derivative node template file, like node-page.tpl.php), comment.tpl.php, and block.tpl.php
 *
 */

/**
 * Intercept template variables
 *
 * @param $hook
 *   The name of the theme function being executed
 * @param $vars
 *   A sequential array of variables passed to the theme function.
 */

/**
 * Note this is just an example so it is commented out
 *
 *
 
function _phptemplate_variables($hook, $vars = array()) {
  
  // variable available to all template files
  $vars['img_path'] = my_custom_function_get_image_path_to_my_image_server();
  
  switch ($hook) {
    case 'page':
      // if we're on the homepage we want to show a special graphic
      if ($vars['is_front']) {
        $vars['home_bannder'] = 'some_image.jpg';
      }
      
      break;
    
    case 'node':
      // get the currently logged in user
      global $user;

      // set a new $is_admin variable
      // this is determined by looking at the currently logged in user and seeing if they are in the role 'admin'
      $vars['is_admin'] = in_array('admin', $user->roles);
      break;
      
    case 'comment':
      // we load the node object that the current comment is attached to
      $node_author = node_load($vars['comment']->nid);
      // if the author of this comment is equal to the author of the node, we set a variable
      // then in our theme we can theme this comment differently to stand out
      $vars['author_comment'] = $vars['comment']->uid == $node_author->uid ? TRUE : FALSE;
      break;
  }
  
  return $vars;
}

 *
 */
 