/**
* @api {get} /employee/:id/supervisor 16.Supervisor Details
* @apiName getEmployeeSupervisor
* @apiGroup Employee
* @apiVersion 0.1.0
*
* @apiParam {Number}  id Employee id.
*
* @apiSuccess {String} name Supervisor name.
* @apiSuccess {Number} supervisorId Supervisor id.
* @apiSuccess {String} code Supervisor code.
* @apiSuccess {String} reportingMethod Reporting method to the supervisor.
* @apiSuccess {Object} Data Success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*   {
*    "data": [
*      {
*        "name": "Hameesh Von Johnson",
*         "id": "5",
*         "code": "1021",
*         "reportingMethod": "Direct"
*      },
*      {
*        "name": "James Paterson",
*         "id": "2",
*         "code": "103",
*         "reportingMethod": "Direct"
*      },
*            ],
*      'rels : []
*   }
*
* @apiError UserNotFound The id of the employee was not found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Not Found
*     {
*       "error": "Employee Not Found"
*     }
*/
