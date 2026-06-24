<?php

namespace App\Enums;

enum Type: string
{
    case Employee = "employee";
    case Client = "client";
    case Outsider = "outsider";
}
