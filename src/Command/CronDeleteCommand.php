<?php

namespace App\Command;

use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Service\CronMail;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronDeleteCommand extends Command
{
    protected static $defaultName = 'cron:delete';
    /** @var CronMail $mailer */
    private $mailer;
    /** @var ReservationRepository $reservationRepository */
    private $reservationRepository;
    /** @var UserRepository $userRepository */
    private $userRepository;
    /** @var EntityManagerInterface $em */
    private $em;

    public function __construct(CronMail $mail,EntityManagerInterface $em, ?string $name = null)
    {
        parent::__construct($name);
        $this->mailer = $mail;
        $this->reservationRepository = $em->getRepository(Reservation::class);
        $this->userRepository = $em->getRepository(User::class);;
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Envoi un mail pour indiquer les reservations qui vont être supprimé');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $reservations = $this->reservationRepository->findReservationNotComplete();
        $secretaires = $this->userRepository->findSecretaires();

        if ($reservations !== []) {
            foreach ($secretaires as $secretaire)
            {
                $this->mailer->mailReservationsDeleted($reservations, $secretaire);
                sleep(1);
            }
            $this->deleteReservations($reservations);
        }
    }

    private function deleteReservations($reservations)
    {
        foreach ($reservations as $reservation)
        {
            $this->em->remove($reservation);
            $this->em->flush();
        }
    }
}
