/**
* @api {post} /employee/:id/punch-in 1.Punch In
* @apiName punchIn
* @apiGroup Attendance
* @apiVersion 0.1.0
*
*
* @apiParam   {Number} id  Employee id.
* @apiParam   {String} timezone  Time zone ( ex: Europe/London ).
* @apiParam   {String} note Punch in note.
* @apiParam   {Date}  datetime Date and time
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*            {
*            "success": "Successfully Punched In",
*            "id": "004"
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
* @apiError Employee Not Found Found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["Employee Id  Not Found"]
*     }
* @apiError Invalid Action.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["Cannot Proceed Punch In Employee Already Punched In"]
*     }
*
*/
