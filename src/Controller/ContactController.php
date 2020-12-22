<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Controller;

use Contact\Entity\AddressType;
use Contact\Entity\Contact;
use Contact\Form\Password;
use Contact\InputFilter\PasswordFilter;
use Contact\Search\Service\ProfileSearchService;
use Contact\Service\ContactService;
use Search\Form\SearchResult;
use Search\Paginator\Adapter\SolariumPaginator;
use Solarium\QueryType\Select\Query\Query as SolariumQuery;
use Laminas\Http\Request;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;

use function sprintf;

/**
 * Class ContactController
 * @package Contact\Controller
 */
final class ContactController extends ContactAbstractController
{
    private ContactService $contactService;
    private TranslatorInterface $translator;
    private ProfileSearchService $profileSearchService;

    public function __construct(
        ContactService $contactService,
        TranslatorInterface $translator,
        ProfileSearchService $profileSearchService
    ) {
        $this->contactService = $contactService;
        $this->translator = $translator;
        $this->profileSearchService = $profileSearchService;
    }

    public function changePasswordAction()
    {
        $form = new Password();
        $form->setInputFilter(new PasswordFilter());

        $data = $this->getRequest()->getPost()->toArray();

        $form->setData($data);
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $formData = $form->getData();
            $this->contactService->updatePasswordForContact($formData['password'], $this->identity());

            $this->flashMessenger()->addSuccessMessage(
                $this->translator->translate(
                    'txt-password-successfully-been-updated'
                )
            );

            return $this->redirect()->toRoute('community/contact/profile/view');
        }

        return new ViewModel(['form' => $form]);
    }

    public function getAddressByTypeAction(): JsonModel
    {
        $contactId = (int)$this->getEvent()->getRequest()->getQuery()->get('id');
        $typeId = (int)$this->getEvent()->getRequest()->getQuery()->get('typeId');

        $contact = $this->contactService->findContactById($contactId);

        if (null === $contact) {
            return new JsonModel([]);
        }

        switch ($typeId) {
            case AddressType::ADDRESS_TYPE_FINANCIAL:
                $address = $this->contactService
                    ->getAddressByTypeId($contact, AddressType::ADDRESS_TYPE_FINANCIAL);
                break;
            case AddressType::ADDRESS_TYPE_BOOTH_FINANCIAL:
                $address = $this->contactService
                    ->getAddressByTypeId($contact, AddressType::ADDRESS_TYPE_BOOTH_FINANCIAL);
                break;
            default:
                return new JsonModel([]);
        }

        if (null === $address) {
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

    public function searchAction(): ViewModel
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $page = $this->params('page', 1);

        $form = new SearchResult();
        $data = array_merge(['query' => '', 'facet' => []], $request->getQuery()->toArray());
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
            $this->profileSearchService->setSearch($data['query'], $searchFields);
            if (isset($data['facet'])) {
                foreach ($data['facet'] as $facetField => $values) {
                    $quotedValues = [];
                    foreach ($values as $value) {
                        $quotedValues[] = sprintf('"%s"', $value);
                    }

                    $this->profileSearchService->addFilterQuery(
                        $facetField,
                        implode(' ' . SolariumQuery::QUERY_OPERATOR_OR . ' ', $quotedValues)
                    );
                }
            }

            $form->addSearchResults(
                $this->profileSearchService->getQuery()->getFacetSet(),
                $this->profileSearchService->getResultSet()->getFacetSet()
            );
            $form->setData($data);
        }

        $paginator = new Paginator(
            new SolariumPaginator($this->profileSearchService->getSolrClient(), $this->profileSearchService->getQuery())
        );
        $paginator::setDefaultItemCountPerPage(($page === 'all') ? 1000000 : 25);
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
