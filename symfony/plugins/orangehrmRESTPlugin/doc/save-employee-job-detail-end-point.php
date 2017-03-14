/**
* @api {post} /employee/:id/job-detail Save Employee Job Detail
* @apiName SaveEmployeeJobDetails
* @apiGroup Employee
*
* @apiParam {Number}  id Employee id
*
* @apiParam {String} title  Job title name .
* @apiParam {String} category   Job category.
* @apiParam {String} status   Employee job status.
* @apiParam {String} subunit   Subunit of the employee.
* @apiParam {String} location   Job location of the employee.
* @apiParam {String} joinedDate   Employee joined date.
* @apiParam {String} startDate  Employee contract start date.
* @apiParam {String} endDate   Employee contract end date.
* @apiSuccess {String} Object  Data Success response.
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
*     HTTP/1.1 401 Bad Request
*     {
*       "error": ["Saving Failed"]
*     }
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 401 Invalid Parameter
*     {
*       "error": ["Invalid Parameter"]
*     }
*/
