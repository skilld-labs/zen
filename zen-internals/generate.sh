#!/bin/bash

# This script is used by the MAINTAINERS to generate the CSS files from the Sass
# files and make copies of the STARTERKIT stylesheets for the base Zen theme.


ORIG=`pwd`;
STARTERKIT=../STARTERKIT;


# Change directory to the STARTERKIT and run compass with a custom config.
cd $STARTERKIT;
compass clean;

# Create our custom base partial, while keeping the original.
mv sass/_base.scss $ORIG/;
cat $ORIG/_base.scss $ORIG/extras/sass/_base_extras.scss > sass/_base.scss;

# Build the stylesheets for the Zen base theme.
cp $ORIG/extras/sass/styles-fixed* sass/;
compass compile --environment production --no-line-comments --output-style compressed;
rm sass/styles-fixed*;

# Copy the stylesheets from STARTERKIT to the Zen theme.
rm $ORIG/css/*.css;
rm $ORIG/images/*;
cp css/styles* $ORIG/css/;
cp images/* $ORIG/images/;

# Build the CSS versions of the stylesheets.
cp $ORIG/extras/sass/css-* sass/;
rm css/*.css;
compass clean;
compass compile --no-line-comments;
rm sass/css-*;

# Don't use the generated styles.css.
git checkout css/styles.css css/styles-rtl.css;

# Massage the generated css-* files and rename them.
for FILENAME in css/css-*.css; do
  NEWFILE=`echo $FILENAME | sed -e 's/css\-//'`;

  cat $FILENAME |
  # Ensure comment headings have a proceeding blank line.
  sed -e '/^ \*\/$/ G' |
  # Ensure section headings have a proceeding blank line.
  sed -e '/^   ========================================================================== \*\/$/ G' |
  # Ensure each selector is on its own line.
  sed -e 's/^\(\/\*.*\), /\1FIX_THIS_COMMA /' |
  sed -e 's/^\([^ ].*\), /\1,\
/' |
  sed -e 's/^\([^ ].*\), /\1,\
/' |
  sed -e 's/^\([^ ].*\), /\1,\
/' |
  sed -e 's/^\([^ ].*\), /\1,\
/' |
  sed -e 's/FIX_THIS_COMMA/,/' |
  sed -e '/: /! s/^\(  [^ /].*\), /\1,\
  /' |
  # Fix IE wireframes rules.
  sed -n '1h;1!H;$ {g;s/\.lt\-ie8\n/.lt-ie8 /g;p;}' |
  # Fix site name rules.
  sed -n '1h;1!H;$ {g;s/  #site-name\n  /  #site-name /g;p;}' |
  # Ensure each rule has a proceeding blank line.
  sed -e '/^ *}/ G' |
  # Move property-level comments back to the previous line with the property.
  sed -e 's/^ \{2,4\}\(\/\*.*\*\/\)$/  MOVE_UP\1/' |
  sed -n '1h;1!H;$ {g;s/\n  MOVE_UP/ /g;p;}' |
  # Move commented-out properties to their own line.
  sed -e 's/ \(\/\* [^:/]*: [^;]*; \*\/ \/\* [^/]* \*\/\) /\
  \1\
  /' |
  sed -e 's/\([^ ]\) \(\/\* [^:/]*: [^;]*; \*\/ \/\* [^/]* \*\/\)$/\1\
  \2/' |
  # Remove blank lines before closing curly brackets.
  sed -n '1h;1!H;$ {g;s/\n*\(\n}\n\)/\1/g;p;}' |
  # Remove blank lines before block-level end comment tags ( */ ).
  sed -n '1h;1!H;$ {g;s/\n*\(\n\*\/\n\)/\1/g;p;}' |
  # Add a blank line between 2 block-level comment tags.
  sed -n '1h;1!H;$ {g;s/\(\n\*\/\n\)\/\*/\1\
\/\*/g;p;}' |
  # Put /* End @media ... */ comments directly after their closing curly brackets.
  sed -n '1h;1!H;$ {g;s/\}\n\n\(\/\* End @media \)/\} \1/g;p;}' |
  # Remove any blank lines at the end of the file.
  sed -n '$!p;$ {s/^\(..*\)$/\1/p;}' |
  # Remove the second @file comment block in RTL layout files.
  sed -n '1h;1!H;$ {g;s/\n\/\*\*\n \* \@file\n[^\/]*\/\/[^\/]*\n \*\/\n//;p;}' |
  # Convert 2 or more blank lines into 1 blank line and write to the new file.
  cat -s > $NEWFILE;

  rm $FILENAME;
done

for FIND_FILE in $ORIG/extras/text-replacements/*--search.txt; do
  REPLACE_FILE=`echo "$FIND_FILE" | sed -e 's/\-\-search\.txt/--replace.txt/'`;
  CSS_FILE=css/`basename $FIND_FILE | sed -e 's/\-\-.*\-\-search\.txt/.css/'`;

  # Convert search string to a sed-compatible regular expression.
  FIND=`cat $FIND_FILE | perl -e 'while (<>) { $_ =~ s/\s+$//; $line = quotemeta($_) . "\\\n"; $line =~ s/\\\([\(\)\{\}])/\1/g; print $line}'`;

  cat $CSS_FILE |
  # Replace search string with "TEXT-REPLACEMENT" token.
  sed -n -e '1h;1!H;$ {g;' -e "s/$FIND/TEXT\-REPLACEMENT/;" -e 'p;}' |
  sed -e 's/TEXT\-REPLACEMENT/TEXT\-REPLACEMENT\
/' |
  # Replace "TEXT-REPLACEMENT" token with contents of replacement file.
  sed -e "/^TEXT-REPLACEMENT\$/{r $REPLACE_FILE" -e 'd;}' | #-e '/^TEXT-REPLACEMENT$/! d;' |
  cat > $CSS_FILE.new;

  # Halt the script if no replacement has been made.
  if [ -z "`diff -q $CSS_FILE $CSS_FILE.new`" ]; then
    echo "FATAL ERROR: The following file contents were not found: `basename $FIND_FILE`";
    # Delete all the generated CSS, except for the one that generated the error.
    rm css/*.css $ORIG/css/*.css;
    mv $CSS_FILE.new $CSS_FILE;
    # Restore the base partial.
    mv $ORIG/_base.scss sass/;
    exit;
  fi

  mv $CSS_FILE.new $CSS_FILE;
done

# Restore the environment.
mv $ORIG/_base.scss sass/;
cd $ORIG;
