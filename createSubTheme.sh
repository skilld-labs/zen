#!/bin/sh

SOURCES="./STARTERKIT"
REPLACE="STARTERKIT"

echo -n "Please enter the theme name: "
read THEME

if [ -z "$THEME" ]; then
  echo "You need to enter a theme name."
  exit 1
fi

if [ ! -d "$SOURCES" ]; then
  echo "Failed to find STARTERKIT sources."
  exit 1
fi

cp -rf $SOURCES $THEME

find $THEME \
  -type f \
  -exec sed -i "s/${REPLACE}/${THEME}/g" {} +

node rename.js $THEME $REPLACE $THEME
