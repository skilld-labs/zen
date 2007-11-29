Full documentation can (cough) /will/ be found in Drupal's Handbook:
  http://drupal.org/node/193318


Installation:

  1. Download Zen from http://drupal.org/project/zen

  2. Unpack the downloaded file and place the zen folder in your Drupal
     installation under one of the following locations:
       sites/all/themes
       sites/default/themes

  3. Log in as an administrator on your Drupal site and go to Administer > Site
     building > Themes (admin/build/themes) and make Zen or one of its
     sub-themes the default theme.

  Optional:

  4. Install the Theme Settings API module. Available from:
     http://drupal.org/project/themesettingsapi

     This module is built-in to Drupal 6, so why not add it to your Drupal 5 installation?

  5. From the Theme settings page (admin/build/themes) configure the Zen theme
     or a sub-theme and note the additional settings that are now available the
     under "Theme-specific settings" heading.

Build your own sub-theme:

  The base Zen theme is designed to be easily extended by its sub-themes. You
  shouldn't modify any of the CSS or PHP files in the root zen/ folder; but
  instead you should create a sub-theme of zen which is located in a sub-folder
  of the root zen/ folder.  For example, the zen-classic sub-folder contains the
  files for the Zen Classic sub-theme.

  [ instructions go here ]

Layout method for the base Zen theme:

  Based on the completely undocumented "Holy Slurpy Cup" layout method. Docs to
  follow soon; I promise.
