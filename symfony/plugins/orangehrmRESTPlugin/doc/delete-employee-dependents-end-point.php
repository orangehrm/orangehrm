/**
* @api {del} /employee/:id/dependent Delete Employee Dependents
* @apiName deleteEmployeeDependents
* @apiGroup Employee
*
* @apiParam {Number} id Employee id
*
* @apiParam {Number} sequenceNumber Mandatory sequence number of the dependent.
* @apiSuccess {Object} Data success response.
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
*     HTTP/1.1 400 Bad Request
*     {
*       "error": ["Deleting failed"]
*     }
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Not Found
*     {
*       "error": ["Deleting failed"]
*     }
*/
