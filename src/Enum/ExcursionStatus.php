<?php

namespace App\Enum;

enum ExcursionStatus
{
    case Created;
    case Open;
    case Closed;
    case Ongoing;
    case Finished;
    case Cancelled;
    case Archived;
}
