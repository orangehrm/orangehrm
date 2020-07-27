/**
* @api {get} /organization 03.Get Organization Information
* @apiName getOrganization
* @apiGroup Admin
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
*
* @apiSuccess  {int} id  Record id.
* @apiSuccess  {String} name  Employee name.
* @apiSuccess  {String} taxId  Tax id.
* @apiSuccess  {String} registrationNumber  Registration number.
* @apiSuccess  {String} phone  Company phone number.
* @apiSuccess  {String} fax  Company fax .
* @apiSuccess  {String} email  Company email.
* @apiSuccess  {String} country  Country Code.
* @apiSuccess  {String} province  Province.
* @apiSuccess  {String} city  City.
* @apiSuccess  {String} zipCode  Zip code.
* @apiSuccess  {String} street1  Street 1.
* @apiSuccess  {String} street2  Street 2.
* @apiSuccess  {String} note  Note.
* @apiSuccess  {Number} numberOfEmployees Number of employees.
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*		{
*		  "data": {
*		    "id": "1",
*		    "name": "Beacon Test",
*		    "taxId": "234",
*		    "registraionNumber": "Errt1",
*		    "phone": "097645362",
*		    "fax": "07647364",
*		    "email": "Orange@live.com",
*		    "country": "UK",
*		    "province": "Western",
*		    "city": "London",
*		    "zipCode": "12345",
*		    "street1": "No 56 Wellington street ",
*		    "street2": "grand central london",
*		    "note": "sample note"
*		  },
*		  "rels": []
*		}
*
*
* @apiError No-Records Found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Record Not Found
*     {
*       "error": ["Employee Picture Not Found"]
*     }
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Record Not Found
*     {
*       "error": ["Employee Not Found"]
*     }
*
*/
