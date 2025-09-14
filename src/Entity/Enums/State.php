<?php

namespace App\Entity\Enums;

/**
 * Defines the Enum class State that will be used to type the State field of Documents Entity
 */

enum State: string
{
    case Valid = 'VALID';
    case Invalid = 'INVALID';
    case Unevaluated = 'UNEVALUATED';
}
