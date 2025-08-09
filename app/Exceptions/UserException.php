<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class UserException extends Exception
{
    protected $message;
    protected $code;

    public function __construct($message = "İstifadəçi əməliyyatında səhv baş verdi", $code = Response::HTTP_BAD_REQUEST)
    {
        $this->message = $message;
        $this->code = $code;
        parent::__construct($message, $code);
    }

    public function render($request)
    {
        return response()->json([
            'message' => $this->message,
            'error_type' => 'UserException'
        ], $this->code);
    }
}

