#!/usr/bin/env bash

PLUGIN_REMOTE_NAME='my-video-room'
PLUGIN_DIR='my-video-room'
SCRIPT_PATH=$(readlink -f "$0")
SCRIPT_DIR=$(dirname "${SCRIPT_PATH}")

if [ ! -d "${SCRIPT_DIR}/svn" ]; then
  echo 'creating svn'
  svn co https://plugins.svn.wordpress.org/${PLUGIN_REMOTE_NAME} ${SCRIPT_DIR}/svn
else
  echo 'svn folder already exists'
  (
    cd ${SCRIPT_DIR}/svn
    svn up
  )
fi

rsync -av "${SCRIPT_DIR}/${PLUGIN_DIR}/" "${SCRIPT_DIR}/svn/trunk" --exclude .DS_Store --exclude test/
rsync -av "${SCRIPT_DIR}/assets/" "${SCRIPT_DIR}/svn/assets" --exclude .DS_Store --exclude test/

(
  cd svn
  svn st trunk | grep ? | awk '{print $2}' | xargs svn add
  svn st assets | grep ? | awk '{print $2}' | xargs svn add
)

echo "
You probably want to do something like this:

cd svn
svn ci -m '<message>'
svn cp trunk tags/<tag>
svn ci -m 'Tag <tag>'
"
