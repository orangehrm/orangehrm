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
*     [
*       {
*       "firstName": "John",
*       "lastName": "Doe",
*       "middleName": "",
*       "fullName": "John Doe",
*       "id": "001",
*       "status": "active",
*       "jobtitle": "web developer",
*       "supervisor": "Mike com",
*       "supervisorId": "2",
*       "dob": "1989-09-7",
*       "unit": "development",
*       "gender": "M",
*       },
*     ]
*
* @apiError UserNotFound The id of the employee was not found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Not Found
*     {
*       "error": "EmployeeNotFound"
*     }
*/
