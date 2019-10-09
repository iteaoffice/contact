<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Service\Office;

use Contact\Entity\Office\Contact as OfficeContact;
use Contact\Entity\Office\Leave;
use Contact\Service\AbstractService;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\LazyCriteriaCollection;
use function array_column;

/**
 * Class ContactService
 *
 * @package Contact\Service\Office
 */
class ContactService extends AbstractService
{
    public function findLeave(OfficeContact $officeContact, DateTime $start, DateTime $end): LazyCriteriaCollection
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('officeContact', $officeContact))
            ->andWhere(Criteria::expr()->gte('dateStart', $start))
            ->andWhere(Criteria::expr()->lt('dateEnd', $end))
            ->orderBy(['dateStart' => Criteria::ASC]);

        /** @var LazyCriteriaCollection $leave */
        $leave = $this->entityManager->getRepository(Leave::class)->matching($criteria);

        return $leave;
    }

    public function findLeaveYears(OfficeContact $officeContact): array
    {
        $years = array_column(
            $this->entityManager->getRepository(Leave::class)->findYears($officeContact),
            'year'
        );

        return empty($years) ? [date('Y')] : $years;
    }

    public function parseFullCalendarEvent(Leave $leave): array
    {
        return [
            'id'            => $leave->getId(),
            'start'         => $leave->getDateStart()->format('Y-m-d\TH:i:s'),
            'end'           => $leave->getDateEnd()->add(new \DateInterval('P1D'))->format('Y-m-d\TH:i:s'),
            'title'         => $leave->getDescription(),
            'allDay'        => true,
            'extendedProps' => [
                'hours'  => $leave->getHours(),
                'typeId' => ($leave->getType() ? $leave->getType()->getId() : null)
            ]
        ];
    }
}
