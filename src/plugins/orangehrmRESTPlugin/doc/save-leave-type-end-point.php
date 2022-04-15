/**
* @api {post} /leave/type 01.Save Leave Type
* @apiName saveLeaveType
* @apiGroup Leave
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam   {String} name Mandatory leave type name.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully Saved"
*      }
*
* @apiError No-Records Found.
*
*
* @apiError Bad-Response Saving Failed.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 400 Bad Request
*     {
*       "error": ["Saving Failed"]
*     }
*/
