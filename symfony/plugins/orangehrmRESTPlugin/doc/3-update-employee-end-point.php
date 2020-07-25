/**
* @api {put} /employee/:id 03.Update Employee Detail
* @apiName UpdateEmployeeDetails
* @apiGroup Employee
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam {Number}  id Employee id.
*
* @apiParam {String} firstName First name of the employee.
* @apiParam {String} middleName  Middle name of the employee.
* @apiParam {String} lastName  Last Name of the employee.
* @apiParam {String} code  Employee code.
* @apiParam {String} dob  DOB of the employee.
* @apiParam {String} licenseNumber  Employee driver's license number.
* @apiParam {String} licenseNumberExpDate  Employee driver's license expiry date
* @apiParam {String} maritalStatus  Employee marital status.
* @apiParam {String} gender  Gender of the employee (M-Male/F-Female).
* @apiParam {String} otherId  Employee other id.
* @apiParam {String} nationality  Nationality of the employee.
* @apiSuccess {String} Object Data Success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully Saved"
*      }
*
* @apiError Bad-Response Saving Failed.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 400 Bad Request
*     {
*       "error": ["Saving Failed"]
*     }
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["Invalid Parameter"]
*     }
*/
