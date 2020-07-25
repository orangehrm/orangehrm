/**
* @api {post} /employee/:id/leave-request 06.Save Leave Single Day Request
* @apiName saveLeaveRequest
* @apiGroup Leave
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam   {Number} id Employee id.
* @apiParam   {Number} type Mandatory leave type id.
* @apiParam   {Date} fromDate Leave start date.
* @apiParam   {Date} toDate Leave end date.
* @apiParam   {String} [comment] Leave comment.
* @apiParam   {String} singleType Single day leave applying type ('half_day','full_day','specify_time').
* @apiParam   {String} singleAMPM Half day morning or evening ( 'AM','PM') (required for 'half_day').
* @apiParam   {String} singleFromTime Single day from time for specify time ( required if specifying time ).
* @apiParam   {String} singleToTime Single day to time for specify time ( required if specifying time ).
* @apiParam   {String} action Leave action type ( "SCHEDULED""PENDING""REJECTED""CANCELLED").
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
*/
