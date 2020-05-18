<?php

declare(strict_types=1);

/**
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnConde\Authnet;

/**
 * Contains constant values.
 *
 * @package   AuthnetJSON
 * @author    John Conde <stymiee@gmail.com>
 * @copyright John Conde <stymiee@gmail.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @link      https://github.com/stymiee/authnetjson
 * @see       https://developer.authorize.net/api/reference/
 */
class AuthnetAcceptJs
{
    /**
     * @var string URL for Accept hosted form in the production environment
     */
    public const PRODUCTION_HOSTED_PAYMENT_URL = 'https://accept.authorize.net/payment/payment';

    /**
     * @var string URL for Accept hosted form in the sandbox environment
     */
    public const SANDBOX_HOSTED_PAYMENT_URL = 'https://test.authorize.net/payment/payment';

    /**
     * @var string URL for Accept hosted form in the production environment
     */
    public const PRODUCTION_HOSTED_CIM_URL = 'https://accept.authorize.net/profile/manage';

    /**
     * @var string URL for Accept hosted form in the sandbox environment
     */
    public const SANDBOX_HOSTED_CIM_URL = 'https://test.authorize.net/profile/manage';
}
