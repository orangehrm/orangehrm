<?php
/**
 * @apiDefine AdminDescription
 * @apiDescription Support access using OAuth2 access token created with `client_credentials` and `password` grant type
 *
 * Required scope: `admin`
 *
 * Required scope by grant type;
 * - `client_credentials`: No need to request scopes. By default grant only for `admin` scope
 * - `password`: Required `admin` scope `Since OrangeHRM 4.5`
 *
 */
