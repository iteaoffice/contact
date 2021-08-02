<?php

/**
 * Jield BV all rights reserved
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2021 Jield BV (https://jield.nl)
 */

namespace Contact\Command;

use Contact\Service\ContactService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
final class Cleanup extends Command
{
    /** @var string */
    protected static $defaultName = 'contact:cleanup';
    private ContactService $contactService;

    public function __construct(ContactService $contactService)
    {
        parent::__construct(self::$defaultName);

        $this->contactService = $contactService;
    }

    protected function configure(): void
    {
        $this->setName(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start contact cleanup');
        $this->contactService->removeInactiveContacts($output);
        $output->writeln('Contact cleanup completed');

        return Command::SUCCESS;
    }
}
