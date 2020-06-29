<?php
/**
 * @api {get} /leave/my-leave 1.Get My Leave Entitlements & Requests
 * @apiName myLeaveRequest
 * @apiGroup Mobile
 * @apiVersion 0.1.0
 *
 * @apiParam {Date}  [fromDate] Valid leave period from date
 * @apiParam {Date}  [toDate] Valid leave period to date
 * @apiParam {Number}  [page] Page no. (Only apply to leave requests)
 * @apiParam {Number}  [limit] Leave record limit (Only apply to leave requests)
 *
 * @apiSuccess {Object[]} entitlement Entitlements array
 * @apiSuccess {String} entitlement.id Entitlement id
 * @apiSuccess {Date} entitlement.validFrom From date
 * @apiSuccess {Date} entitlement.validTo To date
 * @apiSuccess {Date} entitlement.creditedDate Credited date
 * @apiSuccess {Object} entitlement.leaveBalance Leave balance object
 * @apiSuccess {Number} entitlement.leaveBalance.entitled Entitlement
 * @apiSuccess {Number} entitlement.leaveBalance.used Used leave
 * @apiSuccess {Number} entitlement.leaveBalance.scheduled Scheduled leave
 * @apiSuccess {Number} entitlement.leaveBalance.pending Pending leave
 * @apiSuccess {Number} entitlement.leaveBalance.notLinked
 * @apiSuccess {Number} entitlement.leaveBalance.taken Leave taken
 * @apiSuccess {Number} entitlement.leaveBalance.adjustment
 * @apiSuccess {Number} entitlement.leaveBalance.balance Leave balance
 * @apiSuccess {Object} entitlement.leaveType Leave type object
 * @apiSuccess {String} entitlement.leaveType.type Leave type
 * @apiSuccess {String} entitlement.leaveType.id Leave type id
 * @apiSuccess {Object[]} leaveRequest Leave requests array
 * @apiSuccess {String} leaveRequest.id Leave request id
 * @apiSuccess {Date} leaveRequest.fromDate From date
 * @apiSuccess {Date} leaveRequest.toDate To date
 * @apiSuccess {Date} leaveRequest.appliedDate Applied date
 * @apiSuccess {String} leaveRequest.leaveType Leave type
 * @apiSuccess {String} leaveRequest.numberOfDays No of days
 * @apiSuccess {Object[]} leaveRequest.comments Leave comments
 * @apiSuccess {String} leaveRequest.comments.user Employee name
 * @apiSuccess {Date} leaveRequest.comments.date Commented date
 * @apiSuccess {String} leaveRequest.comments.time Commented time
 * @apiSuccess {String} leaveRequest.comments.comment Comment
 * @apiSuccess {Object[]} leaveRequest.days Leaves
 * @apiSuccess {Date} leaveRequest.days.date Leaves
 * @apiSuccess {String="REJECTED","CANCELLED","PENDING APPROVAL","SCHEDULED","TAKEN","WEEKEND","HOLIDAY"} leaveRequest.days.status Leave status
 * @apiSuccess {Date} leaveRequest.days.duration Duration (eg. 4.00)
 * @apiSuccess {String} leaveRequest.days.durationString Duration as string (eg.(09:00 - 13:00))
 * @apiSuccess {Object[]} leaveRequest.days.comments Leaves
 * @apiSuccess {String} leaveRequest.days.comments.user Employee name
 * @apiSuccess {Date} leaveRequest.days.comments.date Commented date
 * @apiSuccess {String} leaveRequest.days.comments.time Commented time
 * @apiSuccess {String} leaveRequest.days.comments.comment Comment
 *
 * @apiSuccessExample Success-Response:
 * HTTP/1.1 200 OK
 *
 * {
 *   "data": {
 *     "entitlement": [
 *       {
 *         "id": "1",
 *         "validFrom": "2020-01-01",
 *         "validTo": "2020-12-31",
 *         "creditedDate": "2020-06-25",
 *         "leaveBalance": {
 *           "entitled": 3,
 *           "used": 1,
 *           "scheduled": 0.5,
 *           "pending": 0.5,
 *           "notLinked": 0,
 *           "taken": 0,
 *           "adjustment": 0,
 *           "balance": 2
 *         },
 *         "leaveType": {
 *           "type": "Annual",
 *           "id": "2"
 *         }
 *       },
 *       {
 *         "id": "2",
 *         "validFrom": "2020-01-01",
 *         "validTo": "2020-12-31",
 *         "creditedDate": "2020-06-20",
 *         "leaveBalance": {
 *           "entitled": 2,
 *           "used": 0.5,
 *           "scheduled": 0.5,
 *           "pending": 0,
 *           "notLinked": 0,
 *           "taken": 0,
 *           "adjustment": 0,
 *           "balance": 1.5
 *         },
 *         "leaveType": {
 *           "type": "Casual",
 *           "id": "3"
 *         }
 *       }
 *     ],
 *     "leaveRequest": [
 *       {
 *         "id": "8",
 *         "fromDate": "2020-07-16",
 *         "toDate": "2020-07-21",
 *         "appliedDate": "2020-07-16",
 *         "type": "Annual",
 *         "numberOfDays": "3.00",
 *         "comments": {
 *           "user": "rajitha kumara",
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
 *   },
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
 *
 */
