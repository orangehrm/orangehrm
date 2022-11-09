/**
* @api {post} /employee/:id/work-experience 22.Save Employee Work Experience
* @apiName saveEmployeeWorkExperience
* @apiGroup Employee
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam {Number}  id Employee id.
*
* @apiParam  {String} company Company name.
* @apiParam  {String} title job Title.
* @apiParam  {Date} fromDate Experience from date.
* @apiParam  {Date} toDate Experience to date.
* @apiParam  {String} comment Work experience comment.
* @apiSuccess {Object} Data Success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*   {
*
*   "success":"Successfully Saved",
*   "seqId":3
*
*   }
*
* @apiError UserNotFound The id of the employee was not found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Not Found
*     {
*       "error": "Employee Not Found"
*     }
*
*
*/
