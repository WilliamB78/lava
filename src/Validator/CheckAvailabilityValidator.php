<?php
/**
 * Created by PhpStorm.
 * User: hello
 * Date: 20/04/2018
 * Time: 11:40
 */

namespace App\Validator;


use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckAvailabilityValidator extends ConstraintValidator
{

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /**
     * CheckAvailabilityValidator constructor.
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        // TODO: Implement validate() method.
        $reservationRepository = $this->entityManager->getRepository(Reservation::class);
        $date = $value->getDate();
        $start = new \DateTime($date.$value->getStart());
        $start = $start->format('Y-m-d H:i:s');
        $end = new \DateTime($date.$value->getEnd());
        $end = $end->format('Y-m-d H:i:s');
        $roomId = $value->getRoom()->getId();


        $starIsAvailable = $reservationRepository->findReservationStartTimeAtDate($start, $roomId, $date);
        $endIsAvailable = $reservationRepository->findReservationEndTimeAtDate($end, $roomId, $date);
        $rangeIsAvailable = $reservationRepository->findReservationBetweenTime($start, $end, $roomId, $date);

        if (count($rangeIsAvailable) > 0) {
            $this->context->buildViolation('La salle n\'est pas disponible à cette plage horaire')
                ->atPath('start')
                ->addViolation();

            $this->context->buildViolation(null)
                ->atPath('end')
                ->addViolation();

            return;
        }
        if (count($starIsAvailable) > 0) {
            $this->context->buildViolation('La salle n\'est pas disponible a cette heure')
                ->atPath('start')
                ->addViolation();
        }
        if (count($endIsAvailable) > 0) {

            $this->context->buildViolation('La salle n\'est pas disponible a cette heure')
                ->atPath('end')
                ->addViolation();
        }

        //dump($starIsAvailable, $endIsAvailable);

    }
}