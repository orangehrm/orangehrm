<?php
/**
 * @api {get} /attendance/attendance-list 06.Get Attendance List
 * @apiName getAttendanceList
 * @apiGroup User-Attendance
 * @apiVersion 1.2.0
 * @apiUse UserDescription_47
 *
 * @apiParam {Date} [fromDate] From date (Default: Timesheet Period start date, Format: Y-m-d H:i:s, e.g. 2020-05-20 00:00:00)
 * @apiParam {Date} [toDate] To date (Default: Timesheet Period end date, Format: Y-m-d H:i:s, e.g. 2020-05-26 23:59:59)
 * @apiParam {String} [empNumber] Employee number
 * @apiParam {String='true','false'} [pastEmployee] Past employee results
 * @apiParam {String='true','false'} [all] With Zero duration results
 * @apiParam {String='true','false'} [includeSelf] Include self when getting list. This parameter ignore when `empNumber` parameter passed
 *
 * @apiSuccess {Object[]} data Leave requests array
 * @apiSuccess {String} data.employeeId Employee id
 * @apiSuccess {String} data.employeeName Employee name
 * @apiSuccess {String} data.code Employee code
 * @apiSuccess {String} data.jobTitle Job title
 * @apiSuccess {String} data.unit Sub unit
 * @apiSuccess {String} data.status Employment status
 * @apiSuccess {String} data.duration Work duration
 *
 * @apiSuccessExample Success-Response:
 * HTTP/1.1 200 OK
 *
 * {
 *   "data": [
 *       {
 *         "employeeId": "4",
 *         "employeeName": "Kevin Mathews",
 *         "code": "0004",
 *         "jobTitle": "SE",
 *         "unit": "Development",
 *         "status": "Full Time",
 *         "duration": "8:40",
 *       },
 *       {
 *         "employeeId": "5",
 *         "employeeName": "Linda Jane",
 *         "code": "0005",
 *         "jobTitle": null,
 *         "unit": null,
 *         "status": null,
 *         "duration": "4:00",
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
 * @apiError Employee Not Found.
 *
 * @apiErrorExample Error-Response:
 * HTTP/1.1 400 Employee Not Found
 * {
 *   "error": ["Employee Not Found"]
 * }
 *
 */
