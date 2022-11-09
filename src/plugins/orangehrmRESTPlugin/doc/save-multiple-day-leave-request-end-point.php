/**
* @api {post} /employee/:id/leave-request 08.Save Multiple Day Leave Request
* @apiName saveMultipleDayLeaveRequest
* @apiGroup Leave
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam   {Number} id Employee id.
* @apiParam   {Number} type Mandatory leave type id.
* @apiParam   {Date} fromDate Leave start date.
* @apiParam   {Date} toDate Leave end date.
* @apiParam   {String} [comment] Leave comment.
* @apiParam   {String} partialOption Partial day option ( required ) ('all','start','end','start_end','none' ).
* Note : If partial option is 'all'  start day fields must be filled.
*        If partial option is 'end'  end day fields must be filed.
*        If partial option is 'start'  start day fields must be filed.
*        If partial option is 'start_end'  start and end day fields must be filed.
*        If partial option is 'none'  No partial option.
* @apiParam   {String} startDayType Start day leave applying type ('half_day','full_day','specify_time').
* @apiParam   {String} startDayAMPM Half day morning or evening ( 'AM','PM') required for  start day 'half_day'.
* @apiParam   {String} startDayFromTime Start day from time for specify time(required for start day specifying time ).
* @apiParam   {String} startDayToTime Start day to time for specify time(required for start day specifying time ).
* @apiParam   {String} endDayType End day leave applying type ('half_day','full_day','specify_time').
* @apiParam   {String} endDayAMPM Half day morning or evening ( 'AM','PM')required for end day 'half_day'.
* @apiParam   {String} endDayFromTime End day from time for specify time(required for end day specifying time ).
* @apiParam   {String} endDayToTime End day to time for specify time (required for end day specifying time ).
* @apiParam   {String} action Leave action type ( "SCHEDULED""PENDING""REJECTED""CANCELLED").
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
*/
