/**
* @api {post} /employee/:id/dependent Save Employee Dependent
* @apiName saveEmployee dependents
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiParam {String} name Mandatory name of the dependent.
* @apiParam {String} relationship  Mandatory relationship of the dependent.
* @apiParam {String} dob Optional date of birth of dependent.
* @apiParam {String} type  Optional relationship type.
* @apiSuccess {Object} data success response
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
