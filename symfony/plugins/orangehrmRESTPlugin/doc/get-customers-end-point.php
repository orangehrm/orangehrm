/**
* @api {get} /customer 1.Get Customers
* @apiName getCustomers
* @apiGroup Time
* @apiVersion 0.1.0
*
*
* @apiSuccess  {Number} customerId  Customer Id.
* @apiSuccess  {String} is_deleted  Is deleted( 1 = true /0 = false).
* @apiSuccess  {String} name  Customer name.
* @apiSuccess  {String} description  Description.
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*		{
*		  "data": [
*		    {
*		      "customerId": "1",
*		      "is_deleted": "0",
*		      "name": "Aus Trading",
*		      "description": ""
*		    },
*		    {
*		      "customerId": "2",
*		      "is_deleted": "0",
*		      "name": "Test11",
*		      "description": "Defsg"
*		    }
*		  ],
*		  "rels": []
*		}
* @apiError RecordNotFound .
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 No Customers Found
*     {
*       "error": ["No Customers Found"]
*     }
*
*/
