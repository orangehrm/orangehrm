/**
* @api {put} /employee/:id/custom-field 30.Update Employee Custom Field
* @apiName updateEmployeeCustomField
* @apiGroup Employee
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam {Number}  id Employee id.
* @apiParam {Number} fieldId Field id.
* @apiParam {String} value Field value.
*
* @apiSuccess {Object} Data Success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*   {
*
*   "success":"Successfully Saved",
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
* @apiError InvalidParameter Invalid parameter.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": "Field Value Must Not Be Empty"
*     }
*
*/



