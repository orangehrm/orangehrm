/**
* @api {post} /attendance/punch-in 02.Save Employee Punch In
* @apiName EmployeePunchIn
* @apiGroup User-Attendance
* @apiVersion 1.2.0
* @apiUse UserDescription_47
*
*
* @apiParam   {String} timezoneOffset  Time Zone Offset ( ex: 5.5 ).
* @apiParam   {String} [note] Punch In Note. ( ex: "Successfully Punched In" )
* @apiParam   {Date}  datetime Date and Time Required If Current Time Editable ( ex: "2020-12-28 08:30" )
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*            {
*                "id": "1",
*                "datetime": "2020-12-28 08:30",
*                "timezoneOffset": 5.5,
*                "note": "PUNCH IN NOTE"
*            }
*
* @apiError InvalidParameter Found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["Invalid Time Zone"]
*     }
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
* @apiError Invalid Action.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["Overlapping Records Found"]
*     }
*
* @apiError Invalid Action.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["You Are Not Allowed To Change Current Date & Time"]
*     }
*
*/


