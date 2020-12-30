/**
* @api {get} /attendance/punch-status 01.Get Employee Punch Status
* @apiName EmployeePunchStatus
* @apiGroup User-Attendance
* @apiVersion 1.2.0
* @apiUse UserDescription_47
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*            {
*                "data": {
*                    "punchTime": "2021-01-31 19:31:00",
*                    "punchNote": "PUNCH IN NOTE",
*                    "punchTimeZoneOffset": 5.5,
*                    "dateTimeEditable": true,
*                    "currentUtcDateTime": "2020-11-12 05:25",
*                    "punchState": "PUNCHED IN"
*                },
*
*                "rels": []
*             }
*
*
*/


