<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Core\Controller\Rest\V2;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     openapi="3.1.0",
 *     @OA\Components(
 *         @OA\RequestBody(
 *             request="DeleteRequestBody",
 *             @OA\JsonContent(
 *                 @OA\Property(property="ids", type="array", @OA\Items(type="integer")),
 *                 required={"ids"}
 *             )
 *         ),
 *         @OA\Response(
 *             response="DeleteResponse",
 *             description="Success",
 *             @OA\JsonContent(
 *                 @OA\Property(property="data", type="array", @OA\Items(type="integer")),
 *                 @OA\Property(property="meta", type="object")
 *             )
 *         ),
 *         @OA\Response(
 *             response="RecordNotFound",
 *             description="Record Not Found",
 *             @OA\JsonContent(
 *                 @OA\Property(
 *                     property="error",
 *                     type="object",
 *                     @OA\Property(property="status", type="string", default="404"),
 *                     @OA\Property(property="message", type="string")
 *                 ),
 *                 example={"error" : {"status" : "404", "message" : "Record Not Found"}}
 *             )
 *         ),
 *         @OA\Parameter(
 *             name="sortOrder",
 *             in="query",
 *             required=false,
 *             @OA\Schema(type="string", enum={"ASC", "DESC"})
 *         ),
 *         @OA\Parameter(
 *             name="limit",
 *             in="query",
 *             required=false,
 *             @OA\Schema(type="integer", default=50)
 *         ),
 *         @OA\Parameter(
 *             name="offset",
 *             in="query",
 *             required=false,
 *             @OA\Schema(type="integer", default=0)
 *         )
 *     )
 * )
 * @OA\Info(
 *     title="OrangeHRM Open Source : REST API v2 docs",
 *     version=\OrangeHRM\Config\Config::ORANGEHRM_API_VERSION,
 * )
 * @OA\Server(
 *     url="{orangehrm-url}",
 *     variables={
 *         @OA\ServerVariable(
 *             serverVariable="orangehrm-url",
 *             default="https://opensource-demo.orangehrmlive.com/index.php"
 *         )
 *     }
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="Cookie-HTTPS",
 *     type="apiKey",
 *     in="cookie",
 *     name="orangehrm"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="Cookie-HTTP",
 *     type="apiKey",
 *     in="cookie",
 *     name="_orangehrm"
 * )
 */
final class OpenApi
{
}
