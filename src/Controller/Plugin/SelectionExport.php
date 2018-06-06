<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Invoice
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        https://github.com/iteaoffice/invoice for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\Controller\Plugin;

use Contact\Entity\AddressType;
use Contact\Entity\Selection;
use Contact\Service\AddressService;
use Contact\Service\ContactService;
use Contact\Service\SelectionContactService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Zend\Http\Headers;
use Zend\Http\Response;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class SelectionExport
 *
 * @package Contact\Controller\Plugin
 */
final class SelectionExport extends AbstractPlugin
{
    public const EXPORT_CSV = 1;
    public const EXPORT_EXCEL = 2;

    /**
     * @var Spreadsheet
     */
    private $excel;
    /**
     * @var string
     */
    private $csv;
    /**
     * @var ContactService
     */
    private $contactService;
    /**
     * @var SelectionContactService
     */
    private $selectionContactService;
    /**
     * @var  AddressService
     */
    private $addressService;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var int
     */
    private $type = self::EXPORT_CSV;
    /**
     * @var Selection
     */
    private $selection;

    public function __construct(
        ContactService $contactService,
        SelectionContactService $selectionContactService,
        AddressService $addressService,
        TranslatorInterface $translator
    ) {
        $this->contactService = $contactService;
        $this->selectionContactService = $selectionContactService;
        $this->addressService = $addressService;
        $this->translator = $translator;
    }

    public function __invoke(Selection $selection, int $type): SelectionExport
    {
        $this->type = $type;
        $this->selection = $selection;

        switch ($this->type) {
            case self::EXPORT_CSV:
                $this->exportCSV();
                break;
            case self::EXPORT_EXCEL:
                $this->exportExcel();
                break;
        }

        return $this;
    }

    public function exportCSV(): SelectionExport
    {
        // Open the output stream
        $fh = fopen('php://output', 'wb');

        ob_start();

        fputcsv(
            $fh,
            [
                'Email',
                'First name',
                'Last name',
                'Organisation',
                'Country'
            ]
        );

        foreach ($this->selectionContactService->findContactsInSelection($this->selection, true) as $contact) {
            fputcsv(
                $fh,
                [
                    $contact['email'],
                    $contact['firstName'],
                    trim(sprintf("%s %s", $contact['middleName'], $contact['lastName'])),
                    $contact['contactOrganisation']['organisation']['organisation'] ?? null,
                    $contact['contactOrganisation']['organisation']['country']['iso3'] ?? null,
                ]
            );
        }

        $string = ob_get_clean();

        // Convert to UTF-16LE
        $string = mb_convert_encoding($string, 'UTF-16LE', 'UTF-8');

        // Prepend BOM
        $string = "\xFF\xFE" . $string;

        $this->csv = $string;

        return $this;
    }

    public function exportExcel(): SelectionExport
    {
        $this->excel = new Spreadsheet();

        $exportSheet = $this->excel->getActiveSheet();
        $exportSheet->setTitle($this->translator->translate('txt-selection-export'));
        $exportSheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $exportSheet->getPageSetup()->setFitToWidth(1);
        $exportSheet->getPageSetup()->setFitToHeight(0);

        $header = [
            'Email',
            'Title',
            'First name',
            'Last name',
            'Department',
            'Direct phone',
            'Mobile phone',
            'Organisation',
            'Organisation Type',
            'Organisation Country',
            'Organisation Country (iso3)',
            'Address',
            'ZIP Code',
            'City',
            'Country (address)',
        ];

        //Create the header row
        $column = 'A';
        foreach ($header as $item) {
            $exportSheet->setCellValue($column . '1', $item);
            $column++;
        }

        $row = 2;

        foreach ($this->selectionContactService->findContactsInSelection($this->selection) as $contact) {
            $country = $this->contactService->parseCountry($contact);
            /** @var AddressType $contactAddress */
            $contactAddress = $this->addressService->find(
                AddressType::class,
                AddressType::ADDRESS_TYPE_MAIL
            );
            $contactAddress = $this->addressService->findAddressByContactAndType(
                $contact,
                $contactAddress
            );

            $contactRow
                = [
                $contact->getEmail(),
                $this->contactService->parseAttention($contact),
                $contact->getFirstName(),
                trim(sprintf("%s %s", $contact->getMiddleName(), $contact->getLastName())),
                $contact->getDepartment(),
                $this->contactService->getDirectPhone($contact),
                $this->contactService->getMobilePhone($contact),
                $this->contactService->parseOrganisation($contact),
                null === $contact->getContactOrganisation() ? ''
                    : $contact->getContactOrganisation()->getOrganisation()->getType()->getType(),
                null === $country ? '' : $country->getCountry(),
                null === $country ? '' : $country->getIso3(),
                null === $contactAddress ? '' : $contactAddress->getAddress(),
                null === $contactAddress ? '' : $contactAddress->getZipCode(),
                null === $contactAddress ? '' : $contactAddress->getCity(),
                null === $contactAddress ? '' : $contactAddress->getCountry()->getCountry()
                ];

            $column = 'A';
            foreach ($contactRow as $item) {
                $exportSheet->setCellValue($column . $row, $item);

                $column++;
            }

            $row++;
        }

        return $this;
    }

    public function translate(string $translate): string
    {
        return $this->translator->translate($translate);
    }

    public function parseResponse(): Response
    {
        switch ($this->type) {
            case self::EXPORT_CSV:
                return $this->parseCsvResponse();
                break;

            case self::EXPORT_EXCEL:
                return $this->parseExcelResponse();
        }
    }

    public function parseCsvResponse(): Response
    {
        $response = new Response();

        // Prepare the response
        $response->setContent($this->csv);
        $response->setStatusCode(Response::STATUS_CODE_200);
        $headers = new Headers();
        $headers->addHeaders(
            [
                'Content-Disposition' => 'attachment; filename="Export ' . $this->selection->getSelection() . '.csv"',
                'Content-Type'        => 'text/csv',
                'Content-Length'      => \strlen($this->csv),
                'Expires'             => '@0', // @0, because ZF2 parses date as string to \DateTime() object
                'Cache-Control'       => 'must-revalidate',
                'Pragma'              => 'public',
            ]
        );
        $response->setHeaders($headers);

        return $response;
    }

    public function parseExcelResponse(): Response
    {
        $response = new Response();
        if (!($this->excel instanceof Spreadsheet)) {
            return $response->setStatusCode(Response::STATUS_CODE_404);
        }

        /** @var Xlsx $writer */
        $writer = IOFactory::createWriter($this->excel, 'Xlsx');

        ob_start();
        $gzip = false;
        // Gzip the output when possible. @see http://php.net/manual/en/function.ob-gzhandler.php
        if (ob_start('ob_gzhandler')) {
            $gzip = true;
        }
        $writer->save('php://output');
        if ($gzip) {
            ob_end_flush(); // Flush the gzipped buffer into the main buffer
        }
        $contentLength = ob_get_length();

        // Prepare the response
        $response->setContent(ob_get_clean());
        $response->setStatusCode(Response::STATUS_CODE_200);
        $headers = new Headers();
        $headers->addHeaders(
            [
                'Content-Disposition' => 'attachment; filename="Export ' . $this->selection->getSelection() . '.xlsx"',
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Length'      => $contentLength,
                'Expires'             => '@0', // @0, because ZF2 parses date as string to \DateTime() object
                'Cache-Control'       => 'must-revalidate',
                'Pragma'              => 'public',
            ]
        );
        if ($gzip) {
            $headers->addHeaders(['Content-Encoding' => 'gzip']);
        }
        $response->setHeaders($headers);

        return $response;
    }
}
