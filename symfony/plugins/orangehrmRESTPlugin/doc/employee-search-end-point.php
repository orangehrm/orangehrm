/**
 * @api {get} /employee/:search Employee Search
* @apiName SearchEmployee
 * @apiGroup Employee
 *
 * @apiParam {String} [name] employee name
 * @apiParam {Number} [id] employee Id
 * @apiParam {String} [status] employee status
 * @apiParam {String} [supervisor] supervisor name
 * @apiParam {String} [jobtitle] employee job title
 * @apiParam {String} [unit] Employee Unit
 * @apiParam {String} [dob] employee birth day
 * @apiParam {String} [gender] employee gender
 * @apiParam {Number} [limit] record limit
 *
 * @apiSuccess {String} firstname Firstname of the employee.
 * @apiSuccess {String} lastname  Lastname of the employee.
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
 *       {
 *       "firstname": "John",
 *       "lastname": "Mass",
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
 *       }
 *     ]
 *
 * @apiError EmployeeNotFound.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "EmployeeNotFound"
 *     }
 */
