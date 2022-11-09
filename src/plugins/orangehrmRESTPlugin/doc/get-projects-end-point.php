/**
* @api {get} /project 09.Get Projects
* @apiName getProjects
* @apiGroup Time
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
*
* @apiSuccess  {Number} projectId  Project id.
* @apiSuccess  {Number} customerId  Customer id.
* @apiSuccess  {Number} isDeleted  Is deleted status values (0,1).
* @apiSuccess  {String} projectName  Project name.
* @apiSuccess  {String} customerName  Customer name.
* @apiSuccess  {String} description  Description.
* @apiSuccess  {Object} admins Project admins.
* @apiSuccess  {Object} activities Project activities.
* @apiSuccess  {Number} employeeId Project admin employee id.
* @apiSuccess  {String} name Project admin name.
* @apiSuccess  {Number} activities.id Project Activity id.
* @apiSuccess  {String} activities.name Project activity name.
* @apiSuccess  {Number} activities.isDeleted Project is deleted status (1,0).
*
*
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*		{
*		    "data": [
*			{
*			    "projectId": "1",
*			    "projectName": "Manage Sub Units",
*			    "customerId": "2",
*			    "customerName": "customer",
*			    "description": "description",
*			    "isDeleted": "0",
*			    "admins": {
*				"employeeId": "3",
*				"name": "Hameesh Marshall"
*			    },
*			    "activities": [
*				{
*				    "id": "1",
*				    "name": "unit1",
*				    "isDeleted": "0"
*				},
*				{
*				    "id": "2",
*				    "name": "planning",
*				    "isDeleted": "0"
*				},
*				{
*				    "id": "3",
*				    "name": "Electrical",
*				    "isDeleted": "0"
*				},
*				{
*				    "id": "4",
*				    "name": "Testing",
*				    "isDeleted": "0"
*				}
*			    ]
*			}
*		  "rels": []
*		}
* @apiError RecordNotFound .
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 No Projects Found
*     {
*       "error": ["No Projects Found"]
*     }
*
*/
