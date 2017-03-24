/**
* @api {get} /user 1.Get Users
* @apiName getUsers
* @apiGroup Admin
* @apiVersion 0.1.0
*
* @apiParam   {String} userName  User Name.
* @apiParam   {Number} employeeId  Employee id.
* @apiParam   {Number} userType  User type id.
* @apiParam   {Number} offset  Page number.
* @apiParam   {Number} limit  Number of results per page.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*		{
*		    "data":[
*			{
*			    "userName":"hameesh",
*			    "userRole":"ESS",
*			    "status":"1",
*			    "employeeName":"Hameesh Von Johnson"
*			},
*			{
*			    "userName":"nina123",
*			    "userRole":"ESS",
*			    "status":"1",
*			    "employeeName":"Nina Jane Lewis"
*			},
*			{
*			    "userName":"shawn",
*			    "userRole":"ESS",
*			    "status":"1",
*			    "employeeName":"Shawn haffman"
*			}
*		    ],
*		    "rels":[
*
*		    ]
*		}
*
* @apiError No-Records Found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Record Not Found
*     {
*       "error": ["No Users Found"]
*     }
*
*/
