<?php
/**
 * @api {get} /leave/work-shift 01.Get Work Shift
 * @apiName getWorkShift
 * @apiGroup User-Leave
 * @apiVersion 1.1.0
 * @apiUse UserDescription
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *          "data": {
 *              "workShift": "8.00",
 *              "startTime": "09:00",
 *              "endTime": "17:00"
 *          },
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
