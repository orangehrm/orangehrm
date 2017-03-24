/**
* @api {get} /leave/type 3.Search Leave Requests
* @apiName searchLeaveRequest
* @apiGroup Leave
* @apiVersion 0.1.0
*
*
* @apiParam {Date}  [fromDate] From date.
* @apiParam {Date}  [toDate] To date.
* @apiParam {String}  [reject] Leave status rejected ( 'true' / 'false' ).
* @apiParam {String}  [cancelled] Leave status cancelled ( 'true' / 'false' ).
* @apiParam {String}  [pendingApproval] Leave status pending approval ( 'true' / 'false' ).
* @apiParam {String}  [scheduled] Leave status scheduled ( 'true' / 'false' ).
* @apiParam {String}  [taken] Leave status taken ( 'true' / 'false' ).
* @apiParam {String}  [pastEmployee] Past employee results ( 'true' /'false').
* @apiParam {String}  [subunit] Employee subunit.
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
