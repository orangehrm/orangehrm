/**
* @api {post} /employee/:id/job-detail Save Employee Job Detail
* @apiName SaveEmployeeJobDetails
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiParam  {String} title  Optional Job title name.
* @apiParam  {String} category  Optional Job category.
* @apiParam  {String} joinedDate  Optional Employee joined date.
* @apiParam  {String} startDate Optional Employee contract start date.
* @apiSuccess {String} Object Optional data success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully saved"
*      }
*
* @apiError Bad-Response Saving failed.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 401 Bad Request
*     {
*       "error": ["Saving failed"]
*     }
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 401 Invalid Parameter
*     {
*       "error": ["invalid Parameter"]
*     }
*/
