<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Entity\AddressType;
use Contact\Entity\Contact;
use Contact\Form\Password;
use Contact\InputFilter\PasswordFilter;
use Search\Form\SearchResult;
use Search\Paginator\Adapter\SolariumPaginator;
use Solarium\QueryType\Select\Query\Query as SolariumQuery;
use Zend\Http\Request;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class ContactController
 *
 * @package Contact\Controller
 */
class ContactController extends ContactAbstractController
{
    /**
     * @return ViewModel
     */
    public function signatureAction(): ViewModel
    {
        return new ViewModel(
            [
                'contactService' => $this->getContactService(),
                'contact'        => $this->zfcUserAuthentication()->getIdentity(),
            ]
        );
    }

    /**
     * @return JsonModel
     */
    public function optInUpdateAction(): JsonModel
    {
        $optInId = (int)$this->params()->fromQuery('optInId');

        /*
         * We do not specify the enable, so we give the result
         */
        if (\is_null($enable = $this->params()->fromQuery('enable'))) {
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
        $enable = $enable === 'true';

        $this->getContactService()
            ->updateOptInForContact($optInId, $enable, $this->zfcUserAuthentication()->getIdentity());

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
        $viewModel = new ViewModel(['hasSession' => $this->zfcUserAuthentication()->getIdentity()]);
        $viewModel->setTerminal(true);

        return $viewModel;
    }

    /**
     * Function to save the password of the user.
     */
    public function changePasswordAction()
    {
        $form = new Password();
        $form->setInputFilter(new PasswordFilter());
        $form->setAttribute('class', 'form-horizontal');

        $data = $this->getRequest()->getPost()->toArray();

        $form->setData($data);
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $formData = $form->getData();
            $this->getContactService()
                ->updatePasswordForContact($formData['password'], $this->zfcUserAuthentication()->getIdentity());

            $this->flashMessenger()->setNamespace('success')
                ->addMessage($this->translate("txt-password-successfully-been-updated"));

            return $this->redirect()->toRoute('community/contact/profile/view');
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * @return JsonModel|ViewModel
     */
    public function getAddressByTypeAction()
    {
        $contactId = (int)$this->getEvent()->getRequest()->getQuery()->get('id');
        $typeId = (int)$this->getEvent()->getRequest()->getQuery()->get('typeId');

        $contact = $this->getContactService()->findContactById($contactId);

        if (\is_null($contact)) {
            return $this->notFoundAction();
        }

        switch ($typeId) {
            case AddressType::ADDRESS_TYPE_FINANCIAL:
                $address = $this->getContactService()
                    ->getAddressByTypeId($contact, AddressType::ADDRESS_TYPE_FINANCIAL);
                break;
            case AddressType::ADDRESS_TYPE_BOOTH_FINANCIAL:
                $address = $this->getContactService()
                    ->getAddressByTypeId($contact, AddressType::ADDRESS_TYPE_BOOTH_FINANCIAL);
                break;
            default:
                return $this->notFoundAction();
        }

        if (\is_null($address)) {
            return new JsonModel();
        }

        return new JsonModel(
            [
                'address' => $address->getAddress(),
                'zipCode' => $address->getZipCode(),
                'city'    => $address->getCity(),
                'country' => $address->getCountry()->getId(),
            ]
        );
    }

    /**
     * @return ViewModel
     */
    public function searchAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $searchService = $this->getProfileSearchService();
        $page = $this->params('page', 1);

        $form = new SearchResult();
        $data = array_merge(['query' => '*', 'facet' => []], $request->getQuery()->toArray());
        $searchFields = [
            'fullname_search',
            'position_search',
            'profile_search',
            'organisation_search',
            'organisation_type_search',
            'country_search',
            'cv_search',
        ];

        if ($request->isGet()) {
            $searchService->setSearch($data['query'], $searchFields);
            if (isset($data['facet'])) {
                foreach ($data['facet'] as $facetField => $values) {
                    $quotedValues = [];
                    foreach ($values as $value) {
                        $quotedValues[] = sprintf("\"%s\"", $value);
                    }

                    $searchService->addFilterQuery(
                        $facetField,
                        implode(' ' . SolariumQuery::QUERY_OPERATOR_OR . ' ', $quotedValues)
                    );
                }
            }

            $form->addSearchResults(
                $searchService->getQuery()->getFacetSet(),
                $searchService->getResultSet()->getFacetSet()
            );
            $form->setData($data);
        }

        $paginator = new Paginator(new SolariumPaginator($searchService->getSolrClient(), $searchService->getQuery()));
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? 1000000 : 16);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        return new ViewModel(
            [
                'form'                   => $form,
                'paginator'              => $paginator,
                'queryParams'            => http_build_query($data),
                'routeName'              => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
                'params'                 => $this->getEvent()->getRouteMatch()->getParams(),
                'currentPage'            => $page,
                'lastPage'               => $paginator->getPageRange(),
                'contact'                => new Contact(),
                'showAlwaysFirstAndLast' => true,
            ]
        );
    }
}
