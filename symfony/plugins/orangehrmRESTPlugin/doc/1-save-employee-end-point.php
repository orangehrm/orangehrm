/**
* @api {post} /employee/:id 01.Save Employee
* @apiName saveEmployee
* @apiGroup Employee
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
*
* @apiParam {String} firstName Mandatory First name of the employee.
* @apiParam {String} middleName Middle name of the employee.
* @apiParam {String} lastName  Mandatory Last name of the employee.
* @apiParam {String} code  Employee code.
* @apiSuccess {Object} Data Success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully Saved",
*        "id": "11"
*      }
*
* @apiError Bad-Response Saving Failed.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Bad Request
*     {
*       "error": ["Saving Failed"]
*     }
*/
