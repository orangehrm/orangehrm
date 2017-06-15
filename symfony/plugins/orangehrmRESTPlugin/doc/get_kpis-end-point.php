/**
* @api {get} /kpis 2.Get KPIS
* @apiName getKPIS
* @apiGroup Performance
* @apiVersion 0.1.0
*
*
* @apiSuccess  {Number} id  Job titile id.
* @apiSuccess  {jobTitileCode} Titile code.
* @apiSuccess  {String} JobTitileName  Title name.
* @apiSuccess  {String} jobDescription  Job description.
* @apiSuccess  {String} note  Note.
* @apiSuccess  {String} isDeleted  Is deleted or not.
* @apiSuccess  {String} kpi  User status.
* @apiSuccess  {Number} minRating  Min Rating.
* @apiSuccess  {Number} maxRating  Max rating.
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*		{
*		  "data": [
*		    {
*		      "id": "1",
*		      "jobTitleCode": {},
*		      "jobTitle": {
*			"id": "1",
*			"jobTitleName": "marketing",
*			"jobDescription": "",
*			"note": "",
*			"isDeleted": "0"
*		      },
*		      "kpi": "Test",
*		      "minRating": "0",
*		      "maxRating": "5"
*		    }
*		  ],
*		  "rels": []
*		}
*
*
*/
