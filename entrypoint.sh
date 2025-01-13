#!/bin/bash

# if [ -d "node_modules" ]; then
#     # run ls -a to see all files in the directory
#     # if webroot/cyclomap exists, remove it
#     if [ -d "webroot" ]; then
#         echo "webroot/cyclomap directory exists. Removing it..."
#         rm -rf ./webroot/cyclomap ./webroot/ebike2021
#         # check inside webroot with ls command
#         ls -a ./webroot


#     fi
#     ls -a ./webroot
#     echo "node_modules directory exists. Running 'npm run dev'..."
#     npm run dev
# else
#     echo "node_modules directory does not exist. Running 'npm install' and 'npm run dev'..."
#     npm install
#     npm run dev
# fi
# Keep the container running
tail -f /dev/null