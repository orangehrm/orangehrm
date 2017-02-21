/**
* @api {put} /employee/:id/dependent Update Employee Dependents
* @apiName updateEmployeeDependents
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiParam {String} name Mandatory name of the dependent.
* @apiParam {String} relationship  Mandatory relationship of the dependent.
* @apiParam {String} dob Optional date of birth of dependent.
* @apiParam {String} type  Optional relationship type.
* @apiSuccess {Object} data success response
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully deleted"
*      }
*
* @apiError Bad-Response Saving failed.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 401 Bad Request
*     {
*       "error": ["updating failed"]
*     }
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 401 Invalid Parameter
*     {
*       "error": ["invalid Parameter"]
*     }
*/
