<?php

namespace App\Enums;

enum SupportStatus: string
{
    case Closed = 'closed';
    case Solved = 'solved';
    case Pending = 'pending';
}
