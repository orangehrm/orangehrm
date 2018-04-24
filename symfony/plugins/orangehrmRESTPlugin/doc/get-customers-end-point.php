/**
* @api {get} /customer 1.Get Customers
* @apiName getCustomers
* @apiGroup Time
* @apiVersion 0.1.0
*
*
* @apiSuccess  {Number} customerId  Customer Id.
* @apiSuccess  {String} isDeleted  Is deleted( 1,0).
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
*		      "isDeleted": "0",
*		      "name": "Aus Trading",
*		      "description": "Description"
*		    },
*		    {
*		      "customerId": "2",
*		      "isDeleted": "0",
*		      "name": "Test11",
*		      "description": "Test Description"
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
