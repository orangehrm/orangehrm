<?php
/**
 * @api {get} /attendance/records 04.Get Attendance Records
 * @apiName getAttendance
 * @apiGroup User-Attendance
 * @apiVersion 1.2.0
 * @apiUse UserDescription_47
 *
 * @apiParam {String} [fromDate] From date (Default: Timesheet Period start date, Format: Y-m-d H:i:s, e.g. 2020-05-20 00:00:00)
 * @apiParam {String} [toDate] To date (Default: Timesheet Period end date, Format: Y-m-d H:i:s, e.g. 2020-05-26 23:59:59)
 * @apiParam {String} [empNumber] Employee Number
 *
 *
 * @apiSuccess {Object[]} data Attendance array
 * @apiSuccess {String} data.id Attendance Id
 * @apiSuccess {String} data.punchInUtcTime Punch In UTC Time
 * @apiSuccess {String} data.punchInNote Punch In Note
 * @apiSuccess {String} data.punchInTimeOffset Punch In Time Offset
 * @apiSuccess {String} data.punchInUserTime Punch In User Time
 * @apiSuccess {String} data.punchOutUtcTime Punch Out UTC Time
 * @apiSuccess {String} data.punchOutNote Punch Out Note
 * @apiSuccess {String} data.punchOutTimeOffset Punch Out Time Offset
 * @apiSuccess {String} data.punchOutUserTime Punch Out User Time
 * @apiSuccess {String} data.state Punch Status
 *


 * @apiSuccessExample Success-Response:
 * HTTP/1.1 200 OK
 *
 * {
 *   "data": [
 *       {
 *           "id": "18",
 *           "punchInUtcTime": "2020-11-26 10:05:00",
 *           "punchInNote": "PUNCH IN NOTE",
 *           "punchInTimeOffset": "5.5",
 *           "punchInUserTime": "2020-11-26 15:35:00",
 *           "punchOutUtcTime": "2020-11-26 12:29:00",
 *           "punchOutNote": "PUNCH OUT NOTE",
 *           "punchOutTimeOffset": "5.5",
 *           "punchOutUserTime": "2020-11-26 17:59:00",
 *           "state": "PUNCHED OUT"
 *       },
 *       {
 *           "id": "19",
 *           "punchInUtcTime": "2020-11-26 10:05:00",
 *           "punchInNote": "PUNCH IN NOTE",
 *           "punchInTimeOffset": "5.5",
 *           "punchInUserTime": "2020-11-26 15:35:00",
 *           "punchOutUtcTime": "2020-11-26 12:29:00",
 *           "punchOutNote": "PUNCH OUT NOTE",
 *           "punchOutTimeOffset": "5.5",
 *           "punchOutUserTime": "2020-11-26 17:59:00",
 *           "state": "PUNCHED OUT"
 *        }
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
 *
 *
 */
