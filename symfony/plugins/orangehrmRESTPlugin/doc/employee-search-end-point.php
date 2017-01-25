/**
 * @api {get} /employee/:search Search Employee
 * @apiName SearchEmployee
 * @apiGroup Employee
 *
 * @apiParam {search} string query.
 *
 * @apiSuccess {String} firstname Firstname of the employee.
 * @apiSuccess {String} lastname  Lastname of the employee.
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     [
 *       {
 *       "firstname": "John",
 *       "lastname": "Doe"
 *       }
 *     ]
 *
 * @apiError UserNotFound The id of the User was not found.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *       "error": "EmployeeNotFound"
 *     }
 */
