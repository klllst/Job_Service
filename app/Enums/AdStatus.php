<?php

namespace App\Enums;

enum AdStatus: string
{
    case Completed = 'completed';
    case Deleted = 'deleted';
    case Published = 'published';
    case Draft = 'draft';
    case Pending = 'pending';
}
