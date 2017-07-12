/**
* @api {get} employee/id/photo 34.Employee Picture
* @apiName getEmployeePicture
* @apiGroup Employee
* @apiVersion 0.1.0
*
* @apiParam   {Number} id Employee id.
* @apiSuccess {String} base64 Base64 encoded employee picture details.
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*		{
*		  "data": [
*		    {
*		     "base64": "/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAMCAgICAgMCAgIDAwMDBAY"
*		    }
*		  ]
*		 }
*
* @apiError Employee Picture Not Found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Employee Picture Not Found
*     {
*       "error": ["Employee Not Found"]
*     }
*
*/
