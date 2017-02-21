/**
* @api {post} /employee/:id/contact-detail Save Employee Contact detail
* @apiName saveEmployeeContactDetails
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiParam {String} address  Optional address of the employee.
* @apiParam {String} email  Optional email of the employee.
* @apiParam {String} phone  Optional phone of the employee.
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
*     HTTP/1.1 400 Bad Request
*     {
*       "error": ["Saving failed"]
*     }
*/
