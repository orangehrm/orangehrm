<?php
/**
 * @api {get} /leave/my-leave-request 2.Get My Leave Requests
 * @apiName myLeaveRequests
 * @apiGroup Mobile
 * @apiVersion 0.1.0
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
 * @apiSuccess {String} data.leaveType Leave type
 * @apiSuccess {String} data.numberOfDays No of days
 * @apiSuccess {Object[]} data.comments Leave comments
 * @apiSuccess {String} data.comments.user Employee name
 * @apiSuccess {Date} data.comments.date Commented date
 * @apiSuccess {String} data.comments.time Commented time
 * @apiSuccess {String} data.comments.comment Comment
 * @apiSuccess {Object[]} data.days Leaves
 * @apiSuccess {Date} data.days.date Leaves
 * @apiSuccess {String="REJECTED","CANCELLED","PENDING APPROVAL","SCHEDULED","TAKEN","WEEKEND","HOLIDAY"} data.days.status Leave status
 * @apiSuccess {Date} data.days.duration Duration (eg. 4.00)
 * @apiSuccess {String} data.days.durationString Duration as string (eg.(09:00 - 13:00))
 * @apiSuccess {Object[]} data.days.comments Leaves
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
 *         "type": "Annual",
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
 *         ]
 *       },
 *       {
 *         "id": "3",
 *         "fromDate": "2020-07-15",
 *         "toDate": "2020-07-15",
 *         "appliedDate": "2020-07-15",
 *         "type": "Casual",
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
 *         ]
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
