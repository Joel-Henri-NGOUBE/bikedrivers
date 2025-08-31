<?php

namespace App\Entity\Enums;

/**
 * Defines the Enum class Status that will be used to type the Status field of Offer Entity
 */

enum Status: string
{
    case Availaible = 'AVAILABLE';
    case  Rented = 'RENTED';
    case Bought = "BOUGHT";
    case Inactive = "INACTIVE";
}