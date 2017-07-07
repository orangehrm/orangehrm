/**
* @api {get} /employee/:id/dependent 09.Employee Dependents
* @apiName GetEmployeeDependents
* @apiGroup Employee
* @apiVersion 0.1.0
*
* @apiParam {Number}  id Employee id.
*
* @apiSuccess {Object} name Name of the dependent.
* @apiSuccess {Object} relationship Relationship of the dependent.
* @apiSuccess {Object} dob Date of birth of the dependent.
* @apiSuccess {Object} seqNumber Sequence number of the dependent.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*       {
*         "data":[
*          {
*             "name": "Inu Lim",
*             "relationship": "Daughter",
*             "dob": "1989-09-02",
*             "sequenceNumber": "5"
*          },
*          {
*             "name": "Sam Lim",
*             "relationship": "Daughter",
*             "dob": "2009-09-02"
*             "sequenceNumber": "5"
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
*       "error": "Employee Not Found"
*     }
*/
