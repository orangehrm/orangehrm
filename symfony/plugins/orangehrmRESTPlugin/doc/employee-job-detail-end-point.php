/**
* @api {get} /employee/:id/job-detail Employee Job detail
* @apiName GetEmployeeJobDetails
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiSuccess {String} title Job title name.
* @apiSuccess {String} category  Job category.
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
*             "joinedDate": "2001-01-12",
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
*       "error": "EmployeeNotFound"
*     }
*
*
*/
