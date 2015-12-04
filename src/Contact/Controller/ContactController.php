<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Controller;

use Contact\Entity\AddressType;
use Contact\Entity\Photo;
use General\Service\EmailServiceAwareInterface;
use Search\Form\SearchResult;
use Search\Paginator\Adapter\SolariumPaginator;
use Search\Service\SearchServiceAwareInterface;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;
use Solarium\QueryType\Select\Query\Query as SolariumQuery;

/**
 * @category    Contact
 *
 * @method      ZfcUserAuthentication zfcUserAuthentication()
 * @method      FlashMessenger flashMessenger()
 * @method      bool isAllowed($resource, $action)
 */
class ContactController extends ContactAbstractController implements
    EmailServiceAwareInterface,
    SearchServiceAwareInterface
{
    /**
     * @return ViewModel
     */
    public function signatureAction()
    {
        $contactService = $this->getContactService()->setContact(
            $this->zfcUserAuthentication()->getIdentity()
        );

        return new ViewModel(['contactService' => $contactService]);
    }

    /**
     * Show the details of 1 project.
     *
     * @return \Zend\Stdlib\ResponseInterface|null
     */
    public function photoAction()
    {
        /**
         * @var $photo Photo
         */
        $photo = $this->getContactService()->findEntityById(
            'photo',
            $this->params('id')
        );

        /*
         * Do a check if the given has is correct to avoid guessing the image
         */
        if (is_null($photo) || is_null($photo->getPhoto()) || $this->params('hash')
            !== $photo->getHash()
        ) {
            return $this->notFoundAction();
        }

        $file = stream_get_contents($photo->getPhoto());

        /*
         * Check if the file is cached and if not, create it
         */
        if (!file_exists($photo->getCacheFileName())) {
            /*
             * The file exists, but is it not updated?
             */
            file_put_contents($photo->getCacheFileName(), $file);
        }

        $response = $this->getResponse();
        $response->getHeaders()
            ->addHeaderLine(
                'Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 36000)
            )
            ->addHeaderLine("Cache-Control: max-age=36000, must-revalidate")
            ->addHeaderLine("Pragma: public")
            ->addHeaderLine(
                'Content-Type: ' . $photo->getContentType()->getContentType()
            )
            ->addHeaderLine('Content-Length: ' . (string) strlen($file));
        $response->setContent($file);

        return $response;
    }

    /**
     * Ajax controller to update the OptIn.
     *
     * @return \Zend\View\Model\JsonModel
     */
    public function optInUpdateAction()
    {
        $optInId = (int) $this->params()->fromQuery('optInId');

        /*
         * We do not specify the enable, so we give the result
         */
        if (is_null($enable = $this->params()->fromQuery('enable'))) {
            return new JsonModel(
                [
                    'enable' => $this->getContactService()
                        ->hasOptInEnabledByContact(
                            $optInId,
                            $this->zfcUserAuthentication()->getIdentity()
                        ),
                    'id'     => $optInId,
                ]
            );
        }

        //Make a boolean value of $enable
        $enable = ($enable === 'true');

        $this->getContactService()->updateOptInForContact(
            $optInId,
            $enable,
            $this->zfcUserAuthentication()->getIdentity()
        );

        return new JsonModel(
            [
                'enable' => $enable,
                'id'     => $optInId,
            ]
        );
    }

    /**
     * Dedicated function which checks if the user has an active session.
     */
    public function hasSessionAction()
    {
        $viewModel = new ViewModel(
            ['hasSession' => $this->zfcUserAuthentication()->getIdentity()]
        );
        $viewModel->setTerminal(true);

        return $viewModel;
    }

    /**
     * Function to save the password of the user.
     */
    public function changePasswordAction()
    {
        $form = $this->getServiceLocator()->get('contact_password_form');
        $form->setInputFilter(
            $this->getServiceLocator()->get('contact_password_form_filter')
        );
        $form->setAttribute('class', 'form-horizontal');

        $data = array_merge_recursive(
            $this->getRequest()->getPost()->toArray(),
            $this->getRequest()->getFiles()->toArray()
        );

        $form->setData($data);
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $formData = $form->getData();
            if ($this->getContactService()->updatePasswordForContact(
                $formData['password'],
                $this->zfcUserAuthentication()->getIdentity()
            )
            ) {
                $this->flashMessenger()->setNamespace('success')->addMessage(
                    _("txt-password-successfully-been-updated")
                );

                return $this->redirect()->toRoute('community/contact/profile/view');
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * @return array|JsonModel
     */
    public function getAddressByTypeAction()
    {
        $contactId = (int) $this->getEvent()->getRequest()->getQuery()->get(
            'id'
        );
        $typeId = (int) $this->getEvent()->getRequest()->getQuery()->get(
            'typeId'
        );

        $this->getContactService()->setContactId($contactId);

        if ($this->getContactService()->isEmpty()) {
            return $this->notFoundAction();
        }

        switch ($typeId) {
            case AddressType::ADDRESS_TYPE_FINANCIAL:
                $address = $this->getContactService()->getAddressByTypeId(AddressType::ADDRESS_TYPE_FINANCIAL);
                break;
            case AddressType::ADDRESS_TYPE_BOOTH_FINANCIAL:
                $address = $this->getContactService()->getAddressByTypeId(AddressType::ADDRESS_TYPE_BOOTH_FINANCIAL);
                break;
            default:
                return $this->notFoundAction();
        }

        if (is_null($address)) {
            return new JsonModel();
        }

        return new JsonModel(
            [
                'address' => $address->getAddress()->getAddress(),
                'zipCode' => $address->getAddress()->getZipCode(),
                'city'    => $address->getAddress()->getCity(),
                'country' => $address->getAddress()->getCountry()->getId(),
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function searchAction()
    {
        $client = $this->getSearchService()->getSolrClient('contact');
        $form = new SearchResult();
        if ($this->getRequest()->isGet()) {
            //Only produce an isValid as we don't care about the result
            if (is_null($query = $this->getRequest()->getQuery()->get('query'))) {
                $query = '*';
            }

            $this->getSearchService()->facetSearchContact($query);

            if (is_array($facet = $this->getRequest()->getQuery()->get('facet'))) {
                foreach ($facet as $facetField => $values) {
                    $quotedValues = [];
                    foreach ($values as $value) {
                        $quotedValues[] = sprintf("\"%s\"", $value);
                    }

                    $this->getSearchService()->addFilterQuery(
                        $facetField, 
                        implode(' '.SolariumQuery::QUERY_OPERATOR_OR.' ', $quotedValues)
                    );
                }
            }

            $form->addSearchResults(
                $this->getSearchService()->getFacetSet(),
                $this->getSearchService()->getResultSet()->getFacetSet()
            );

            $form->setData($this->getRequest()->getQuery()->toArray());
        }

        $page = $this->getRequest()->getQuery()->get('page', 1);
        $paginator = new Paginator(new SolariumPaginator($client, $this->getSearchService()->getQuery()));
        $paginator->setDefaultItemCountPerPage(($page === 'all') ? PHP_INT_MAX : 16);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator->getDefaultItemCountPerPage()));

        //Create the search-result as url-field (without the page)
        $params = $this->getRequest()->getQuery()->toArray();
        unset($params['page']);

        return new ViewModel([
            'form'                   => $form,
            'paginator'              => $paginator,
            'queryParams'            => http_build_query($params),
            'routeName'              => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params'                 => $this->getEvent()->getRouteMatch()->getParams(),
            'currentPage'            => $this->getRequest()->getQuery()->get('page', 1),
            'lastPage'               => $paginator->getPageRange(),
            'showAlwaysFirstAndLast' => true,
        ]);
    }
}
