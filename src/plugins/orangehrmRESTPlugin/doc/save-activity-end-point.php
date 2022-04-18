/**
* @api {post} /activity 06.Save Activity
* @apiName saveActivity
* @apiGroup Time
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
*
* @apiParam  {Number} projectId  Project id.
* @apiParam  {String} name  Activity name.
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully Saved"
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
