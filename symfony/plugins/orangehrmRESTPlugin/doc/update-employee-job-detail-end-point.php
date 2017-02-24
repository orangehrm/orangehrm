/**
* @api {put} /employee/:id/job-detail Update Employee Job Detail
* @apiName updateEmployeeJobDetails
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiParam  {String} title Job Optional title name.
* @apiParam  {String} category  Optional Job category.
* @apiParam  {String} joinedDate  Optional Employee joined date.
* @apiParam  {String} startDate Optional Employee contract start date.
* @apiSuccess {String} Object data success response.
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
*     HTTP/1.1 400 Bad Request
*     {
*       "error": ["Saving failed"]
*     }
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["invalid Parameter"]
*     }
*/
