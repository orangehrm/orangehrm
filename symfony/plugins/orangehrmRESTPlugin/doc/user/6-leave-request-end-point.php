<?php
/**
 * @api {get} /leave/leave-request/:id 06.Get Leave Request
 * @apiName getLeaveRequestById
 * @apiGroup User
 * @apiVersion 1.1.0
 * @apiUse UserDescription
 *
 * @apiParam {Number} id Leave request id
 *
 * @apiSuccess {Object} data Leave request
 * @apiSuccess {String} data.employeeId Employee id
 * @apiSuccess {String} data.employeeName Employee name
 * @apiSuccess {String} data.leaveRequestId Leave request id
 * @apiSuccess {Date} data.fromDate From date
 * @apiSuccess {Date} data.toDate To date
 * @apiSuccess {Date} data.appliedDate Applied date
 * @apiSuccess {String} data.leaveBalance Leave balance
 * @apiSuccess {String} data.numberOfDays No of days
 * @apiSuccess {String} data.leaveBreakdown Leave breakdown string
 * @apiSuccess {Object[]} data.comments Leave request comments
 * @apiSuccess {String} data.comments.user Employee name
 * @apiSuccess {Date} data.comments.date Commented date
 * @apiSuccess {String} data.comments.time Commented time
 * @apiSuccess {String} data.comments.comment Comment
 * @apiSuccess {Object[]} data.days Leaves
 * @apiSuccess {Date} data.days.date Leave date
 * @apiSuccess {String="REJECTED","CANCELLED","PENDING APPROVAL","SCHEDULED","TAKEN","WEEKEND","HOLIDAY"} data.days.status Leave status
 * @apiSuccess {String} data.days.duration Duration (eg. 4.00)
 * @apiSuccess {String} data.days.durationString Duration as string (eg.(09:00 - 13:00))
 * @apiSuccess {Object[]} data.days.comments Leave comments
 * @apiSuccess {String} data.days.comments.user Employee name
 * @apiSuccess {Date} data.days.comments.date Commented date
 * @apiSuccess {String} data.days.comments.time Commented time
 * @apiSuccess {String} data.days.comments.comment Comment
 * @apiSuccess {Object} data.leaveType Leave type
 * @apiSuccess {String} data.leaveType.type Leave type name
 * @apiSuccess {String} data.leaveType.id Leave type id
 * @apiSuccess {String[]='Approve','Reject','Cancel'} data.allowedActions Allowed actions on this leave request
 *
 * @apiSuccessExample Success-Response:
 * HTTP/1.1 200 OK
 *
 * {
 *   "data": {
 *         "employeeId": "4",
 *         "employeeName": "Kevin Mathews",
 *         "leaveRequestId": "8",
 *         "fromDate": "2020-07-16",
 *         "toDate": "2020-07-21",
 *         "appliedDate": "2020-07-16",
 *         "leaveBalance": "10.00",
 *         "numberOfDays": "3.00",
 *         "comments": {
 *           "user": "Kevin Mathews",
 *           "date": "2020-06-25",
 *           "time": "17:23:03",
 *           "comment": "Comment"
 *         },
 *         "days": [
 *           {
 *             "date": "2020-07-20",
 *             "status": "SCHEDULED",
 *             "duration": "8.00",
 *             "durationString": "",
 *             "comments": []
 *           },
 *           {
 *             "date": "2020-07-19",
 *             "status": "WEEKEND",
 *             "duration": "0.00",
 *             "durationString": "",
 *             "comments": []
 *           },
 *           {
 *             "date": "2020-07-18",
 *             "status": "WEEKEND",
 *             "duration": "0.00",
 *             "durationString": "",
 *             "comments": []
 *           },
 *           {
 *             "date": "2020-07-17",
 *             "status": "SCHEDULED",
 *             "duration": "8.00",
 *             "durationString": "",
 *             "comments": []
 *           }
 *         ],
 *         "leaveBreakdown": "Scheduled(2.00)",
 *         "leaveType": {
 *           "type": "Annual",
 *           "id": "2"
 *         },
 *         "allowedActions": ["Cancel"]
 *       }
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
 * HTTP/1.1 404 Employee Not Found
 * {
 *   "error": ["Employee Not Found"]
 * }
 *
 */
