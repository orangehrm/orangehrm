/**
* @api {del} /employee/:id/dependent Delete Employee Dependents
* @apiName deleteEmployeeDependents
* @apiGroup Employee
*
* @apiParam {Number} id Employee id.
*
* @apiParam {Number} sequenceNumber Mandatory sequence number of the dependent.
* @apiSuccess {Object} Data Success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully Deleted"
*      }
*
* @apiError Bad-Response Saving Failed.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 400 Bad Request
*     {
*       "error": ["Deleting Failed"]
*     }
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Not Found
*     {
*       "error": ["Deleting Failed"]
*     }
*/
