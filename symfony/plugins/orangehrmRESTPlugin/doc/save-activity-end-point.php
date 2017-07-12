/**
* @api {post} /activity 6.Save Activity
* @apiName saveActivity
* @apiGroup Time
* @apiVersion 0.1.0
*
*
* @apiSuccess  {Number} projectId  Project id.
* @apiSuccess  {String} name  Activity name.
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully Saved"
*      }
*
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
