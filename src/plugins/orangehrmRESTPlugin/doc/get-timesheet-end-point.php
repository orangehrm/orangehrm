/**
* @api {get} /employee/:id/timesheet 13.Get TimeSheets
* @apiName getTimesheets
* @apiGroup Time
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
*
* @apiParam    {Number} id Employee id.
* @apiParam    {Date}   startDate Timesheet start date.
* @apiSuccess  {Number} timeSheetId  Timesheet id.
* @apiSuccess  {Number} employeeId  Employee id.
* @apiSuccess  {Date} startDate  Start date.
* @apiSuccess  {Date} endDate  End date.
* @apiSuccess  {String} state  State.
* @apiSuccess  {Number} timeSheetItemId  Timesheet item id.
* @apiSuccess  {String} projectName  Project name.
* @apiSuccess  {Object} projectId Project id.
* @apiSuccess  {Object} activityName Project activity name.
* @apiSuccess  {Number} activityId Project activity id.
* @apiSuccess  {String} date Timesheet item date.
* @apiSuccess  {Number} duration Timesheet item duration.
* @apiSuccess  {String} comment Timesheet state change comment.
*
*
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*		{
*		    "data": [
*			{
*			    "timeSheetId": "1",
*			    "employeeId": "1",
*			    "startDate": "2017-06-26",
*			    "endDate": "2017-07-02",
*			    "state": "REJECTED",
*			    "timeSheetItems": [
*				{
*                   "timesheetItemId": "1",
*				    "projectName": "4",
*				    "projectId": "4",
*				    "activityName": "Tournement",
*				    "activityId": "6",
*				    "date": "2017-06-26",
*				    "duration": "21600",
*				    "comment": null
*				},
*				{
*                   "timesheetItemId": "2",
*				    "projectName": "4",
*				    "projectId": "4",
*				    "activityName": "Tournement",
*				    "activityId": "6",
*				    "date": "2017-06-27",
*				    "duration": "25200",
*				    "comment": null
*				},
*				{
*                   "timesheetItemId": "3",
*				    "projectName": "4",
*				    "projectId": "4",
*				    "activityName": "Tournement",
*				    "activityId": "6",
*				    "date": "2017-06-28",
*				    "duration": "21600",
*				    "comment": null
*				},
*				{
*                   "timesheetItemId": "4",
*				    "projectName": "4",
*				    "projectId": "4",
*				    "activityName": "Tournement",
*				    "activityId": "6",
*				    "date": "2017-06-29",
*				    "duration": "21600",
*				    "comment": null
*				}
*
*			    ]
*			}
*		    ],
*		    "rels": []
*		}
*
*
*
* @apiError RecordNotFound .
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 No Projects Found
*     {
*       "error": ["No TimeSheets Found"]
*     }
*
*/
