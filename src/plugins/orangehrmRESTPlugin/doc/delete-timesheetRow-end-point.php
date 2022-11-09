/**
* @api {delete} /employee/:id/timesheet/row_delete 16.Delete Timesheet Row
* @apiName deleteTimesheetRow
* @apiGroup Time
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam  {Number} projectId  Project id.
* @apiParam  {Number} id  Employee id.
* @apiParam  {Number} timesheetId  Project id.
* @apiParam  {Number} activityId  Project id.
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully Deleted"
*      }
*
*
* @apiError Bad Request.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 400 Unable To Delete Timesheet Rows
*     {
*       "error": ["Unable To Delete Timesheet Rows"]
*     }
*
*
*
*/
