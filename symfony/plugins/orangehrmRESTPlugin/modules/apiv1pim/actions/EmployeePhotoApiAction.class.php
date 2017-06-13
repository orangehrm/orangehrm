<?php

/**
 * Created by PhpStorm.
 * User: sanjaya
 * Date: 6/2/17
 * Time: 8:15 AM
 */


use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Api\Pim\EmployeePhotoAPI;
use Orangehrm\Rest\Api\Exception\NotImplementedException;

class EmployeePhotoApiAction extends baseRestAction
{

    private $apiEmployeePhoto = null;


    protected function init(Request $request)
    {
        $this->apiEmployeePhoto = new EmployeePhotoAPI($request);
    }


    /**
     * @param \Orangehrm\Rest\Http\Request $request
     * @return \Orangehrm\Rest\Http\Response
     */
    protected function handleGetRequest(\Orangehrm\Rest\Http\Request $request)
    {
        return $this->apiEmployeePhoto->getEmployeePhoto();

    }

    /**
     * @param \Orangehrm\Rest\Http\Request $request
     * @return \Orangehrm\Rest\Http\Response
     */
    protected function handlePostRequest(\Orangehrm\Rest\Http\Request $request)
    {
        throw new NotImplementedException();
    }
}