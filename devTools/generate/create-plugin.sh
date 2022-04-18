#!/bin/bash

################################################################################
# Help                                                                         #
################################################################################
Help()
{
   # Display Help
   echo "Create plugin folders"
   echo
   echo "Syntax: create-plugin my-plugin-name"
}

# Get the options
while getopts ":h" option; do
   case $option in
      h) # display Help
         Help
         exit
   esac
done

################################################################################
# Check the plugin name                                                                        #
################################################################################
if [ $# -eq 0 ]; then
    echo "No plugin name provided"
    echo "Syntax: create-plugin my-plugin-name"
    exit 1
fi

################################################################################
# Create the plugin folders                                                                        #
################################################################################
PLUGIN_NAME=$1
FE_PLUGIN_FOLDER=../../src/client/src/$PLUGIN_NAME
BE_PLUGIN_FOLDER=../../src/plugins/$PLUGIN_NAME

mkdir -p ${BE_PLUGIN_FOLDER}/{Api,config,Controller,entity,test,modules,Service}
mkdir -p ${FE_PLUGIN_FOLDER}/{pages,}
cp ./templates/front-end/index.ts.txt ${FE_PLUGIN_FOLDER}/index.ts

echo "$PLUGIN_NAME plugin folder generated"
echo "Happy coding"
