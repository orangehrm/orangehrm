################################################################################
# Check the action name                                                                        #
################################################################################
if [ $# -eq 0 ]; then
    echo "No API class provided"
    echo "Syntax: create-api api-class"
    exit 1
fi

################################################################################
# define vars                                                    #
################################################################################
PLUGIN_NAME=$1
API_NAME=$2
BE_PLUGIN_FOLDER=../../src/plugins/$PLUGIN_NAME
FE_PLUGIN_FOLDER=../../src/client/src/$PLUGIN_NAME
API_CLASS_NAME=$API_NAME
API_NAMESPACE=$API_NAME
ENTITY_CLASS=$API_NAME

################################################################################
# copy api files                                                                       #
################################################################################
MODEL_FILE=${BE_PLUGIN_FOLDER}/Api/Model/${API_CLASS_NAME}Model.php
API_FILE=${BE_PLUGIN_FOLDER}/Api/${API_CLASS_NAME}Api.php

mkdir -p ${BE_PLUGIN_FOLDER}/Api/Model
cp ./templates/back-end/api/model/apiModel.php.txt ${MODEL_FILE}
cp ./templates/back-end/api/api.php.txt ${API_FILE}

#replace placeholders
sed -i "s/{API_NAMESPACE}/${API_NAMESPACE}/g" ${MODEL_FILE}
sed -i "s/{API_CLASS}/${API_CLASS_NAME}/g" ${MODEL_FILE}
sed -i "s/{ENTITY_CLASS}/${ENTITY_CLASS}/g" ${MODEL_FILE}

sed -i "s/{API_NAMESPACE}/${API_NAMESPACE}/g" ${API_FILE}
sed -i "s/{API_CLASS}/${API_CLASS_NAME}/g" ${API_FILE}
sed -i "s/{ENTITY_CLASS}/${ENTITY_CLASS}/g" ${API_FILE}

echo "Generated ${MODEL_FILE=} File"
echo "Generated ${API_FILE=} File"
