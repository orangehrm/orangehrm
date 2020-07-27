/**
* @api {get} /employee/:id/education 25.Employee Education
* @apiName getEmployeeEducation
* @apiGroup Employee
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam {Number}  id Employee id.
*
* @apiSuccess {Number} level Education level id.
* @apiSuccess {Number} seqId Education record id.
* @apiSuccess {String} institute Institute of studying.
* @apiSuccess {Date} fromDate Start date.
* @apiSuccess {Date} toDate End date.
* @apiSuccess {String} specialization Specialization.
* @apiSuccess {String} year Year of study.
* @apiSuccess {String} gpa Gpa/score.
* @apiSuccess {Object} Data Success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*		{
*		  "data": [
*		    {
*		      "seqId": "1",
*		      "level": "Graduate",
*		      "institute": "",
*		      "specialization": "batsmen",
*		      "year": "2014",
*		      "fromDate": "2014-05-16",
*		      "toDate": "2024-05-09",
*		      "gpa": "4.0"
*		    }
*		  ]
*		}
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
