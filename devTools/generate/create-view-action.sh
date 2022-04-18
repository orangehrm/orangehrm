#!/bin/bash

################################################################################
# Help                                                                         #
################################################################################
Help()
{
   # Display Help
   echo "Create action"
   echo
   echo "Syntax: create-view-action action-name"
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
# Check the action name                                                                        #
################################################################################
if [ $# -eq 0 ]; then
    echo "No action name provided"
    echo "Syntax: create-view-action action-name"
    exit 1
fi

################################################################################
# define place holders                                                                       #
################################################################################
PLUGIN_NAME=$1
ACTION_NAME=$2
ACTION_VIEW_TITLE='View '${ACTION_NAME}
SAVE_ACTION='save'${ACTION_NAME}
ACTION_CONTROLLER_NAME=${ACTION_NAME}
ACTION_FOLDER="$(tr [A-Z] [a-z] <<< "${ACTION_NAME}")"
ACTION_CONTROLLER_COMPONENT=${ACTION_FOLDER}'-list'
BE_PLUGIN_FOLDER=../../src/plugins/$PLUGIN_NAME
FE_PLUGIN_FOLDER=../../src/client/src/$PLUGIN_NAME
VIEW_VUE_FILE=${FE_PLUGIN_FOLDER}/pages/${ACTION_FOLDER}/${ACTION_NAME}.vue
################################################################################
# copy vue files                                                                       #
################################################################################

mkdir -p ${FE_PLUGIN_FOLDER}/pages/${ACTION_FOLDER}
cp ./templates/front-end/view/ActionView.txt ${VIEW_VUE_FILE}

#replace placeholders
sed -i "s/{ACTION_VIEW_TITLE}/${ACTION_VIEW_TITLE}/g" ${VIEW_VUE_FILE}
sed -i "s/{SAVE_ACTION}/${SAVE_ACTION}/g" ${VIEW_VUE_FILE}

echo "Generated ${VIEW_VUE_FILE} File"

#update imports
sed -i "s/{ACTION_NAME}/${ACTION_NAME}/g" ${FE_PLUGIN_FOLDER}/index.ts
sed -i "s/{ACTION_FOLDER}/${ACTION_FOLDER}/g" ${FE_PLUGIN_FOLDER}/index.ts
sed -i "s/{EXPORT_GOES_HERE}/'${ACTION_CONTROLLER_COMPONENT}': ${ACTION_NAME},/g" ${FE_PLUGIN_FOLDER}/index.ts

echo "updated import/exports in ${FE_PLUGIN_FOLDER}/index.ts File"

################################################################################
# copy action files                                                                       #
################################################################################
CONTROLLER_FILE=${BE_PLUGIN_FOLDER}/Controller/${ACTION_CONTROLLER_NAME}Controller.php
ROUTES_FILE=${BE_PLUGIN_FOLDER}/config/routes.yaml
cp ./templates/back-end/controller/viewController.txt ${CONTROLLER_FILE}
cp ./templates/back-end/config/routes.yaml.txt ${ROUTES_FILE}

#replace placeholders
sed -i "s/{ACTION_CONTROLLER_NAME}/${ACTION_CONTROLLER_NAME}/g" ${CONTROLLER_FILE}
sed -i "s/{ACTION_CONTROLLER_COMPONENT}/${ACTION_CONTROLLER_COMPONENT}/g" ${CONTROLLER_FILE}
sed -i "s/{PLUGIN_NAME_SPACE}/${ACTION_NAME}/g" ${CONTROLLER_FILE}

echo "Generated ${CONTROLLER_FILE} File"
