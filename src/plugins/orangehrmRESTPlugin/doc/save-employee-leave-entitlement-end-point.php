/**
* @api {post} /employee/:id/entitlement 04.Save Employee Leave Entitlement
* @apiName saveEmployeeLeaveEntitlement
* @apiGroup Leave
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam {Number} id Employee id.
* @apiParam {Number} leaveType Leave type id.
* @apiParam {Date} fromDate From date.
* @apiParam {Date} toDate To date.
* @apiParam {Number} days Number of days.
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully Saved"
*      }
*
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
