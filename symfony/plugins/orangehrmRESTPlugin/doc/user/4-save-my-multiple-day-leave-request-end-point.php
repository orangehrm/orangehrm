<?php
/**
 * @api {post} /leave/my-leave-request 04.Save My Leave Request (Multiple Day)
 * @apiName saveMyLeaveRequestMultipleDay
 * @apiGroup User
 * @apiVersion 1.1.0
 * @apiUse UserDescription
 *
 * @apiUse UserLeaveRequestSingleDayMultipleDay
 *
 */

/**
 * @apiDefine UserLeaveRequestSingleDayMultipleDay
 * @apiParam {Number} type Mandatory leave type id
 * @apiParam {Date} fromDate Leave start date
 * @apiParam {Date} toDate Leave end date
 * @apiParam {String} [comment] Leave comment
 * @apiParam {String='all', 'start', 'end', 'start_end', 'none'} partialOption Partial day option
 *
 * Note :
 * If partial option is 'all'  start day fields must be filled.
 * If partial option is 'end'  end day fields must be filed.
 * If partial option is 'start'  start day fields must be filed.
 * If partial option is 'start_end'  start and end day fields must be filed.
 * If partial option is 'none'  No partial option.
 *
 * @apiParam {String='half_day','full_day','specify_time'} startDayType Start day leave applying type
 * @apiParam {String='AM','PM'} startDayAMPM Half day morning or evening, required for  start day 'half_day'
 * @apiParam {String} startDayFromTime Start day from time for specify time(required for start day specifying time )
 * @apiParam {String} startDayToTime Start day to time for specify time(required for start day specifying time )
 * @apiParam {String='half_day','full_day','specify_time'} endDayType End day leave applying type
 * @apiParam {String='AM','PM'} endDayAMPM Half day morning or evening, required for end day 'half_day'
 * @apiParam {String} endDayFromTime End day from time for specify time(required for end day specifying time )
 * @apiParam {String} endDayToTime End day to time for specify time (required for end day specifying time )
 *
 * @apiSuccessExample Success-Response:
 * HTTP/1.1 200 OK
 *
 * {
 *    "success": "Successfully Saved"
 * }
 *
 *
 * @apiError Bad-Response Saving Failed.
 *
 * @apiErrorExample Error-Response:
 * HTTP/1.1 400 Bad Request
 * {
 *    "error": ["Saving Failed"]
 * }
 *
 */
