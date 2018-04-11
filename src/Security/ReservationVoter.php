<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 11/04/2018
 * Time: 17:00
 */

namespace App\Security;


use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ReservationVoter extends Voter
{

    const VIEW = 'view';
    const EDIT = 'edit';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::EDIT,self::VIEW])) {
            return false;
        }

        if(!$subject instanceof Reservation){
            return false;
        }
        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($subject,$user);
            case self::EDIT:
                return $this->canEdit($subject,$user);
        }
        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Reservation $reservation, User $user)
    {
        return $this->canEdit($reservation, $user);
    }

    private function canEdit(Reservation $reservation, User $user)
    {
        return $user === $reservation->getUser();
    }
}