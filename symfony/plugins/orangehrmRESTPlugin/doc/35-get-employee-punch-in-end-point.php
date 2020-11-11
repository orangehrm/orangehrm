/**
* @api {get} /attendance/punch-in 14.Get Employee Punch In
* @apiName EmployeePunchIn
* @apiGroup User
* @apiVersion 1.2.0
* @apiUse UserDescription
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*            {
*                        "data": {
*                           "id": "28",
*                           "punchOutTime": "2020-12-29 19:34:00",
*                           "punchOutTimezone": 5.5,
*                           "dateTimeEditable": true,
*                           "currentUtcDateTime": "2020-11-10 07:29"
*                        },
*
*                "rels": []
*             }
*
*
* @apiError Invalid Action.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["Cannot Proceed Punch In Employee Already Punched In"]
*     }
*
*
*/


