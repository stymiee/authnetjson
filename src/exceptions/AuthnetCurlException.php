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

use Throwable;

/**
 * Exception that is throw when cURL experiences an unexpected error
 *
 * @author     John Conde <authnet@johnconde.net>
 * @copyright  John Conde <authnet@johnconde.net>
 * @license    http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @link       https://github.com/stymiee/authnetjson
 */
class AuthnetCurlException extends AuthnetException
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
