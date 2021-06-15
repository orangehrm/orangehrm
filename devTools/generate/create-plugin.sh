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

# Create plugin folders
mkdir -p ../../symfony/plugins/$PLUGIN_NAME/{Api,config,Controller,entity,test,modules,Service}
mkdir  ../../symfony/client/src/$PLUGIN_NAME/

echo "$PLUGIN_NAME plugin folder generated"
echo "Happy coding"
