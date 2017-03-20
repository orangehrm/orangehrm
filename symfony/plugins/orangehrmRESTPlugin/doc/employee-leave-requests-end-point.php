/**
* @api {get} /api/v1/leave/type 2.Get Employee Leave Requests
* @apiName employeeLeaveRequest
* @apiGroup Leave
* @apiVersion 0.1.0
*
*
* @apiParam {Number}  [id] Employee id.
*
* @apiSuccess {String} [type] Leave type.
* @apiSuccess {Number} [id] Leave id.
* @apiSuccess {Date} [date] Requested date.
* @apiSuccess {Number} [leaveBalance] Leave balance.
* @apiSuccess {Number} [numberOfDays] Number of Days.
* @apiSuccess {String} [comments] Leave comments.
* @apiSuccess {Strung} [action] Leave Action.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*           "type": "Annual",
*           "id": "2",
*           "date": "2017-03-31",
*           "$leaveBalance": 9,
*           "numberOfDays": "1.00",
*           "status": 2,
*           "comments": [
*                    {
*                    "commentId": "3",
*                    "author": "Admin",
*                    "date": "2017-03-16",
*                    "time": "14:20:27",
*                    "comment": "Test"
*                    },
*                    {
*                    "commentId": "1",
*                    "author": "Admin",
*                    "date": "2017-03-16",
*                    "time": "14:18:10",
*                    "comment": "Test"
*                    }
*           "action": null
*      }
*
* @apiError No-Records Found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Record Not Found
*     {
*       "error": ["No Records Found"]
*     }
*
*
* @apiError Employee Not Found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Employee Not Found
*     {
*       "error": ["Employee Not Found"]
*     }
*/
