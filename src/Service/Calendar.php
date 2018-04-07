<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 04/04/18
 * Time: 21:58.
 */

namespace App\Service;

class Calendar
{
    private $months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    protected $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

    /** @var int $month */
    protected $month;

    /** @var int $year */
    protected $year;

    /**
     * Calendar constructor.
     *
     * @param int $month
     * @param int $year
     *
     * @throws \Exception
     */
    public function __construct(?int $month = null, ?int $year = null)
    {
        if (null === $month || $month < 1 || $month > 12) {
            $month = intval(date('m'));
        }
        if (null === $year) {
            $year = intval(date('Y'));
        }

        $this->month = $month;
        $this->year = $year;
    }

    /**
     * @return int
     */
    public function getMonth(): int
    {
        return $this->month;
    }

    /**
     * @param int $month
     */
    public function setMonth(int $month): void
    {
        $this->month = $month;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    /**
     * @return array
     */
    public function getDays(): array
    {
        return $this->days;
    }

    /**
     * Return Month to string.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->months[$this->getMonth() - 1].' '.$this->getYear();
    }

    /**
     * @return int
     */
    public function getWeeks(): int
    {
        // first day of the month
        $start = $this->getFirstDay();

        // Last day of the month
        $end = (clone $start)->modify('+1 month - 1 day');

        // number of weeks in the current month
        $weeks = intval($end->format('W')) - intval($start->format('W')) + 1;

        // condition to be sure to handle January case
        if ($weeks < 0) {
            $weeks = intval($end->format('W'));
        }

        // Handle case where $end->format('W') return 1
        if (1 === $weeks) {
            $weeks = 6;
        }

        return $weeks;
    }

    /**
     * Return first day of the month.
     *
     * @return \DateTime
     */
    public function getFirstDay(): \DateTime
    {
        return new \DateTime("{$this->getYear()}-{$this->getMonth()}-01");
    }

    /**
     * Return if day is part of the current month.
     *
     * @param \DateTime $dateTime
     *
     * @return bool
     */
    public function inThisMonth(\DateTime $dateTime): bool
    {
        return $this->getFirstDay()->format('Y-m') === $dateTime->format('Y-m');
    }

    /**
     * @return Calendar
     *
     * @throws \Exception
     */
    public function nextMonth(): Calendar
    {
        $month = $this->getMonth() + 1;
        $year = $this->getYear();
        if ($month > 12) {
            $month = 1;
            $year += 1;
        }

        return new Calendar($month, $year);
    }

    /**
     * @return Calendar
     *
     * @throws \Exception
     */
    public function previousMonth(): Calendar
    {
        $month = $this->getMonth() - 1;
        $year = $this->getYear();
        if ($month < 1) {
            $month = 12;
            $year -= 1;
        }

        return new Calendar($month, $year);
    }
}
