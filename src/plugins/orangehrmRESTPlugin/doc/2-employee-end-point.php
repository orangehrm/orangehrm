/**
* @api {get} /employee/:id 02.Employee Detail
* @apiName GetEmployeeDetail
* @apiGroup Employee
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam {Number}  id Employee id.
*
* @apiSuccess {String} firstName First name of the employee.
* @apiSuccess {String} middleName  Middle name of the employee.
* @apiSuccess {String} lastName  Last Name of the employee.
* @apiSuccess {Number} id  Id of the employee.
* @apiSuccess {String} code  Employee code.
* @apiSuccess {String} fullName  Full Name of the employee.
* @apiSuccess {String} status  Status of the employee.
* @apiSuccess {String} dob  DOB of the employee.
* @apiSuccess {String} driversLicenseNumber  Employee driver's license number.
* @apiSuccess {String} licenseExpiryDare  Employee driver's license expiry date
* @apiSuccess {String} maritalStatus  Employee marital status.
* @apiSuccess {String} gender  Gender of the employee.
* @apiSuccess {String} otherId  Employee other id.
* @apiSuccess {String} nationality  Nationality of the employee.
* @apiSuccess {String} jobTitle  Employee job title.
* @apiSuccess {String} unit Employee sub unit.
* @apiSuccess {Object} supervisor  Employee supervisor details.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*       {
*         "data":
*         {
*             "firstName": "Nina",
*             "middleName": "Jane",
*              "lastName": "Lewis",
*              "code": "0014",
*              "id": "1",
*              "fullName": "Nina Jane Lewis (Past Employee)",
*              "status": "Active",
*              "dob": "2016-05-04",
*              "driversLicenseNumber": "444555124223",
*              "licenseExpiryDate": "2017-02-09",
*              "maritalStatus": "Married",
*              "gender": "2",
*              "otherId": "4646522",
*              "nationality": "Armenian",
*              "unit": "Marketing Unit",
*              "jobTitle": "marketing",
*
*            "supervisor": [
*                           {
*                             "name": "Hameesh Von Johnson",
*                             "id": "3"
*                            }
*                           ]
*         },
*       "rels": {
*         "contact-detail": "/employee/:id/contact-detail",
*         "supervisor": "/employee/:id/supervisor",
*         "job-detail": "/employee/:id/job-detail",
*         "dependent": "/employee/:id/dependent"
*       }
*     }
*
* @apiError UserNotFound The id of the employee was not found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Not Found
*     {
*       "error": "Employee Not Found"
*     }
*/
