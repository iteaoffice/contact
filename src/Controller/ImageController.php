<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Entity\Photo;
use Contact\Service\ContactService;
use Laminas\Http\Response;

use function stream_get_contents;

/**
 * Class ImageController
 *
 * @package Contact\Controller
 */
final class ImageController extends ContactAbstractController
{
    private ContactService $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function contactPhotoAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var Photo $photo */
        $photo = $this->contactService->find(Photo::class, (int)$this->params('id'));

        if (null === $photo || null === $photo->getPhoto()) {
            return $response;
        }

        $response->getHeaders()
            ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine('Cache-Control: max-age=36000, must-revalidate')
            ->addHeaderLine('Pragma: public')
            ->addHeaderLine('Content-Type: ' . $photo->getContentType()->getContentType());

        $response->setContent(stream_get_contents($photo->getPhoto()));

        return $response;
    }
}
