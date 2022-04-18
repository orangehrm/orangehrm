/**
* @api {get} /employee/:id/work-experience 21.Employee Work Experience
* @apiName getEmployeeWorkExperience
* @apiGroup Employee
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam {Number}  id Employee id.
*
* @apiSuccess {String} company Company name.
* @apiSuccess {Number} id Work experience id.
* @apiSuccess {String} jobTitle Job title.
* @apiSuccess {Date} fromDate Experience from date.
* @apiSuccess {Date} toDate Experience to date.
* @apiSuccess {String} comment Work experience comment.
* @apiSuccess {Object} Data Success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*		{
*		  "data": [
*		    {
*		      "id": "2",
*		      "company": "Aniline pvt ltd",
*		      "jobTitle": "Craft Worker",
*		      "fromDate": "2016-02-09 00:00:00",
*		      "toDate": "2017-02-12 00:00:00",
*		      "comment": "sample comment"
*		    },
*		    {
*		      "id": "1",
*		      "company": "NSR 11",
*		      "jobTitle": "Craft Worker123",
*		      "fromDate": "2014-02-09 00:00:00",
*		      "toDate": "2016-02-12 00:00:00",
*		      "comment": "test"
*		    }
*		  ]
*
*		}
**   }
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
