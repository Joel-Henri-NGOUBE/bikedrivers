<?php

namespace App\Entity\Enums;

/**
 * Defines the Enum class ApplicationState that will be used to type the State field of Applications Entity
 */

enum ApplicationState: string
{
    case Rejected = 'REJECTED';
    case Accepted = 'ACCEPTED';
    case Evaluating = 'EVALUATING';
}
