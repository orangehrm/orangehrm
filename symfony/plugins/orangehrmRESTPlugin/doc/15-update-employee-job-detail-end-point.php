/**
* @api {put} /employee/:id/job-detail 15.Update Employee Job Detail
* @apiName updateEmployeeJobDetails
* @apiGroup Employee
* @apiVersion 0.1.0
*
* @apiParam {Number}  id Employee id
*
* @apiParam {Number} title  Job title id.
* @apiParam {Number} category   Job category id.
* @apiParam {Number} status   Employee job status id.
* @apiParam {Number} subunit   Subunit id of the employee.
* @apiParam {Number} location   Job location id of the employee.
* @apiParam {String} joinedDate   Employee joined date.
* @apiParam {String} startDate  Employee contract start date.
* @apiParam {String} endDate   Employee contract end date.
* @apiSuccess {String} Object Data Success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully Saved"
*      }
*
* @apiError Bad-Response Saving Failed.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 400 Bad Request
*     {
*       "error": ["Saving Failed"]
*     }
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["Invalid Parameter"]
*     }
*/
