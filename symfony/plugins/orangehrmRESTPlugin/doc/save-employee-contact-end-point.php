/**
* @api {post} /employee/:id/contact-detail Save Employee Contact Detail
* @apiName saveEmployeeContactDetails
* @apiGroup Employee
*
* @apiParam {Number}  id Employee number
*
* @apiParam {String} addressStreet1  Address street 1 of the employee.
* @apiParam {String} addressStreet2  Address street 2 of the employee.
* @apiParam {String} city  City of the employee.
* @apiParam {String} state  State of the employee.
* @apiParam {String} zip  Zip code of the employee.
* @apiParam {String} country  Country of the employee.
* @apiParam {String} homeTelephone  Home telephone number of the employee.
* @apiParam {String} mobile  Mobile number of the employee.
* @apiParam {String} workTelephone  Work telephone number of the employee.
* @apiParam {String} workEmail  Work email of the employee.
* @apiParam {String} otherEmail  Other email of the employee.
* @apiSuccess {Object} data success response
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully saved"
*      }
*
* @apiError Bad-Response Saving failed.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 400 Bad Request
*     {
*       "error": ["Saving failed"]
*     }
*/
