/**
* @api {del} /employee/:id/supervisor 18.Delete Supervisor Details
* @apiName deleteEmployeeSupervisor
* @apiGroup Employee
* @apiVersion 0.1.0
*
* @apiParam {Number}  id Employee id.
* @apiParam {Number} supervisorId Supervisor id (Mandatory).
* @apiParam {String} reportingMethod Reporting method to the supervisor (Mandatory).
* @apiSuccess {Object} Data Success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
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
*       "error": ["Supervisor Not Found"]
*     }
*/
