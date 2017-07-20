<?php 

class ActionFilter extends sfFilter {

    public function execute($filterChain) {
        $rootPath = realpath(dirname(__FILE__) . '/../../../../..');

        $request = $this->getContext()->getRequest();
        if (is_file($rootPath . '/lib/confs/Conf.php')) {
            header("Location: /index.php");
            exit();
        }

        if (($request['action'] != 'getDatabaseInfo') && !$this->getContext()->getUser()->isAuthenticated()) {
            $this->getContext()->getController()->redirect('upgrade/getDatabaseInfo');
        }
        
        // Execute next filter in filter chain
        $filterChain->execute();
    }

}