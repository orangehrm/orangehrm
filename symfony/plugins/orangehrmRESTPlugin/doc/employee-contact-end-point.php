/**
* @api {get} /employee/:id/contact Employee Contact detail
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
*     [
*       {
*       "firstname": "John",
*       "lastname": "Doe",
*       "address": "17, clifford rd, Wellington",
*       "email": "john@orangehrm.com",
*       "phone": "+762153413",
*       }
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
