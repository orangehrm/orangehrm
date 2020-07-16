<?php
/**
 * @api {get} /subordinate/:id/leave-entitlement 08.Get Subordinate Leave Entitlements
 * @apiName subordinateLeaveEntitlements
 * @apiGroup Mobile
 * @apiVersion 0.1.0
 * @apiUse MobileDescription
 *
 * @apiParam {Date}  id Subordinate employee id
 * @apiParam {Date}  [fromDate] Valid leave period from date
 * @apiParam {Date}  [toDate] Valid leave period to date
 *
 * @apiUse MobileLeaveEntitlementsSuccess
 *
 */
