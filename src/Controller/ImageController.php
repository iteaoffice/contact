<?php
/**
 * ITEA Office all rights reserved
 *
 * @category  Content
 *
 * @author    Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license   https://itea3.org/license.txt proprietary
 *
 * @link      https://itea3.org
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Entity\Photo;
use Contact\Service\ContactService;
use Zend\Http\Response;

/**
 * Class ImageController
 *
 * @package Contact\Controller
 */
final class ImageController extends ContactAbstractController
{
    /**
     * @var ContactService
     */
    private $contactService;

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

        if (null === $photo) {
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
