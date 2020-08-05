<?php
/**
 * @api {post} /leave/leave-request/:id 07.Change Leave Request Status
 * @apiName changeLeaveRequestStatus
 * @apiGroup User
 * @apiVersion 1.1.0
 * @apiUse UserDescription
 *
 * @apiParam {Number} id Leave request id
 * @apiParam {String='changeStatus','comment'} actionType Action type on leave request
 *
 * Note :
 * If `actionType` is "changeStatus", `status` fields must be filled.
 * If `actionType` is "comment", `comment` fields must be filled.
 *
 * @apiParam {String='Approve','Reject','Cancel'} status Status to be changed
 * @apiParam {String} comment Comment text
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
