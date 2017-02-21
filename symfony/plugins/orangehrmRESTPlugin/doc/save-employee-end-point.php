/**
* @api {post} /employee/:id Save Employee
* @apiName saveEmployee
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiParam {String} firstName Mandatory First name of the employee.
* @apiParam {String} middleName  Optional Middle name of the employee.
* @apiParam {String} lstName  Mandatory Last name of the employee.
* @apiParam {String} id  employee number.
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
*     HTTP/1.1 40 Bad Request
*     {
*       "error": ["Saving failed"]
*     }
*/
