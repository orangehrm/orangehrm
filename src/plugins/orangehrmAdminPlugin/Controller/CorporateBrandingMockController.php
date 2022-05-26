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

namespace OrangeHRM\Admin\Controller;

use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;

class CorporateBrandingMockController extends AbstractController
{
    /**
     * @return Response
     */
    public function getTheme(): Response
    {
        $response = new Response();
        $response->setContent(
            json_encode([
                "data" => [
                    "primaryColor" => "#FF7B1D",
                    "primaryFontColor" => "#FFFFFF",
                    "secondaryColor" => "#76BC21",
                    "secondaryFontColor" => "#FFFFFF",
                    "primaryGradientStartColor" => "#FF920B",
                    "primaryGradientEndColor" => "#F35C17",
                    "showSocialMediaImages" => true,
                    "clientLogo" => [
                        "id" => 1,
                        "fileSize" => 5028,
                        "fileType" => "image/png",
                        "filename" => "test.png"
                    ],
                    "clientBanner" => null,
                    "loginBanner" => null
                ],
                "meta" => []
            ])
        );

        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function bulidThemePreview(Request $request): Response
    {
        $response = new Response();
        $primaryColor = $request->request->get('primaryColor');
        $secondaryColor = $request->request->get('secondaryColor');
        $primaryFontColor = $request->request->get('primaryFontColor');
        $secondaryFontColor = $request->request->get('secondaryFontColor');
        $primaryGradientStartColor = $request->request->get('primaryGradientStartColor');
        $primaryGradientEndColor = $request->request->get('primaryGradientEndColor');

        $response->setContent(
            json_encode([
                "data" => [
                    "--oxd-primary-one-color" => $primaryColor,
                    "--oxd-primary-font-color" => $primaryFontColor,
                    "--oxd-secondary-four-color" =>  $secondaryColor,
                    "--oxd-secondary-font-color" => $secondaryFontColor,
                    "--oxd-primary-gradient-start-color" => $primaryGradientStartColor,
                    "--oxd-primary-gradient-end-color" => $primaryGradientEndColor,
                ],
                "meta" => []
            ])
        );

        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }

    /**
     * @return Response
     */
    public function resetTheme(): Response
    {
        $response = new Response();
        $response->setContent(
            json_encode([
                "data" => [],
                "meta" => []
            ])
        );

        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }
}
