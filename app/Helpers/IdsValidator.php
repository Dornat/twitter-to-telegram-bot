<?php

namespace App\Helpers;

use App\Exceptions\ArgumentValidationException;

class IdsValidator
{
    /**
     * @throws ArgumentValidationException
     */
    public function validate(array $idsArg): void
    {
        foreach ($idsArg as $item) {
            if (!preg_match('/.+:.+/', $item)) {
                throw new ArgumentValidationException();
            }
        }
    }
}
