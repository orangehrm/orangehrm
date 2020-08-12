<?php
/**
 * @api {get} /leave/leave-list 05.Get Leave List
 * @apiName getLeaveList
 * @apiGroup User
 * @apiVersion 1.1.0
 * @apiUse UserDescription
 *
 * @apiParam {Date} [fromDate] From date (default current leave period from date)
 * @apiParam {Date} [toDate] To date (default current leave period to date)
 * @apiParam {String} [employeeName] Employee name
 * @apiParam {String='true','false'} [rejected] Leave status rejected
 * @apiParam {String='true','false'} [cancelled] Leave status cancelled
 * @apiParam {String='true','false'} [pendingApproval] Leave status pending approval
 * @apiParam {String='true','false'} [scheduled] Leave status scheduled
 * @apiParam {String='true','false'} [taken] Leave status taken
 * @apiParam {String='true','false'} [pastEmployee] Past employee results
 * @apiParam {Number} [subunit] Employee subunit id
 * @apiParam {Number} [page] Page number
 * @apiParam {Number} [limit] Leave record limit
 *
 * @apiSuccess {Object[]} data Leave requests array
 * @apiSuccess {String} data.employeeId Employee id
 * @apiSuccess {String} data.employeeName Employee name
 * @apiSuccess {String} data.leaveRequestId Leave request id
 * @apiSuccess {Date} data.fromDate From date
 * @apiSuccess {Date} data.toDate To date
 * @apiSuccess {Date} data.appliedDate Applied date
 * @apiSuccess {String} data.leaveBalance Leave balance
 * @apiSuccess {String} data.numberOfDays No of days
 * @apiSuccess {String} data.leaveBreakdown Leave breakdown string
 * @apiSuccess {Object} data.leaveType Leave type
 * @apiSuccess {String} data.leaveType.type Leave type name
 * @apiSuccess {String} data.leaveType.id Leave type id
 *
 * @apiSuccessExample Success-Response:
 * HTTP/1.1 200 OK
 *
 * {
 *   "data": [
 *       {
 *         "employeeId": "4",
 *         "employeeName": "Kevin Mathews",
 *         "leaveRequestId": "8",
 *         "fromDate": "2020-07-16",
 *         "toDate": "2020-07-21",
 *         "appliedDate": "2020-07-16",
 *         "leaveBalance": "10.00",
 *         "numberOfDays": "3.00",
 *         "leaveBreakdown": "Scheduled(2.00)",
 *         "leaveType": {
 *           "type": "Annual",
 *           "id": "2"
 *         }
 *       },
 *       {
 *         "employeeId": "5",
 *         "employeeName": "Linda Jane",
 *         "leaveRequestId": "3",
 *         "fromDate": "2020-07-15",
 *         "toDate": "2020-07-15",
 *         "appliedDate": "2020-07-15",
 *         "leaveBalance": "2.00",
 *         "numberOfDays": "0.50",
 *         "leaveBreakdown": "Pending Approval(0.50)",
 *         "leaveType": {
 *           "type": "Casual",
 *           "id": "3"
 *         }
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
 * HTTP/1.1 404 Employee Not Found
 * {
 *   "error": ["Employee Not Found"]
 * }
 *
 */
