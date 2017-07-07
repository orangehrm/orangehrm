/**
* @api {get} /project 3.Get Projects
* @apiName getProjects
* @apiGroup Time
* @apiVersion 0.1.0
*
*
* @apiSuccess  {Number} projectId  Project id.
* @apiSuccess  {Number} customerId  Customer id.
* @apiSuccess  {String} is_deleted  Is deleted or not.
* @apiSuccess  {String} name  Project name.
* @apiSuccess  {String} description  Description.
* @apiSuccess  {String} admins Project admin names.
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*		{
*		  "data": [
*		    {
*		      "projectId": "1",
*		      "customerId": "1",
*		      "is_deleted": "0",
*		      "name": "Trading time sheets",
*		      "description": ""
*             "admins": "Ninattttttt Jane Lewis (Past Employee),Hameesh Von Johnson,
*		    },
*		    {
*		      "projectId": "2",
*		      "customerId": "1",
*		      "is_deleted": "0",
*		      "name": "ed",
*		      "description": "Test"
*		    }
*		  ],
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
