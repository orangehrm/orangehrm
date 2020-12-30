<?php
/**
 * @api {get} /leave/leaves 11.Get Leaves
 * @apiName getLeave
 * @apiGroup User-Leave
 * @apiVersion 1.2.0
 * @apiUse UserDescription_47
 *
 * @apiParam {String} [fromDate] From date (Default: Current leave period from date, Format: Y-m-d, e.g. 2020-01-01)
 * @apiParam {String} [toDate] To date (Default: Current leave period to date, Format: Y-m-d, e.g. 2020-12-31)
 * @apiParam {String} [empNumber] Employee Number
 * @apiParam {String} [pendingApproval] True or False
 * @apiParam {String} [scheduled] True or False
 * @apiParam {String} [taken] True or False
 * @apiParam {String} [rejected] True or False
 * @apiParam {String} [cancelled] True or False
 *
 * @apiSuccess {Object[]} data Leave array
 * @apiSuccess {String} data.id Leave id
 * @apiSuccess {String} data.date Leave Date
 * @apiSuccess {String} data.lengthHours Leave Length in hours
 * @apiSuccess {String} data.lengthDays Leave Length in days
 * @apiSuccess {Object[]} data.leaveType Leave Type
 * @apiSuccess {String} leaveType.id Leave Type id
 * @apiSuccess {String} leaveType.type Leave Type Name
 * @apiSuccess {String} data.startTime Leave Start Time
 * @apiSuccess {String} data.endTime Leave End Time
 * @apiSuccess {String} data.status Leave Status

 * @apiSuccessExample Success-Response:
 * HTTP/1.1 200 OK
 *
 * {
 *   "data": [
 *       {
 *           "id": "10",
 *           "date": "2020-12-06",
 *           "lengthHours": "8.00",
 *           "lengthDays": "1.0000",
 *           "leaveType": {
 *              "id": "3",
 *              "type": "Annual"
 *           },
 *           "startTime": "00:00:00",
 *           "endTime": "00:00:00",
 *           "status": "TAKEN"
 *       },
 *       {
 *           "id": "11",
 *           "date": "2020-12-08",
 *           "lengthHours": "4.00",
 *           "lengthDays": "0.5000",
 *           "leaveType": {
 *              "id": "3",
 *              "type": "Medical"
 *           },
 *           "startTime": "09:00:00",
 *           "endTime": "13:00:00",
 *           "status": "TAKEN"
 *       }
 *     ]
 *   ],
 *   "rels": []
 * }
 *
 * @apiError No-Records Found.
 *
 * @apiErrorExample Error-Response:
 * HTTP/1.1 404 Record Not Found
 * {
 *   "error": ["No Records Found"]
 * }
 *
 *
 * @apiError Access Denied For Requested Employee.
 *
 * @apiErrorExample Error-Response:
 * HTTP/1.1 400 Access Denied
 * {
 *   "error": ["Access Denied"]
 * }
 *
 */
