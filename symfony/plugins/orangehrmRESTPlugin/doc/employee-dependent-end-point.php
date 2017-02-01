/**
* @api {get} /employee/:id/dependent Employee Dependents
* @apiName GetEmployeeDependents
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiSuccess {Object} employee dependents.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*       {
*         "data":[
*          {
*             "name": "Inu Lim",
*             "relationship": "Daughter",
*             "dob": "1989-09-02"
*          },
*          {
*             "name": "Sam Lim",
*             "relationship": "Daughter",
*             "dob": "2009-09-02"
*          }
*         ],
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
