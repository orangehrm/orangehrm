<?php
/**
 * @api {get} /leave/my-leave-request 02.Get My Leave Requests
 * @apiName myLeaveRequests
 * @apiGroup User
 * @apiVersion 1.1.0
 * @apiUse UserDescription
 *
 * @apiParam {Date}  [fromDate] From date
 * @apiParam {Date}  [toDate] To date
 * @apiParam {Number}  [page] Page number
 * @apiParam {Number}  [limit] Leave record limit
 *
 * @apiSuccess {Object[]} data Leave requests array
 * @apiSuccess {String} data.id Leave request id
 * @apiSuccess {Date} data.fromDate From date
 * @apiSuccess {Date} data.toDate To date
 * @apiSuccess {Date} data.appliedDate Applied date
 * @apiSuccess {Object} data.leaveType Leave type
 * @apiSuccess {String} data.leaveType.type Leave type name
 * @apiSuccess {String} data.leaveType.id Leave type id
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
 *
 * @apiSuccessExample Success-Response:
 * HTTP/1.1 200 OK
 *
 * {
 *   "data": [
 *       {
 *         "id": "8",
 *         "fromDate": "2020-07-16",
 *         "toDate": "2020-07-21",
 *         "appliedDate": "2020-07-16",
 *         "leaveType": {
 *           "type": "Annual",
 *           "id": "2"
 *         },
 *         "leaveBalance": "10.00",
 *         "numberOfDays": "3.00",
 *         "comments": {
 *           "user": "Employee Name",
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
 *       },
 *       {
 *         "id": "3",
 *         "fromDate": "2020-07-15",
 *         "toDate": "2020-07-15",
 *         "appliedDate": "2020-07-15",
 *         "leaveType": {
 *           "type": "Casual",
 *           "id": "1"
 *         },
 *         "leaveBalance": "3.00",
 *         "numberOfDays": "0.50",
 *         "comments": [],
 *         "days": [
 *           {
 *             "date": "2020-07-15",
 *             "status": "PENDING APPROVAL",
 *             "duration": "4.00",
 *             "durationString": "(09:00 - 13:00)",
 *             "comments": []
 *           }
 *         ],
 *         "leaveBreakdown": "Pending Approval(0.50)",
 *       }
 *     ]
 *   ],
 *   "rels": []
 * }
 *
 * @apiError RecordNotFound No Records Found
 *
 * @apiErrorExample Error-Response:
 * HTTP/1.1 404 Record Not Found
 * {
 *   "error": {
 *     "status": "404",
 *     "text": "No Records Found"
 *   }
 * }
 *
 */
