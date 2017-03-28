/**
* @api {post} /employee/:id/leave-request Save Leave Request
* @apiName saveLeaveRequest
* @apiGroup Leave
* @apiVersion 0.1.0
*
* @apiParam   {Number} empId Employee id.
* @apiParam   {String} type Mandatory leave type name.
* @apiParam   {Date} fromDate Leave start date.
* @apiParam   {Date} toDate Leave end date.
* @apiParam   {String} comment Leave comment.
* @apiParam   {String} partialOption Partial day option ('all','start','end' ).
* @apiParam   {String} singleType Single day leave applying type ('half_day','full_day','specify_time').
* @apiParam   {String} singleAMPM Half day morning or evening ( 'AM','PM').
* @apiParam   {String} singleFromTime Single day from time for specify time.
* @apiParam   {String} singleToTime Single day to time for specify time.
* @apiParam   {String} startType Start day leave applying type ('half_day','full_day','specify_time').
* @apiParam   {String} startAMPM Half day morning or evening ( 'AM','PM').
* @apiParam   {String} startFromTime Start day from time for specify time.
* @apiParam   {String} startToTime Start day to time for specify time.
* @apiParam   {String} endDayType End day leave applying type ('half_day','full_day','specify_time').
* @apiParam   {String} endDayAMPM Half day morning or evening ( 'AM','PM').
* @apiParam   {String} endDayFromTime End day from time for specify time.
* @apiParam   {String} endDayToTime End day to time for specify time.
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
