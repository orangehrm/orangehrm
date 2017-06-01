/**
* @api {put} /employee/:id/education 33.Update Employee Education
* @apiName updateEmployeeEducation
* @apiGroup Employee
* @apiVersion 0.1.0
*
* @apiParam {Number}  id Employee id.
*
* @apiParam {Number} level Education level id.
* @apiParam {Number} seqId Education record id.
* @apiParam {String} institute Institute of studying.
* @apiParam {Date} startDate Start date.
* @apiParam {Date} endDateDate End date.
* @apiParam {String} specialization Specialization.
* @apiParam {String} year Year of study.
* @apiParam {String} gpa Gpa/score.
* @apiSuccess {Object} Data Success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*   {
*
*   "success":"Successfully Updated",
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
