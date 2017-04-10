/**
* @api {get} /employee/:id/leave-request 9.Get Employee Leave Requests
* @apiName employeeLeaveRequest
* @apiGroup Leave
* @apiVersion 0.1.0
*
*
* @apiParam {Number}  [id] Employee id.
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
*                    "comment": "Casual leaves are granted"
*                    },
*                    {
*                    "author": "Admin",
*                    "date": "2017-03-16",
*                    "time": "14:18:10",
*                    "comment": "leaves granted"
*                    }
*                           ]
*         "days": [
*              {
*                "date": "2017-05-25",
*                "status": "SCHEDULED",
*                "type": "Short Leave",
*                "duration": "8.00",
*               "comments": [
*                    {
*                    "user": "Admin",
*                    "date": "2017-03-16",
*                    "time": "14:20:27",
*                    "comment": "Granted"
*                    },
*                    {
*                    "author": "Admin",
*                    "date": "2017-03-16",
*                    "time": "14:18:10",
*                    "comment": "Check the balance"
*                    }
*                           ]
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
