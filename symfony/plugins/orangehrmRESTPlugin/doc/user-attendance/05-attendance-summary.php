<?php
/**
 * @api {get} /attendance/summary 05.Get Attendance Summary
 * @apiName getAttendanceSummary
 * @apiGroup User-Attendance
 * @apiVersion 1.2.0
 * @apiUse UserDescription_47
 *
 * @apiParam {Date} [fromDate] From date (Default: Timesheet Period start date, Format: Y-m-d H:i:s, e.g. 2020-05-20 00:00:00)
 * @apiParam {Date} [toDate] To date (Default: Timesheet Period end date, Format: Y-m-d H:i:s, e.g. 2020-05-26 23:59:59)
 * @apiParam {String} [empNumber] Employee Number
 * @apiParam {String} [pendingApproval] True or False
 * @apiParam {String} [scheduled] True or False
 * @apiParam {String} [taken] True or False
 * @apiParam {String} [rejected] True or False
 * @apiParam {String} [cancelled] True or False
 *
 *
 * @apiSuccess {String} data.totalWorkHours Total Work Duration Of The Week
 * @apiSuccess {String} data.totalLeaveHours Total Leave Duration Of The Week
 * @apiSuccess {Object[]} data.totalLeaveTypeHours Total Leave Duration of Each Leave Type
 * @apiSuccess {String} totalLeaveTypeHours.typeId Leave Type Id of totalLeaveTypeHours
 * @apiSuccess {String} totalLeaveTypeHours.type: Leave Type Name of totalLeaveTypeHours
 * @apiSuccess {String} totalLeaveTypeHours.hours Leave duration of totalLeaveTypeHours
 * @apiSuccess {Object[]} data.workSummary  Work Summary of the Week
 * @apiSuccess {Object[]} workSummary.sunday Work Summary of Sunday
 * @apiSuccess {Object[]} workSummary.monday Work Summary of Monday
 * @apiSuccess {Object[]} workSummary.tuesday Work Summary of Tuesday
 * @apiSuccess {Object[]} workSummary.wednesday Work Summary of Wednesday
 * @apiSuccess {Object[]} workSummary.thursday Work Summary of Thursday
 * @apiSuccess {Object[]} workSummary.friday Work Summary of Friday
 * @apiSuccess {Object[]} workSummary.saturday Work Summary of Saturday
 * @apiSuccess {String} tuesday.workHours Work Hours of each day
 * @apiSuccess {Object[]} tuesday.leave Leaves Leave Records of each day
 * @apiSuccess {String} leave.typeId Leave Id of each Leave Record
 * @apiSuccess {String} leave.type Leave Type of each Leave Record
 * @apiSuccess {String} leave.hours Leave Duration of each Leave Record
 *
 * @apiSuccessExample Success-Response:
 * HTTP/1.1 200 OK
 *
 * {
 *   "data": [
 *       "totalWorkHours": "6.80",
 *        "totalLeaveHours": "24.00",
 *       "totalLeaveTypeHours": [
 *           {
 *               "typeId": "1",
 *               "type": "Medical",
 *               "hours": "16.00"
 *           },
 *           {
 *               "typeId": "2",
 *               "type": "Casual",
 *               "hours": "8.00"
 *           }
 *       ],
 *        "workSummary": {
 *           "sunday": {
 *               "workHours": 0,
 *               "leave": []
 *           },
 *           "monday": {
 *               "workHours": 0,
 *               "leave": [
 *                   {
 *                      "typeId": "1",
 *                       "type": "Medical",
 *                       "hours": "8.00"
 *                   }
 *               ]
 *           },
 *           "tuesday": {
 *               "workHours": 0,
 *               "leave": []
 *           },
 *           "wednesday": {
 *               "workHours": 0,
 *               "leave": []
 *           },
 *           "thursday": {
 *               "workHours": "2.40",
 *               "leave": [
 *                    {
 *                       "typeId": "1",
 *                       "type": "Medical",
 *                       "hours": "4.00"
 *                   }
 *               ]
 *           },
 *           "friday": {
 *               "workHours": 0,
 *               "leave": [
 *                   {
 *                       "typeId": "2",
 *                       "type": "Casual",
 *                       "hours": "8.00"
 *                   }
 *               ]
 *           },
 *           "saturday": {
 *               "workHours": "4.40",
 *               "leave": [
 *                   {
 *                       "typeId": "1",
 *                       "type": "Medical",
 *                       "hours": "4.00"
 *                   }
 *               ]
 *           }
 *       }
 *   ],
 *   "rels": []
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
