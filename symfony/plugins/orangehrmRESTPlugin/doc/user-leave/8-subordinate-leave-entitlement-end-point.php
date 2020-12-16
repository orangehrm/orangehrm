<?php
/**
 * @api {get} /subordinate/:id/leave-entitlement 08.Get Subordinate Leave Entitlements
 * @apiName subordinateLeaveEntitlements
 * @apiGroup User-Leave
 * @apiVersion 1.1.0
 * @apiUse UserDescription
 *
 * @apiParam {Number}  id Subordinate employee id
 * @apiParam {Date}  [fromDate] Valid leave period from date
 * @apiParam {Date}  [toDate] Valid leave period to date
 * @apiParam {Date}  [balanceAsAtDate] Start date for calculate balance. Default: current date.
 * @apiParam {Date}  [balanceEndDate] End date for calculate balance. Default: end date of current leave period
 * @apiParam {Boolean}  [deletedLeaveTypes] With deleted leave types
 * @apiParam {Boolean}  [combineLeaveTypes] Whether combine, not entitled leave types with leave balance.
 *
 * @apiUse UserLeaveEntitlementsSuccess
 *
 */
