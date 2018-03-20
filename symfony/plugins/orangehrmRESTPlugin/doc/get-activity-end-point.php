/**
* @api {get} /activity 5.Get Activities
* @apiName getActivities
* @apiGroup Time
* @apiVersion 0.1.0
*
* @apiParam    {Number} id  Project id.
* @apiSuccess  {Number} activityId  Activity id.
* @apiSuccess  {String} name  Activity name.
* @apiSuccess  {String} isDeleted  Is deleted(1,0).
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*		{
*		  "data": [
*		    {
*		      "activityId": "2",
*		      "isDeleted": "0",
*		      "name": "test activity"
*		    },
*		    {
*		      "activityId": "3",
*		      "isDeleted": "0",
*		      "name": "test activity2"
*		    }
*
*		  ],
*		  "rels": []
*		}
*
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 No Records Found
*     {
*       "error": ["No Records Found"]
*     }
*
*/
