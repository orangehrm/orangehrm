/**
 * @api {get} /employee/:search Employee Search
* @apiName SearchEmployee
 * @apiGroup Employee
 *
 * @apiParam {String} [search] search query
 * @apiParamExample {json} Request-Example:
 *     {
 *       "name": "John",
 *       "gender" : 'M'
 *     }
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
 *       },
 *       {
 *       "firstname": "John",
 *       "lastname": "Mass"
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
