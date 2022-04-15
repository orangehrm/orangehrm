/**
* @api {put} /employee/:id/work-experience 23.Update Employee Work Experience
* @apiName updateEmployeeWorkExperience
* @apiGroup Employee
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam {Number}  id Employee id.
*
* @apiParam  {Number} seqId Work experience record id.
* @apiParam  {String} company Company name.
* @apiParam  {String} title job Title.
* @apiParam  {Date} fromDate Experience from date.
* @apiParam  {Date} toDate Experience to date.
* @apiParam  {String} comment Work experience comment.
* @apiSuccess {Object} Data Success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*   {
*
*   "success":"Successfully Updated"
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
