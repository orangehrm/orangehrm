<?php
/**
 * @api {get} /leave/work-week 14.Get Work Week
 * @apiName getLeaveWorkWeek
 * @apiGroup User-Leave
 * @apiVersion 1.1.0
 * @apiUse UserDescription
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "data": {
 *          "mon": "0",
 *          "tue": "0",
 *          "wed": "0",
 *          "thu": "0",
 *          "fri": "0",
 *          "sat": "4",
 *          "sun": "8"
 *       },
 *       "rels": []
 *     }
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 400 No Bound User
 *     {
 *       "error": ["No Bound User"]
 *     }
 *
 */
