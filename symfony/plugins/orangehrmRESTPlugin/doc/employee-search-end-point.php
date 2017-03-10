/**
* @api {get} /employee/search Employee Search
* @apiName SearchEmployee
* @apiGroup Employee
*
* @apiParam {String} [name] Employee name.
* @apiParam {String} [code] Employee code.
* @apiParam {String} [status] Employee status.
* @apiParam {String} [supervisor] Supervisor name.
* @apiParam {String} [jobtitle] Employee job title.
* @apiParam {String} [unit] Employee Unit.
* @apiParam {String} [dob] Employee birth day.
* @apiParam {String} [gender] Employee gender.
* @apiParam {Number} [limit] Record limit.
* @apiParam {Number} [page] Pagination number.
* @apiSuccess {Object} data Matching Employee list.
* @apiSuccess {Object} rels  API relations.
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
*         "next": "/employee/search?page=3",
*         "previous": "/employee/search?page=1"
*       }
*     }
*
*
* @apiError EmployeeNotFound.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Not Found
*     {
*       "error": "Employee not found"
*     }
*/
