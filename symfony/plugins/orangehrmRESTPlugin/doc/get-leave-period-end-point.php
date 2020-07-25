/**
* @api {get} /leave/period 03.Get Leave Period
* @apiName getLeavePeriods
* @apiGroup Leave
* @apiVersion 0.1.0
* @apiUse AdminDescription
*
*
*
* @apiSuccessExample Success-Response:
*     HTTP/1.1 200 OK
*
*            {
*             "data": [
*            [
*
*            "2017-01-01",
*            "2018-02-28"
*
*            ],
*            [
*
*            "2018-03-01",
*            "2019-02-28"
*
*            ]
*            ],
*            "rels": []
*            }
*
* @apiError No-Records Found.
*
* @apiErrorExample Error-Response:
*     HTTP/1.1 404 Not Found
*     {
*       "error": "No Leave Periods Found"
*     }
*/
