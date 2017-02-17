/**
* @api {get} /employee/:id/supervisor Employee Contact detail
* @apiName GetEmployeeContactDetails
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiSuccess {Object} supervisors employee Supervisors
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*       {
*         "data": [
*           {
*             "Id": "001",
*             "fullName": "John Doe",
*             "reportingMethod": "direct"
*           },
*           {
*             "Id": "002",
*             "fullName": "John Max",
*             "reportingMethod": "Indirect"
*           },
*        ],
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
