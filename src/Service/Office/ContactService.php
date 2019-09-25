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

/**
 * Class ContactService
 *
 * @package Contact\Service\Office
 */
class ContactService extends AbstractService
{
    public function findUpcomingLeave(OfficeContact $officeContact): LazyCriteriaCollection
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('officeContact', $officeContact))
            ->andWhere(Criteria::expr()->gte('dateStart', new DateTime()))
            ->orderBy(['dateStart' => Criteria::ASC]);

        /** @var LazyCriteriaCollection $upcomingLeave */
        $upcomingLeave = $this->entityManager->getRepository(Leave::class)->matching($criteria);

        return $upcomingLeave;
    }

    public function parseFullCalendarEvent(Leave $leave): array
    {
        return [
            'id'            => $leave->getId(),
            'start'         => $leave->getDateStart()->format('Y-m-d\TH:i:s'),
            'end'           => $leave->getDateEnd()->format('Y-m-d\TH:i:s'),
            'title'         => $leave->getDescription(),
            'allDay'        => true,
            'extendedProps' => [
                'hours'  => $leave->getHours(),
                'typeId' => ($leave->getType() ? $leave->getType()->getId() : null)
            ]
        ];
    }
}
