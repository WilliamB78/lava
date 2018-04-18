<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 09/04/2018
 * Time: 22:24.
 */

namespace App\Command;

use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Service\CronMail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronPresentCommand extends Command
{
    protected static $defaultName = 'cron:prevent';

    private $mailer;
    private $reservationRepository;
    private $userRepository;

    public function __construct(CronMail $mail, ReservationRepository $reservationRepository, UserRepository $userRepository, ?string $name = null)
    {
        parent::__construct($name);
        $this->mailer = $mail;
        $this->reservationRepository = $reservationRepository;
        $this->userRepository = $userRepository;
    }

    public function configure()
    {
        $this
            ->setDescription('Envois pour reservation accepté ou annulé')
            ->setHelp("Envois un email pour indiquer si la reservation n'est accepté ou annulé");
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $reservations = $this->reservationRepository->findWarningReservation();
        $secretaires = $this->userRepository->findSecretaires();

        if ($reservations !== []) {
            foreach ($secretaires as $secretaire) {
                $this->mailer->mailWarning($reservations, $secretaire);
            }
        }
    }
}
