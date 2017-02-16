/**
* @api {del} /employee/:id Delete Employee Dependents
* @apiName deleteEmployeeDependents
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiParam {String} name name of the dependent.
* @apiParam {String} relationship  relationship of the dependent.
* @apiParam {String} dob  date of birth of dependent.
* @apiParam {String} type  relationship type.
* @apiParam {int} sequenceNumber  sequence number of the dependent.
* @apiSuccess {Object} data success response
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully updated"
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
