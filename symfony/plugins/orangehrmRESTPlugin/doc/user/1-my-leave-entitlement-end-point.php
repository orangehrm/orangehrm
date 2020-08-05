<?php
/**
 * @api {get} /leave/my-leave-entitlement 01.Get My Leave Entitlements
 * @apiName myLeaveEntitlements
 * @apiGroup User
 * @apiVersion 1.1.0
 * @apiUse UserDescription
 *
 * @apiParam {Date}  [fromDate] Valid leave period from date
 * @apiParam {Date}  [toDate] Valid leave period to date
 *
 *@apiUse UserLeaveEntitlementsSuccess
 *
 */

/**
 * @apiDefine UserLeaveEntitlementsSuccess
 * @apiSuccess {Object[]} data Entitlements array
 * @apiSuccess {String} data.id Entitlement id
 * @apiSuccess {Date} data.validFrom From date
 * @apiSuccess {Date} data.validTo To date
 * @apiSuccess {Date} data.creditedDate Credited date
 * @apiSuccess {Object} data.leaveBalance Leave balance object
 * @apiSuccess {Number} data.leaveBalance.entitled Entitlement
 * @apiSuccess {Number} data.leaveBalance.used Used leave
 * @apiSuccess {Number} data.leaveBalance.scheduled Scheduled leave
 * @apiSuccess {Number} data.leaveBalance.pending Pending leave
 * @apiSuccess {Number} data.leaveBalance.notLinked
 * @apiSuccess {Number} data.leaveBalance.taken Leave taken
 * @apiSuccess {Number} data.leaveBalance.adjustment
 * @apiSuccess {Number} data.leaveBalance.balance Leave balance
 * @apiSuccess {Object} data.leaveType Leave type object
 * @apiSuccess {String} data.leaveType.type Leave type
 * @apiSuccess {String} data.leaveType.id Leave type id
 *
 * @apiSuccessExample Success-Response:
 * HTTP/1.1 200 OK
 *
 * {
 *   "data": [
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
 *     ]
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
