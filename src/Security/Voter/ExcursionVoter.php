<?php

namespace App\Security\Voter;

use App\Entity\Excursion;
use App\Entity\User;
use App\Enum\ExcursionStatus;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ExcursionVoter extends Voter
{
    // Actions
    public const CANCEL = 'CANCEL';
    public const PUBLISH = 'PUBLISH';
    public const REGISTER  = 'REGISTER';
    public const UNREGISTER = 'UNREGISTER';
    public const EDIT = 'EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Excursion &&
            in_array($attribute, [self::CANCEL, self::PUBLISH, self::REGISTER, self::UNREGISTER, self::EDIT]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var ?Excursion $excursion */
        $excursion = $subject;

        if (!$this->statusVote($attribute, $excursion->getStatus())) {
            return false;
        }

        return match ($attribute) {
            self::REGISTER => true,
            self::CANCEL, self::PUBLISH, self::EDIT => $user->getId() === $excursion->getOrganizer()->getId(),
            self::UNREGISTER => $excursion->getParticipants()->contains($user)
        };
    }

    private function statusVote(string $attribute, ?ExcursionStatus $status): bool
    {
        dump($status);

        return match ($attribute) {
            self::CANCEL, self::UNREGISTER => $status === ExcursionStatus::Open || $status === ExcursionStatus::Closed,
            self::PUBLISH, self::EDIT => $status === ExcursionStatus::Created,
            self::REGISTER => $status === ExcursionStatus::Open
        };
    }
}
