/**
* @api {get} /employee/search 05.Employee Search
* @apiName SearchEmployee
* @apiGroup Employee
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiParam {String} [name] Employee name.
* @apiParam {String} [code] Employee code.
* @apiParam {Number} [status] Employee status id.
* @apiParam {String} [supervisor] Supervisor name.
* @apiParam {Number} [jobTitle] Employee job title id.
* @apiParam {Number} [unit] Employee Unit id.
* @apiParam {String} [dob] Employee birth day.
* @apiParam {String} [include] Include Termination ( TERMINATED_ONLY , WITHOUT_TERMINATED ,TERMINATED_ONLY )
* @apiSuccess {Object} data Matching Employee list.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*       {
*         "data":[
*         {
*             "firstName": "Nina",
*             "middleName": "Jane",
*              "lastName": "Lewis",
*              "code": "0014",
*              "id": "1",
*              "fullName": "Nina Jane Lewis",
*              "status": "Active",
*              "dob": "2016-05-04",
*              "driversLicenseNumber": "444555124223",
*              "licenseExpiryDate": "2017-02-09",
*              "maritalStatus": "Married",
*              "gender": "2",
*              "otherId": "45",
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
*         {
*             "firstName": "Nina",
*             "middleName": "Shane",
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
*       ],
*       "rels": {
*
*       }
*     }
*
*
* @apiError EmployeeNotFound.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Not Found
*     {
*       "error": "Employee Not Found"
*     }
*/
