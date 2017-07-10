/**
* @api {get} /leave/search 7.Search Leave Requests
* @apiName searchLeaveRequest
* @apiGroup Leave
* @apiVersion 0.1.0
*
*
* @apiParam {Date}  fromDate From date.
* @apiParam {Date}   toDate To date.
* @apiParam {String}  [rejected] Leave status rejected ( 'true' / 'false' ).
* @apiParam {String}  [cancelled] Leave status cancelled ( 'true' / 'false' ).
* @apiParam {String}  [pendingApproval] Leave status pending approval ( 'true' / 'false' ).
* @apiParam {String}  [scheduled] Leave status scheduled ( 'true' / 'false' ).
* @apiParam {String}  [taken] Leave status taken ( 'true' / 'false' ).
* @apiParam {String}  [pastEmployee] Past employee results ( 'true' /'false').
* @apiParam {Number}  [subunit] Employee subunit id.
*
* @apiSuccess {String} employeeName Employee name
* @apiSuccess {String} employeeId Employee id.
* @apiSuccess {Date}   fromDate From date.
* @apiSuccess {Date}   toDate To date.
* @apiSuccess {String} type Leave type.
* @apiSuccess {Number} id Leave request id.
* @apiSuccess {Number} leaveBalance Leave balance.
* @apiSuccess {Number} numberOfDays Number of Days.
* @apiSuccess {Object} comments Leave request comments.
*
* @apiSuccess {String} user User.
* @apiSuccess {Date} date Commented date.
* @apiSuccess {Time} time Commented time.
* @apiSuccess {String} comment Comment .
*
* @apiSuccess {Object} days Leave days.
* @apiSuccess {String} status Leave status.
* @apiSuccess {Number} duration Leave duration.
* @apiSuccess {String} durationString Leave duration as a String for specify time and half days.
* @apiSuccess {Object} comments Leave comments.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*           "employeeName" : "Shane Warne",
*           "employeeId"   :'34',
*           "id": "2",
*           "fromDate": "2017-03-31",
*           "toDate": "2017-03-31",
*           "leaveBalance": 9,
*           "numberOfDays": "1.00",
*               "comments": [
*                    {
*                    "user": "Admin",
*                    "date": "2017-03-16",
*                    "time": "14:20:27",
*                    "comment": "Test"
*                    },
*                    {
*                    "user": "Admin",
*                    "date": "2017-03-16",
*                    "time": "14:18:10",
*                    "comment": "Test"
*                    }
*         "days": [
*              {
*                "date": "2017-05-25",
*                "status": "SCHEDULED",
*                "type": "Short Leave",
*                "duration": "8.00",
*                "comments": ""
*            },
*            {
*                "date": "2017-05-24",
*                "status": "SCHEDULED",
*                "type": "Short Leave",
*                "duration": "8.00",
*                "comments": ""
*            }
*                ]
*
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
