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

namespace Authnetjson;

/**
 * Exception that is throw when transaction response data is requested on an API call that does not return any
 *
 * @author    John Conde <stymiee@gmail.com>
 * @copyright John Conde <stymiee@gmail.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @link      https://github.com/stymiee/authnetjson
 */
class AuthnetTransactionResponseCallException extends AuthnetException
{

}
