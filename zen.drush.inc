<?php

/**
 * Implementation of hook_drush_command().
 */
function zen_drush_command() {
  $items = array();

  $items['zen'] = array(
    'description' => 'Set up a Zen starter kit.',
    'arguments' => array(
      'name' => 'A name for your theme.',
    ),
    'options' => array(
      'machine_name' => '[a-z, 0-9] A machine-readable name for your theme.',
    ),
    'examples' => array(
      'drush zen "My theme name"',
    ),
  );

  return $items;
}

/**
 * Command: Set up a Cassette Zen starter kit.
 */
function drush_zen($name = 'My theme') {

  $machine_name = drush_get_option('machine_name');
  $machine_name = ($machine_name) ? $machine_name : preg_replace('/[^a-z0-9]+/', '', strtolower($name));

  $zen_path = drush_locate_root() . '/' . drupal_get_path('theme', 'zen');

  // From Zen's location, we move up one directory and construct the path where
  // our sub theme will be created.
  $_zen_path = explode('/', $zen_path);
  array_pop($_zen_path);
  $subtheme_path = implode('/', $_zen_path) . '/' . $machine_name;

  // Make a fresh copy of the original starter kit.
  drush_op('zen_copy', "$zen_path/STARTERKIT", $subtheme_path);

  // Rename the info file and fill in the theme name.
  drush_op('rename', "$subtheme_path/STARTERKIT.info.txt", "$subtheme_path/$machine_name.info");
  drush_op('zen_file_str_replace', "$subtheme_path/$machine_name.info", '= Zen Sub-theme Starter Kit', "= $name");

  // Replace all occurences of 'STARTERKIT' with the machine name of our sub theme.
  drush_op('zen_file_str_replace', "$subtheme_path/theme-settings.php", 'STARTERKIT', $machine_name);
  drush_op('zen_file_str_replace', "$subtheme_path/template.php", 'STARTERKIT', $machine_name);

  // Notify user of the newly created theme.
  drush_print(dt('Starter kit for !name created in !path.',
    array(
      '!name' => $name,
      '!path' => $subtheme_path,
    )
  ));
}

/**
 * Internal helper: Copy a directory recursively.
 */
function zen_copy($source_dir, $target_dir, $ignore = '/^(\.(\.)?|CVS|SVN|\.DS_Store)$/') {
  if (!is_dir($source_dir)) {
    drush_die(dt('The directory !directory was not found.', array('!directory' => $source_dir)));
  }
  $dir = opendir($source_dir);
  @mkdir($target_dir);
  while($file = readdir($dir)) {
    if (!preg_match($ignore, $file)) {
      if (is_dir("$source_dir/$file")) {
        zen_copy("$source_dir/$file", "$target_dir/$file", $ignore);
      }
      else {
        copy("$source_dir/$file", "$target_dir/$file");
      }
    }
  }
  closedir($dir);
}

/**
 * Internal helper: Replace strings in a file.
 */
function zen_file_str_replace($file_path, $find, $replace) {
  $file_contents = file_get_contents($file_path);
  $file_contents = str_replace($find, $replace, $file_contents);
  file_put_contents($file_path, $file_contents);
}
