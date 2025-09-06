<?php

namespace App\Entity\Enums;

/**
 * Defines the Enum class Status that will be used to type the Status field of Offer Entity
 */

enum Service: string
{
    case Location = "LOCATION";
    case Sale = "SALE";
}