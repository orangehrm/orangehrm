/**
* @api {delete} /customer 04.Delete Customer
* @apiName deleteCustomer
* @apiGroup Time
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
*
* @apiParam  {Number} customerId  Customer Id.
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully Deleted"
*      }
*
*
* @apiError Record Not Found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Customer Not Found
*     {
*       "error": ["Customer Not Found"]
*     }
*
*
*/
