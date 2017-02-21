/**
* @api {post} employee/:id/action/terminate Terminate employment
* @apiName terminateEmployee
* @apiGroup Employee
*
* @apiParam {Number}  employee id
*
* @apiParam {String} Mandatory termination date.
* @apiParam {String} Mandatory termination reason.
* @apiParam {String} note  Optional termination note.
* @apiSuccess {Object} data success response
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully terminated"
*      }
*
* @apiError Bad-Response Saving failed.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 400 Bad Request
*     {
*       "error": ["Termination failed"]
*     }
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["invalid Parameter"]
*     }
*/
