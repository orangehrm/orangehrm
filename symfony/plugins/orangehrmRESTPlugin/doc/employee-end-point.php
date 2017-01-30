/**
* @api {get} /employee/:id Employee detail
* @apiName GetEmployeeDetail
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiSuccess {String} firstname Firstname of the employee.
* @apiSuccess {String} lastname  Lastname of the employee.
* @apiSuccess {String} gender  gender of the employee.
* @apiSuccess {String} title  title of the employee.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*       {
*         "data":
*         {
*             "id": "001",
*             "firstName": "John",
*             "lastName": "Doe",
*             "middleName": "",
*             "fullName": "John Doe",
*             "status": "active",
*             "jobtitle": "web developer",
*             "supervisor": "Mike com",
*             "supervisorId": "2",
*             "dob": "1989-09-7",
*             "unit": "development",
*             "gender": "M"
*         },
*       "rels": {
*         "contact-detail": "/employee/:id/contact-detail",
*         "supervisor": "/employee/:id/supervisor",
*         "job-detail": "/employee/:id/job-detail",
*         "dependent": "/employee/:id/dependent"
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
*/
