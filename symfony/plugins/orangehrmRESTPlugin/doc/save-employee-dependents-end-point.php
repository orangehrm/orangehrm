/**
* @api {post} /employee/:id Save Employee Dependents
* @apiName saveEmployee dependents
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiParam {String} name name of the dependent.
* @apiParam {String} relationship  relationship of the dependent.
* @apiParam {String} dob  date of birth of dependent.
* @apiParam {String} type  relationship type.
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
