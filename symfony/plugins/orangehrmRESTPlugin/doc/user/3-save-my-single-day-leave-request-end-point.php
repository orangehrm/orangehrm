<?php
/**
 * @api {post} /leave/my-leave-request 03.Save My Leave Request (Single Day)
 * @apiName saveMyLeaveRequestSingleDay
 * @apiGroup User
 * @apiVersion 1.1.0
 * @apiUse UserDescription
 *
 * @apiUse UserLeaveRequestSingleDay
 *
 */

/**
 * @apiDefine UserLeaveRequestSingleDay
 * @apiParam {Number} type Mandatory leave type id
 * @apiParam {Date} fromDate Leave start date
 * @apiParam {Date} toDate Leave end date
 * @apiParam {String} [comment] Leave comment
 * @apiParam {String='half_day','full_day','specify_time'} singleType Single day leave applying type
 * @apiParam {String='AM','PM'} singleAMPM Half day morning or evening, (required for 'half_day')
 * @apiParam {String} singleFromTime Single day from time for specify time ( required if specifying time )
 * @apiParam {String} singleToTime Single day to time for specify time ( required if specifying time )
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
 */
