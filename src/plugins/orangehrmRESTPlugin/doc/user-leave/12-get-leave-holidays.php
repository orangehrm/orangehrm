<?php
/**
 * @api {get} /leave/holidays 12.Get Holidays
 * @apiName getLeaveHolidays
 * @apiGroup User-Leave
 * @apiVersion 1.1.0
 * @apiUse UserDescription
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "data": [
 *          {
 *              "id": "1",
 *              "recurring": "0",
 *              "description": "Holiday1",
 *              "date": "2020-08-05",
 *              "length": "4"
 *          },
 *          {
 *              "id": "2",
 *              "recurring": "1",
 *              "description": "Holiday2",
 *              "date": "2020-08-06",
 *              "length": "8"
 *          }
 *       ],
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
