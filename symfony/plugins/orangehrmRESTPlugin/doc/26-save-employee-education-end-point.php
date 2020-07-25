/**
* @api {post} /employee/:id/education 26.Save Employee Education
* @apiName saveEmployeeEducation
* @apiGroup Employee
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam {Number}  id Employee id.
*
* @apiParam {Number} level Education level id.
* @apiParam {Number} seqId Education record id.
* @apiParam {String} institute Institute of studying.
* @apiParam {Date} startDate Start date.
* @apiParam {Date} endDate End date.
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
*   "success":"Successfully Saved",
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
*
*/
