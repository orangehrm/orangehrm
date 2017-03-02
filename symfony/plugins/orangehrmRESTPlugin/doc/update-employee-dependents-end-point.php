/**
* @api {put} /employee/:id/dependent Update Employee Dependents
* @apiName updateEmployeeDependents
* @apiGroup Employee
*
* @apiParam {Number}  id Employee id
*
* @apiParam {String} name Name of the dependent.
* @apiParam {String} relationship  relationship of the dependent.
* @apiParam {String} dob DOB of dependent.
* @apiParam {String} sequenceNumber  Mandatory sequence number.
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
