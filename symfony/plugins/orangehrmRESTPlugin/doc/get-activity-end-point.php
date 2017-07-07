/**
* @api {get} /activity 5.Get Activities
* @apiName getActivities
* @apiGroup Time
* @apiVersion 0.1.0
*
* @apiParam    {Number} id  Project id.
* @apiSuccess  {Number} activityId  Activity id.
* @apiSuccess  {Number} projectId  Project id.
* @apiSuccess  {String} name  Activity name.
* @apiSuccess  {String} is_deleted  Is deleted( 1 = true /0 = false).
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*		{
*		  "data": [
*		    {
*		      "activityId": "2",
*		      "projectId": "1",
*		      "is_deleted": "0",
*		      "name": "test activity"
*		    },
*		    {
*		      "activityId": "3",
*		      "projectId": "1",
*		      "is_deleted": "0",
*		      "name": "test activity2"
*		    }
*
*		  ],
*		  "rels": []
*		}
* @apiError RecordNotFound .
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 No Customers Found
*     {
*       "error": ["No Customers Found"]
*     }
* @apiError RecordNotFound .
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 204 No Project Found
*     {
*       "error": ["No Projects Found"]
*     }
*
*/
