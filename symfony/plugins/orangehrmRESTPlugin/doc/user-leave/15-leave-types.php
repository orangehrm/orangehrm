<?php
/**
 * @api {get} /leave/leave-types 15.Get Leave Types
 * @apiName getLeaveTypes
 * @apiGroup User-Leave
 * @apiVersion 1.1.0
 * @apiUse UserDescription
 *
 * @apiParam {String='true','false'} [all] Get all leave types. Only allowed to perform supervisors and admins
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "data": [
 *              {
 *                  "id": "1",
 *                  "type": "Casual",
 *                  "deleted": "0",
 *                  "situational": false
 *              },
 *              {
 *                  "id": "2",
 *                  "type": "Medical",
 *                  "deleted": "0",
 *                  "situational": false
 *              }
 *         ],
 *         "rels": []
 *     }
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 400 No Bound User
 *     {
 *       "error": ["No Bound User"]
 *     }
 *
 */
