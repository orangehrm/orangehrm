/**
* @api {put} /activity 7.Update Activity
* @apiName updateActivity
* @apiGroup Time
* @apiVersion 0.1.0
*
*
* @apiParam  {Number} projectId  Project id.
* @apiParam  {Number} activityId  Activity id.
* @apiParam  {String} name  Activity name.
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully Updated"
*      }
*
* @apiError Invalid Parameter.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Activity Name Already Exists
*     {
*       "error": ["Activity Name Already Exists"]
*     }
* @apiError RecordNotFound.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 204 No Project Found
*     {
*       "error": ["No Projects Found"]
*     }
*
*/
