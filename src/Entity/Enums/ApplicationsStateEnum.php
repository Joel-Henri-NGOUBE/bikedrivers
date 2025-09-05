<?php

namespace App\Entity\Enums;

/**
 * Defines the Enum class State that will be used to type the State field of Documents Entity
 */

enum ApplicationState: string
{
    case Rejected = 'REJECTED';
    case  Accepted = 'ACCEPTED';
    case Evaluating = "EVALUATING";
}