/**
* @api {delete} /project 12.Delete Project
* @apiName deleteProject
* @apiGroup Time
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam  {Number} projectId  Project id.
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully Deleted"
*      }
*
*
* @apiError Invalid Parameter.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["Project ID Needed"]
*     }
*
* @apiError Record Not Found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Project Not Found
*     {
*       "error": ["Project Not Found"]
*     }
*
*/
