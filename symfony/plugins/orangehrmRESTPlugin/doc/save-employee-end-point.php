/**
* @api {post} /employee/:id Save Employee
* @apiName saveEmployee
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiParam {String} firstName First name of the employee.
* @apiParam {String} middleName  Middle name of the employee.
* @apiParam {String} lstName  Last name of the employee.
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
*     HTTP/1.1 401 Bad Request
*     {
*       "error": ["Saving failed"]
*     }
*/
