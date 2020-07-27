/**
* @api {put} /customer 03.Update Customer
* @apiName updateCustomer
* @apiGroup Time
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
*
* @apiParam  {Number} customerId  Customer Id.
* @apiParam  {String} name  Customer name.
* @apiParam  {String} description  Description.
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully Updated"
*      }
*
* @apiError Invalid Parameter.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Customer Already Exists
*     {
*       "error": ["Customer Already Exists"]
*     }
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
