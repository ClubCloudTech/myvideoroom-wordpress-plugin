#!/usr/bin/env bash

PLUGIN_REMOTE_NAME='my-video-room'
PLUGIN_DIR='myvideoroom-plugin'
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
