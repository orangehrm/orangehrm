/**
* @api {post} /project 1.Save Project
* @apiName saveProjects
* @apiGroup Time
* @apiVersion 0.1.0
*
* @apiSuccess  {Number} customerId  Customer id.
* @apiSuccess  {String} name  Project name.
* @apiSuccess  {String} description  Description.
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
