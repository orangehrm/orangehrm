/**
* @api {del} /employee/:id/work-experience 24.Delete Employee Work Experience
* @apiName deleteEmployeeWorkExperience
* @apiGroup Employee
* @apiVersion 0.1.0
*
* @apiParam {Number}  id Employee id.
* @apiParam  {Number} seqId Work experience record id.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*   {
*
*   "success":"Successfully Deleted"
*
*   }
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
*       "error": "Work Experience Record Not Found"
*     }
*
*
*/
