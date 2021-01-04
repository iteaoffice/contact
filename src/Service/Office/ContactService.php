<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
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
use function array_replace_recursive;

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

    public function findAllLeave(DateTime $start, DateTime $end): LazyCriteriaCollection
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->gte('dateStart', $start))
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

    public function parseOfficeCalendarEvent(Leave $leave): array
    {
        $customOptions = [
            'title'         => sprintf(
                '%s: %s (%.1fh)',
                $leave->getOfficeContact()->getContact()->getFirstName(),
                $leave->getDescription(),
                $leave->getHours()
            ),
            'extendedProps' => [
                'officeContactId' => $leave->getOfficeContact()->getId(),
                'description'     => $leave->getDescription()
            ]
        ];
        if ($leave->getOfficeContact()->getCalendarColor()) {
            $customOptions['backgroundColor'] = $leave->getOfficeContact()->getCalendarColor();
        }

        return $this->parseCalendarEvent($leave, $customOptions);
    }

    public function parseCalendarEvent(Leave $leave, array $customProperties = []): array
    {
        return array_replace_recursive(
            [
                'id'            => $leave->getId(),
                'start'         => $leave->getDateStart()->format('Y-m-d\TH:i:s'),
                'end'           => $leave->getDateEnd()->add(new \DateInterval('P1D'))->format('Y-m-d\TH:i:s'),
                'title'         => $leave->getDescription(),
                'allDay'        => true,
                'extendedProps' => [
                    'hours'  => $leave->getHours(),
                    'typeId' => ($leave->getType() ? $leave->getType()->getId() : null)
                ]
            ],
            $customProperties
        );
    }
}
