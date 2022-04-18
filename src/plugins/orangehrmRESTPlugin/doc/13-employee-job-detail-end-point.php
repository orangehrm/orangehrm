/**
* @api {get} /employee/:id/job-detail 13.Employee Job Detail
* @apiName GetEmployeeJobDetails
* @apiGroup Employee
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam {Number}  id Employee id.
*
* @apiSuccess {String} title Job title name.
* @apiSuccess {String} category  Job category.
* @apiSuccess {String} status  Employee job status.
* @apiSuccess {String} subunit  Subunit of the employee.
* @apiSuccess {String} location  Job location of the employee.
* @apiSuccess {String} joinedDate  Employee joined date.
* @apiSuccess {String} startDate Employee contract start date.
* @apiSuccess {String} endDate  Employee contract end date.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*       {
*         "data":
*         {
*             "title": "Web Developer",
*             "category": "Engineering",
*             "status": "Active",
*             "joinedDate": "2001-01-12",
*             "subunit": "Marketing Unit",
*             "location": Eng Dept,
*             "startDate": "2001-02-01",
*             "endDate": "2005-02-01"
*         },
*       "rels": {
*       }
*     }
*
* @apiError UserNotFound The id of the employee was not found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Not Found
*     {
*       "error": "Employee Not Found"
*     }
*
*
*/
