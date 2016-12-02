<?php

/*
 * The MIT License
 *
 * Copyright 2016 David Schoenbauer <dschoenbauer@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace DSchoenbauer\DotNotation\Exception;

use DSchoenbauer\DotNotation\Enum\ExceptionMessage;

/**
 * Exception thrown when the final node in the dot notation path needs to be an 
 * array but it is not.
 *
 * @since 1.1.0
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class TargetNotArrayException extends UnexpectedValueException {

    public function __construct($key = "", $message = "", $code = 0, $previous = null) {
        if ($message == "" && $key !== "") {
            $message = sprintf(ExceptionMessage::TARGET_NOT_ARRAY, $key);
        }
        parent::__construct($message, $code, $previous);
    }

}
