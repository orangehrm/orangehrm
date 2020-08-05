<?php
/**
 * @api {get} /myinfo 12.Employee Info
 * @apiName getMyInfo
 * @apiGroup User
 * @apiVersion 1.1.0
 * @apiUse UserDescription
 *
 * @apiParam {String='true','false'} [withPhoto] Specify whether with employee photo. Default `false`
 *
 * @apiSuccess {Object} employee  Employee details.
 * @apiSuccess {String} employeePhoto Base64 encoded employee picture.
 * @apiSuccess {Object} user  User details.
 *
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *
 *        {
 *          "data": {
 *            "employee": {
 *              "firstName": "Nina",
 *              "middleName": "Jane",
 *              "lastName": "Lewis",
 *              "code": "0014",
 *              "id": "1",
 *              "fullName": "Nina Jane Lewis",
 *              "status": "Active",
 *              "dob": "2016-05-04",
 *              "driversLicenseNumber": "444555124223",
 *              "licenseExpiryDate": "2017-02-09",
 *              "maritalStatus": "Married",
 *              "gender": "2",
 *              "otherId": "4646522",
 *              "nationality": "Armenian",
 *              "unit": "Marketing Unit",
 *              "jobTitle": "marketing"
 *            },
 *            "employeePhoto": "/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAMCAgICAgMCAgIDAwMDBAY",
 *            "user": {
 *              "userName":"Nina",
 *              "userRole":"ESS",
 *              "isSupervisor":true,
 *              "isProjectAdmin":false,
 *              "isManager":false,
 *              "isDirector":false,
 *              "isAcceptor":false,
 *              "isOfferer":false,
 *              "isHiringManager":false,
 *              "isInterviewer":false,
 *            }
 *          }
 *         }
 *
 * @apiError User Not Found.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 400 No Bound User
 *     {
 *       "error": ["No Bound User"]
 *     }
 *
 */
