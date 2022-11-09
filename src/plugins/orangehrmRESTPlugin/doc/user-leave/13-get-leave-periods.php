<?php
/**
 * @api {get} /leave/leave-periods 13.Get Leave Periods
 * @apiName getLeavePeriods
 * @apiGroup User-Leave
 * @apiVersion 1.1.0
 * @apiUse UserDescription
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *          "data": [
 *              {
 *                  "startDate": "2020-01-01",
 *                  "endDate": "2020-12-31"
 *              }
 *          ],
 *          "rels": []
 *     }
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 400 No Bound User
 *     {
 *       "error": ["No Bound User"]
 *     }
 *
 */
