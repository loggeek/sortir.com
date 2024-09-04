<?php

namespace App\Enum;

enum ExcursionStatus: string
{
    case Created = 'created';
    case Open = 'open';
    case Closed = 'closed';
    case Ongoing = 'ongoing';
    case Finished = 'finished';
    case Cancelled = 'cancelled';
    case Archived = 'archived';
}
