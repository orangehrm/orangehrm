/**
* @api {del} /employee/:id/custom-field 31.Delete Employee Custom Field
* @apiName deleteEmployeeCustomField
* @apiGroup Employee
* @apiVersion 0.1.0
*
* @apiParam {Number} id Employee id.
* @apiParam {Number} fieldId Field id.
*
* @apiSuccess {Object} Data Success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*   {
*
*   "success":"Successfully Deleted",
*
*   }
*
*
*
* @apiError UserNotFound The id of the employee was not found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Not Found
*     {
*       "error": "Employee Not Found"
*     }
*
* @apiError CustomFieldNotFound The id of the custom field was not found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Not Found
*     {
*       "error": "Custom Field Not Found"
*     }
*
*
*/



