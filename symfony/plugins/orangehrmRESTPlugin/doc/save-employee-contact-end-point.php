/**
* @api {post} /employee/:id/contact-detail Save Employee Contact detail
* @apiName saveEmployeeContactDetails
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiParam {String} firstname Firstname of the employee.
* @apiParam {String} lastname  Lastname of the employee.
* @apiParam {String} address  address of the employee.
* @apiParam {String} email  email of the employee.
* @apiParam {String} phone  phone of the employee.
* @apiSuccess {Object} data success response
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully saved"
*      }
*
* @apiError Bad-Response Saving failed.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 401 Bad Request
*     {
*       "error": ["Saving failed"]
*     }
*/
