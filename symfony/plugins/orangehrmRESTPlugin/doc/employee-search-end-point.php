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
    *             "id": "1",
    *             "code": "001",
    *             "firstName": "John",
    *             "lastName": "Doe",
    *             "middleName": "",
    *             "fullName": "John Doe",
    *             "status": "active",
    *             "jobTitle": "web developer",
    *             "supervisor": "Mike com",
    *             "supervisorId": "2",
    *             "dob": "1989-09-7",
    *             "unit": "development",
    *             "gender": "M"
    *         },
    *         {
    *             "id": "002",
    *             "code": "001",
    *             "firstName": "John",
    *             "lastName": "Mass",
    *             "middleName": "",
    *             "fullName": "John Mass",
    *             "status": "active",
    *             "jobTitle": "web developer",
    *             "supervisor": "Simon English",
    *             "supervisorId": "2",
    *             "dob": "1989-09-7",
    *             "unit": "development",
    *             "gender": "M"
    *         }
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
