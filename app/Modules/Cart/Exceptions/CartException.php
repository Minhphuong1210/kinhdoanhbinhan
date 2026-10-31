<?php

namespace App\Modules\Cart\Exceptions;

/**
 * Lỗi nghiệp vụ giỏ hàng. Code là HTTP status trả về (mặc định 422).
 */
class CartException extends \RuntimeException
{
    public function __construct(string $message, int $code = 422)
    {
        parent::__construct($message, $code);
    }
}
