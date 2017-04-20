/**
* @api {get} /employee/event 20.Get Employee Events
* @apiName getEvents
* @apiGroup Employee
* @apiVersion 0.1.0
*
*
* @apiParam {Number} employeeId Employee id.
* @apiParam {Date} fromDate Event from date.
* @apiParam {Date} toDate Event to date.
* @apiParam {String} event Event ( UPDATE | SAVE | DELETE ).
* @apiParam {String} type Event type ( employee | contact | jobDetail | supervisor | subordinate |dependent ).
*
*
* @apiSuccess {String} employeeId Employee id.
* @apiSuccess {String} employeeName Employee full name.
* @apiSuccess {String} event Event.
* @apiSuccess {String} type Event type.
* @apiSuccess {String} createdBy Created user.
* @apiSuccess {String} createdDate Created date.
* @apiSuccess {String} note Note.
* @apiSuccess {Object} Data Success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*	{
*	 "data": [
*	    {
*	      "employeeId": "3",
*	      "employeeName": "Glen Maxwell",
*	      "event": "UPDATE",
*	      "type": "contact",
*	      "createdBy": "Admin",
*	      "createdDate": "2017-04-12 11:05:51",
*	      "note": "Update Employee Contact Details"
*	    },
*	    {
*	      "employeeId": "3",
*	      "employeeName": "Glen Maxwell",
*	      "event": "SAVE",
*	      "type": "supervisor",
*	      "createdBy": "Admin"
*	      "createdDate": "2017-04-12 01:47:23",
*	      "note": "Saving Employee Supervisor Details"
*           }
*                ]
*       }
*
*
* @apiError RecordNotFound No Event Records Found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Not Found
*     {
*       "error": "No Event Records Found"
*     }
*/
