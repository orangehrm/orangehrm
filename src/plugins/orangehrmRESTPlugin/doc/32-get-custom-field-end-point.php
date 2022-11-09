/**
* @api {get} /custom-field 32.Custom Field
* @apiName getCustomField
* @apiGroup Employee
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
* @apiSuccess {Number} id Field id.
* @apiSuccess {String} name Field name.
* @apiSuccess {String} type Field type.
* @apiSuccess {String} screen Applicable screen.
* @apiSuccess {String} extraData Extra data.
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
*		      "extraData": "Bsc,Msc,PostGrad"
*		    },
*		    {
*		      "id": "3",
*		      "name": "GPA",
*		      "type": "Text or Number",
*		      "screen": "personal",
*		      "extraData": null
*		    }
*		  ]
*		 }
*
*
*/
