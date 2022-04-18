/**
* @api {del} /employee/:id/dependent 10.Delete Employee Dependents
* @apiName deleteEmployeeDependents
* @apiGroup Employee
* @apiVersion 0.1.0
* @apiUse AdminDescription
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
