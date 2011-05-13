<?php

class coreActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {        
        $nationalityService = new NationalityService();
        $defs = sfYaml::load(sfConfig::get('sf_root_dir') . '/plugins/orangehrmCorePlugin/config/list_component.yml');

        $header1 = new ListHeader();
        $header2 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Id',
            'width' => '45%',
            'isSortable' => true,
            'sortField' => 'nat_code',
            'elementType' => 'link',
            'elementProperty' => array(
                'labelGetter' => 'getNatCode',
                'placeholderGetters' => array('id' => 'getNatCode'),
                'urlPattern' => '../../../lib/controllers/CentralController.php?id={id}&uniqcode=NAT&capturemode=updatemode'),
        ));

        $header2->populateFromArray(array(
            'name' => 'Name',
            'isSortable' => true,
            'sortField' => 'nat_name',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getNatName'),
        ));

        $params = $defs['Sample'];
        $params['columns'] = array(
            $header1,
            $header2,
        );
        $params['data'] = $nationalityService->getNationalityList();
        
        $params['currentSortField'] = $request->getParameter('sortField', '');
        $params['currentSortOrder'] = $request->getParameter('sortOrder', '');
        
        $recordsLimit = 10;

        if ($request->isMethod('post')) {

            if ($request->getParameter('hdnAction') == 'search') {
                $this->pageNo = 1;
            } elseif ($request->getParameter('pageNo')) {
                $this->pageNo = $request->getParameter('pageNo');
            }

        } else {
            $this->pageNo = 1;
        }


        $pager = new SimplePager('Nationality', $recordsLimit);
        $pager->setPage($this->pageNo);
        $pager->setNumResults($this->recordsCount);
        $pager->init();
        $params['pager'] = $pager;

        $offset = $pager->getOffset();
        $offset = empty($offset)?0:$offset;
        $params['offset'] = $offset;

        $this->parmetersForListCompoment = $params;
    }

}

