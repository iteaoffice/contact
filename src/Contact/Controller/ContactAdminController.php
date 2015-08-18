<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */

namespace Contact\Controller;

use Contact\Controller\Plugin\GetFilter;
use Contact\Controller\Plugin\HandleImport;
use Contact\Form\ContactFilter;
use Contact\Form\Import;
use Contact\Form\Statistics;
use Contact\Service\StatisticsService;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Project\Service\ProjectServiceAwareInterface;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class ContactManagerController.
 *
 * @method HandleImport handleImport()
 * @method GetFilter getContactFilter()
 */
class ContactAdminController extends ContactAbstractController implements ProjectServiceAwareInterface
{
    /**
     * @return ViewModel
     */
    public function listAction()
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getContactFilter();
        $contactQuery = $this->getContactService()->findEntitiesFiltered(
            'contact',
            $filterPlugin->getFilter()
        );

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($contactQuery, false)));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 15);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

        $form = new ContactFilter($this->getContactService());

        $form->setData(['filter' => $filterPlugin->getFilter()]);

        return new ViewModel([
            'paginator'     => $paginator,
            'form'          => $form,
            'encodedFilter' => urlencode($filterPlugin->getHash()),
            'order'         => $filterPlugin->getOrder(),
            'direction'     => $filterPlugin->getDirection(),
        ]);
    }

    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        $contactService = $this->getContactService()->setContactId($this->params('id'));
        $contact = clone $contactService->getContact();
        $selections = $this->getSelectionService()->findSelectionsByContact($contact);
        $optIn = $this->getContactService()->findAll('optIn');

        if ($contactService->isEmpty()) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'contactService' => $contactService,
                'selections'     => $selections,
                'projects'       => $this->getProjectService()->findProjectParticipationByContact($contact),
                'projectService' => $this->getProjectService(),
                'optIn'          => $optIn,
            ]
        );
    }

    public function permitAction()
    {
        $contactService = $this->getContactService()->setContactId($this->params('id'));

        $this->getAdminService()->findPermitContactByContact($contactService->getContact());

        return new ViewModel(
            [
                'contactService' => $contactService,
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function impersonateAction()
    {
        $contactService = $this->getContactService()->setContactId($this->params('id'));
        $form = $this->getServiceLocator()->get('contact_impersonate_form');

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray()
        );

        $form->setData($data);
        $deeplink = false;
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $data = $form->getData();
            //Create a target
            $target = $this->getDeeplinkService()->findEntityById('target', $data['target']);
            $key = (!empty($data['key']) ? $data['key'] : null);
            //Create a deeplink for the user which redirects to the profile-page
            $deeplink = $this->getDeeplinkService()->createDeeplink($target, $contactService->getContact(), null, $key);
        }

        return new ViewModel(
            [
                'deeplink'       => $deeplink,
                'contactService' => $contactService,
                'form'           => $form,
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function statisticsAction()
    {
        $filter = $this->getRequest()->getQuery()->get('filter', []);
        /*
         * @var StatisticsService
         */
        $statisticsService = $this->getServiceLocator()->get(StatisticsService::class);

        $form = new Statistics();
        $form->setData($_GET);

        $contacts = [];
        if ($this->getRequest()->isGet() && $form->isValid()) {
            $statisticsService->setFilter($form->getData());
//            $contacts = $statisticsService->getContacts();
        }

        return new ViewModel(['form' => $form, 'contacts' => $contacts]);
    }

    /**
     * @return ViewModel
     */
    public function importAction()
    {
        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );
        $form = new Import();
        $form->setData($data);

        $handleImport = null;
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $handleImport = $this->handleImport(file_get_contents($data['file']['tmp_name']));
        }

        return new ViewModel(['form' => $form, 'handleImport' => $handleImport]);
    }

    /**
     * @return JsonModel
     */
    public function searchAction()
    {
        $search = $this->getRequest()->getPost()->get('search');

        $results = [];
        foreach ($this->getContactService()->searchContacts($search) as $result) {
            $text = trim(
                sprintf(
                    "%s, %s (%s)",
                    trim(sprintf("%s %s", $result['middleName'], $result['lastName'])),
                    $result['firstName'],
                    $result['email']
                )
            );

            /*
             * Do a fall-back to the email when the name is empty
             */
            if (strlen($text) === 0) {
                $text = $result['email'];
            }

            $results[] = [
                'value' => $result['id'],
                'text'  => $text,
            ];
        }

        return new JsonModel($results);
    }
}
