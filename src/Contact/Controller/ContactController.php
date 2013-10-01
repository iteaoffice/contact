<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Controller
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Contact\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Contact\Service\ContactService;
use Contact\Service\FormService;
use Contact\Entity;

/**
 * @category    Contact
 * @package     Controller
 */
class ContactController extends AbstractActionController implements ServiceLocatorAwareInterface
{
    /**
     * @var ContactService
     */
    protected $contactService;
    /**
     * @var FormService
     */
    protected $formService;

    /**
     * Message container
     * @return array|void
     */
    public function indexAction()
    {
    }

    /**
     * Show the details of 1 project
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function photoAction()
    {
        $this->layout(false);
        $response = $this->getResponse();

        $contact = $this->getContactService()->findContactByHash(
            $this->getEvent()->getRouteMatch()->getParam('hash')
        );

        $response->getHeaders()
            ->addHeaderLine('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000))
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")
            ->addHeaderLine("Pragma: public");


        if (!is_null($contact->getPhoto())) {

            $file = stream_get_contents($contact->getPhoto()->getPhoto());

            $response->getHeaders()
                ->addHeaderLine('Content-Type: ' .
                $contact->getPhoto()->getContentType()->getContentType())
                ->addHeaderLine('Content-Length: ' . (string)strlen($file));

            $response->setContent($file);

            return $response;
        } else {
            $response->getHeaders()
                ->addHeaderLine('Content-Type: image/jpg');
            $response->setStatusCode(404);
        }
    }


    /**
     * Gateway to the Contact Service
     *
     * @return ContactService
     */
    public function getContactService()
    {
        return $this->getServiceLocator()->get('contact_generic_service');
    }

    /**
     * @param $contactService
     *
     * @return ContactController
     */
    public function setContactService($contactService)
    {
        $this->contactService = $contactService;

        return $this;
    }
}
