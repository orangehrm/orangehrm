/**
* @api {del} /employee/:id/education 27.Delete Employee Education
* @apiName deleteEmployeeEducation
* @apiGroup Employee
* @apiVersion 0.1.0
*
* @apiParam {Number}  id Employee id.
* @apiParam {Number} seqId Education record id.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*   {
*
*   "success":"Successfully Deleted",
*   "seqId":3
*
*   }
*
*
* @apiError UserNotFound The id of the employee was not found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Not Found
*     {
*       "error": "Employee Not Found"
*     }
* @apiError RecordNotFound The id of the record was not found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Not Found
*     {
*       "error": "Employee Education Record Not Found"
*     }
*
*/
