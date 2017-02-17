/**
* @api {get} /employee/:id/contact-detail Employee Contact detail
* @apiName GetEmployeeContactDetails
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiSuccess {String} firstname Firstname of the employee.
* @apiSuccess {String} lastname  Lastname of the employee.
* @apiSuccess {String} address  address of the employee.
* @apiSuccess {String} email  email of the employee.
* @apiSuccess {String} phone  phone of the employee.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*       {
*         "data":
*         {
*             "id": "001",
*             "fullName": "John Doe",
*             "telephone": "03131238",
*             "email": "test@example.com",
*             "address": "17 Clifford Rd, Wellington",
*             "country": "New Zealand"
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
*/
