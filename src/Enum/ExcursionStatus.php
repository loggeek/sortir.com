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

    public function translate(): string
    {
        return match ($this) {
            self::Created => 'En création',
            self::Open => 'Ouvert',
            self::Closed => 'Fermé',
            self::Ongoing => 'En cours',
            self::Finished => 'Terminé',
            self::Cancelled => 'Annulé',
            self::Archived => 'Archivé'
        };
    }
}
