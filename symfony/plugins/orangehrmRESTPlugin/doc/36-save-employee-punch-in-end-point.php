/**
* @api {post} /attendance/punch-in 15.Save Employee Punch In
* @apiName EmployeePunchIn
* @apiGroup User
* @apiVersion 0.1.0
* @apiUse UserDescription
*
*
* @apiParam   {String} timezone  Time zone ( ex: Europe/London ).
* @apiParam   {String} note Punch in note.
* @apiParam   {Date}  datetime Date and time
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*            {
*                "success": "Successfully Punched In",
*                "id": "24",
*                "datetime": "2020-12-28 10:22",
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
*/


