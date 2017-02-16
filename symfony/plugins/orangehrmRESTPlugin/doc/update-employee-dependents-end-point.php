/**
* @api {put} /employee/:id Update Employee Dependents
* @apiName updateEmployeeDependents
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiParam {int} sequenceNumber  sequence number of the dependent.
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
