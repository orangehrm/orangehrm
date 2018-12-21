<?php


class IntegrationXMLProcessor
{

    public $content = null;

    const BUTTON = "button";
    const MODAL = "confirmation";
    const JS_FUNCTION = 'function';
    const ON_CLICK = 'onclick';

    public function processXML($xmlContent)
    {

        $this->content['html'] = '';
        $this->content['js'] = '';
        foreach ($xmlContent->components[0]->component as $component) {

            $componentType = $component->attributes()['type'];

            switch ($componentType) {
                case self::BUTTON:
                    $this->processButton($component);
                    break;
                case self::MODAL:
                    //  $this->processModal($component);
                    break;
                case self::JS_FUNCTION:
                    $this->processFunction($component);
                    break;
                case self::ON_CLICK:
                    $this->processOnClick($component);
                    break;
            }

        }

        return $this->content;
    }


    protected function processButton($component)
    {
        $path_to_file = "../plugins/orangehrmAdminPlugin/modules/integration/templates/IntButton.int";
        $file_contents = file_get_contents($path_to_file);
        $file_contents = str_replace("btn_type", $component->type, $file_contents);
        $file_contents = str_replace("btn_id", $component->id, $file_contents);
        $file_contents = str_replace("btn_onclick", $component->onClick, $file_contents);
        $file_contents = str_replace("btn_text", $component->text, $file_contents);
        $file_contents = str_replace("btn_class", $component->class, $file_contents);
        $this->content['html'] = $this->content['html'] . $file_contents;
        $this->content['css'] = $component->css;
    }

    protected function processModal($component)
    {
        $path_to_file = "../plugins/orangehrmAdminPlugin/modules/integration/templates/IntModal.int";
        $file_contents = file_get_contents($path_to_file);
        $file_contents = str_replace("modal_id", $component->id, $file_contents);
        $file_contents = str_replace("modal_header", $component->header, $file_contents);
        $file_contents = str_replace("modal_onclick", $component->function, $file_contents);
        $file_contents = str_replace("modal_text", $component->text, $file_contents);

        $this->content['html'] = $this->content['html'] . $file_contents;
        $this->content['css'] = $this->content['css'] . $component->css;

    }

    protected function getbackBoneFile($type)
    {
        $path_to_file = "../plugins/orangehrmAdminPlugin/modules/integration/templates/OnClick.int" . $type . "php";
        $file_contents = file_get_contents($path_to_file);
        $file_contents = str_replace("\nH", ",H", $file_contents);
        file_put_contents($path_to_file, $file_contents);
    }


    protected function processOnClick($component)
    {

        $path_to_file = "../plugins/orangehrmAdminPlugin/modules/integration/templates/OnClick.int";
        $file_contents = file_get_contents($path_to_file);
//        $file_contents = str_replace("id",$component->id,$file_contents);
//        if($component->submit != null){
//
//            $file_contents = str_replace("modal_id",$component->submit,$file_contents);
//        }
        //TODO handle other actions

    }

    protected function processFunction($component)
    {
        $path_to_file = "../plugins/orangehrmAdminPlugin/modules/integration/templates/Function.int";
        $file_contents = file_get_contents($path_to_file);
        $file_contents = str_replace("function_name", $component->name, $file_contents);

        if ($component->body[0]->confirm == 'true') {

            $path = "../plugins/orangehrmAdminPlugin/modules/integration/templates/ToggleModal.int";

            $file_contents_confirm = file_get_contents($path);
            $file_contents_changed = str_replace("modal_id", $component->body[0]->confirmation, $file_contents_confirm);
            $file_contents = str_replace("function_body", $file_contents_changed, $file_contents);

            $this->content['js'] = $this->content['js'] . $file_contents;
            return;
        } else {


                if($component->body[0]->ajax[0]->type == 'issueToken'){
//                    $path_issue_token = "../plugins/orangehrmAdminPlugin/modules/integration/templates/AjaxIssueToken.int";
//                    $file_contents_token = file_get_contents($path_issue_token);
//                    $file_contents_token = str_replace("client_url_field",$component->body[0]->ajax[0]->clientUrl, $file_contents_token);
//                    $file_contents_token = str_replace("client_id_field", $component->body[0]->ajax[0]->clientId, $file_contents_token);
//                    $file_contents_token = str_replace("client_secret_field",$component->body[0]->ajax[0]->clientSecret, $file_contents_token);
//
//                    $path_ajax = "../plugins/orangehrmAdminPlugin/modules/integration/templates/Ajax.int";
//                    $file_ajax = file_get_contents($path_ajax);
//                    $file_ajax = str_replace("client_url_field",$component->body[0]->ajax[0]->success[0]->ajax[0]->clientUrl, $file_ajax);
//
//                    $file_contents_token = str_replace("success_body",$file_ajax, $file_contents_token);
//                    $this->content['js'] = $this->content['js'] .$file_contents_token;

                    $this->content['id'] = $component->body[0]->ajax[0]->clientId;
                    $this->content['url'] = $component->body[0]->ajax[0]->clientUrl;
                    $this->content['successUrl'] = $component->body[0]->ajax[0]->success;
                    $this->content['secret'] = $component->body[0]->ajax[0]->clientSecret;
                }else {
                    $this->content['ajaxUrl'] = $component->body[0]->ajax[0]->ajaxUrl;
                }


        }

    }


}