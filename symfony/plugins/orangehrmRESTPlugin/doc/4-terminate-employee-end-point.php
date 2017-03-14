/**
* @api {post} employee/:id/action/terminate Terminate Employment
* @apiName terminateEmployee
* @apiGroup Employee
* @apiVersion 0.1.0
*
* @apiParam {Number}  id Employee id
*
* @apiParam {String} terminationDate Mandatory termination date.
* @apiParam {String} reason Mandatory termination reason.
* @apiParam {String} note Termination note.
* @apiSuccess {Object} Data success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully terminated"
*      }
*
* @apiError Bad-Response Saving Failed.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 400 Bad Request
*     {
*       "error": ["Termination Failed"]
*     }
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["Invalid Parameter"]
*     }
*/
