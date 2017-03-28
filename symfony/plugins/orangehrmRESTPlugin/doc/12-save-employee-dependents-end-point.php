/**
* @api {post} /employee/:id/dependent 12.Save Employee Dependent
* @apiName saveEmployee dependents
* @apiGroup Employee
* @apiVersion 0.1.0
*
* @apiParam {Number}  id Employee id.
*
* @apiParam {String} name Mandatory name of the dependent.
* @apiParam {String} relationship  Mandatory relationship of the dependent.
* @apiParam {String} dob DOB of dependent.
* @apiSuccess {Object} Data Success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully Saved",
*        "sequenceNumber": 1
*      }
*
* @apiError Bad-Response Saving Failed.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 400 Bad Request
*     {
*       "error": ["Saving Failed"]
*     }
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["Invalid Parameter"]
*     }
*/
