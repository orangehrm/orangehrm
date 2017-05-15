/**
* @api {get} /employee/:id/custom-field 28.Employee Custom Field
* @apiName getEmployeeCustomField
* @apiGroup Employee
* @apiVersion 0.1.0
*
* @apiParam {Number}  id Employee id.
*
* @apiSuccess {Number} id Field id.
* @apiSuccess {String} name Field name.
* @apiSuccess {String} type Field type.
* @apiSuccess {String} screen Applicable screen.
* @apiSuccess {String} value Field value.
* @apiSuccess {Object} Data Success response.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*		{
*		  "data": [
*		    {
*		      "id": "4",
*		      "name": "Course",
*		      "type": "Drop Down",
*		      "screen": "personal",
*		      "value": "Bsc"
*		    },
*		    {
*		      "id": "3",
*		      "name": "GPA",
*		      "type": "Text or Number",
*		      "screen": "personal",
*		      "value": 3.6
*		    },
*		    {
*		      "id": "2",
*		      "name": "school",
*		      "type": "Text or Number",
*		      "screen": "dependents",
*		      "value": "Prince Of Wales"
*		    },
*		    {
*		      "id": "1",
*		      "name": "University Name",
*		      "type": "Text or Number",
*		      "screen": "personal",
*		      "value": "University Of Moratuwa"
*		    }
*		  ]
*		 }
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
*/
