<?php
/**
 * @api {get} /employees 11.Get Accessible Employees
 * @apiName getAccessibleEmployees
 * @apiGroup User
 * @apiVersion 1.1.0
 * @apiUse UserDescription
 *
 * @apiParam {String} [actionName] Action name. e.g. `assign_leave`, `view_leave_list`
 * @apiParam {String[]} [properties[]] Employee properties array. e.g. `employeeId`, `firstName`, `lastName`, `termination_id`.
 * ```
 * /api/v1/employees?properties[]=firstName&properties[]=lastName&properties[]=termination_id
 * ```
 * @apiParam {String='true','false'} [pastEmployee] Specify whether with past employee. Default `false`
 *
 * @apiSuccessExample Success-Response:
 * HTTP/1.1 200 OK
 *
 * {
 *   "data": [
 *       {
 *         "employeeId": "4",
 *         "firstName": "Kevin",
 *         "lastName": "Mathews",
 *         "employeeId": "004",
 *       },
 *       {
 *         "employeeId": "5",
 *         "firstName": "Linda",
 *         "lastName": "Jane",
 *         "employeeId": "005",
 *       }
 *     ]
 *   ],
 *   "rels": []
 * }
 *
 * @apiError InvalidParameter Found
 *
 * @apiErrorExample Error-Response:
 * HTTP/1.1 202 Invalid Parameter
 * {
 *   "error": {
 *     "status": "202",
 *     "text": "Invalid `<param>` Value"
 *   }
 * }
 *
 */
