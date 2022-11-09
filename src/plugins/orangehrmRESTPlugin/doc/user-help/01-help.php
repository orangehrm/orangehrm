<?php
/**
 * @api {get} /help/config 01.Get Help Config
 * @apiName getHelpConfig
 * @apiGroup User-Help
 * @apiVersion 1.3.0
 * @apiUse UserDescription_47
 *
 * @apiParam {query} [query] Search by article name mode category name.
 * @apiParam {mode} [mode] If mode is "category", will result in matching categories, otherwise matching articles. `
 * @apiParam {labels} [labels] Get the articles by article labels ( eg :- ['add_employee' , 'apply_leave'] )
 * @apiParam {categoryIds} [categoryIds] Articles in specified categories ( eg :- ['123456' , '654321'] )
 *
 * @apiSuccess {String} data.defaultRedirectUrl Redirect Url For The Help Main Page
 * @apiSuccess {Object[]} data.redirectUrls Redirect Categories Or Articles
 * @apiSuccess {String} redirectUrls.name Name Of The Article Or Category
 * @apiSuccess {String} redirectUrls.url Redirect Url Of The Article Or Category
 *
 * @apiSuccessExample Success-Response:
 * HTTP/1.1 200 OK
 *
 * {
 *       "data": {
 *           "defaultRedirectUrl": "https://opensourcehelp.orangehrm.com/hc/en-us",
 *           "redirectUrls": [
 *               {
 *                   "name": "Create master data for employee job information",
 *                   "url": "https://opensourcehelp.orangehrm.com/hc/en-us/articles/360018594080-Create-master-data-for-employee-job-information"
 *               },
 *               {
 *                   "name": "How to Add a User Account",
 *                   "url": "https://opensourcehelp.orangehrm.com/hc/en-us/articles/360018588480-How-to-Add-a-User-Account"
 *               },
 *               {
 *                   "name": "How to Approve Leave by Admin or Supervisor",
 *                   "url": "https://opensourcehelp.orangehrm.com/hc/en-us/articles/360018659479-How-to-Approve-Leave-by-Admin-or-Supervisor"
 *               },
 *           ]
 *       },
 *      "rels": []
 * }
 *
 *
 */
