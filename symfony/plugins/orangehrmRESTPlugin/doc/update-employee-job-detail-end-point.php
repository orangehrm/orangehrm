/**
* @api {put} /employee/:id/job-detail Update Employee Job Detail
* @apiName updateEmployeeJobDetails
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiParam {String} title Optional Job title name .
* @apiParam {String} category  Optional Job category.
* @apiParam {String} status  Optional Employee job status.
* @apiParam {String} subunit  Optional Subunit of the employee.
* @apiParam {String} location  Optional Job location of the employee.
* @apiParam {String} joinedDate  Optional Employee joined date.
* @apiParam {String} startDate Optional Employee contract start date.
* @apiParam {String} endDate  Optional Employee contract end date.
* @apiSuccess {String} Object data success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully saved"
*      }
*
* @apiError Bad-Response Saving failed.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 400 Bad Request
*     {
*       "error": ["Saving failed"]
*     }
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["invalid Parameter"]
*     }
*/
