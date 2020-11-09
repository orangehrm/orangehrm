/**
* @api {get} /attendance/punch-in 14.Get Employee Punch In
* @apiName EmployeePunchIn
* @apiGroup User
* @apiVersion 0.1.0
* @apiUse UserDescription
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*            {
*                 "data": {
*                    "id": "10",
*                    "punchOutTime": "2020-12-25 10:26:00",
*                    "timezoneOffset": 5.5
*                 },
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


