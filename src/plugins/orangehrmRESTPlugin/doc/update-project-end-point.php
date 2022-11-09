/**
* @api {put} /project 11.Update Project
* @apiName updateProject
* @apiGroup Time
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam  {Number} projectId  Project id.
* @apiParam  {Number} customerId  Customer id.
* @apiParam  {String} name  Project name.
* @apiParam  {String} description  Description.
* @apiParam  {String} adminIds project admin ids ( add up to 5 admin ids with comma separated ex 1,2,45,)
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
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["Customer Id Needed"]
*     }
*
* @apiError Invalid Parameter.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["Project Name Needed"]
*     }
*
*/


