/**
* @api {post} /customer 2.Save Customer
* @apiName saveCustomer
* @apiGroup Time
* @apiVersion 0.1.0
*
* @apiParam  {String} name  Customer name.
* @apiParam  {String} description  Description.
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*      {
*        "success": "Successfully Saved"
*      }
*
* @apiError Invalid Parameter.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 202 Customer Already Exists
*     {
*       "error": ["Customer Already Exists"]
*     }
*
*
*/
