/**
* @api {put} /employee/:id Update Employee detail
* @apiName UpdateEmployeeDetails
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiParam  {String} firstName First Name.
* @apiParam  {String} middleName  Middle Name.
* @apiParam  {String} lastName  LastName.
* @apiParam  {String} startDate Employee contract start date.
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
