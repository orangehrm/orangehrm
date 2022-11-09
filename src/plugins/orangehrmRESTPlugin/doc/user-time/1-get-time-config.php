<?php
/**
 * @api {get} /time/config 01.Get Time Configurations
 * @apiName getTimeConfig
 * @apiGroup User-Time
 * @apiVersion 1.2.0
 * @apiUse UserDescription_47
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *
 *        {
 *          "data": {
 *            "startDate": 1
 *          }
 *        }
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 400 No Bound User
 *     {
 *       "error": ["No Bound User"]
 *     }
 *
 */
