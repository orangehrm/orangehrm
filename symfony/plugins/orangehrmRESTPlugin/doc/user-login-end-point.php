/**
* @api {post} /login 2.User Login
* @apiName userLogin
* @apiGroup Admin
* @apiVersion 0.1.0
*
*
* @apiParam   {String} username  User Name.
* @apiParam   {String} password  User password.
*
* @apiSuccess  {String} login  User login ( true or false ).
* @apiSuccess  {String} userName  User Name.
* @apiSuccess  {String} employeeName  Employee name.
* @apiSuccess  {Number} employeeId  Employee id.
* @apiSuccess  {String} userRole  User role.
* @apiSuccess  {String} status  User status.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*		{
*		  "login": true,
*		  "user": {
*		    "userName": "Admin",
*		    "userRole": "Admin",
*		    "status": "Enabled",
*		    "employeeName": ""
*           "employeeId": ""
*		  }
*		}
*
* @apiError InvalidParameter Found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Invalid Parameter
*     {
*       "error": ["Credentials Are Wrong Please Try Again"]
*     }
*
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 501 Bad Request
*     {
*       "error": ["Login Failed"]
*     }
*
*/
