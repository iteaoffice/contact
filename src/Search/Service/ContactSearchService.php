<?php

/**
 * ITEA Office all rights reserved
 *
 * @category  Search
 *
 * @author    Bart van Eijck <bart.van.eijck@itea3.org>
 * @copyright Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Search\Service;

use Search\Service\AbstractSearchService;
use Search\Service\SearchServiceInterface;
use Solarium\QueryType\Select\Query\Query;

use function in_array;

/**
 * Contact Solr search service
 */
class ContactSearchService extends AbstractSearchService
{
    public const SOLR_CONNECTION = 'contact_contact';

    public function setSearch(
        string $searchTerm,
        array $searchFields = [],
        string $order = '',
        string $direction = Query::SORT_ASC
    ): SearchServiceInterface {
        $this->setQuery($this->getSolrClient()->createSelect());

        $this->getQuery()->setQuery(
            static::parseQuery(
                $searchTerm,
                [
                    'fullname_search',
                    'position_search',
                    'profile_search',
                    'organisation_search',
                    'organisation_type_search',
                    'country_search',
                    'email_search',
                    'cv_search',
                ]
            )
        );

        $hasTerm = ! in_array($searchTerm, ['*', ''], true);
        $hasSort = ($order !== '');

        if ($hasSort) {
            switch ($order) {
                case 'id':
                    $this->getQuery()->addSort('contact_id', $direction);
                    break;
                case 'name':
                    $this->getQuery()->addSort('lastname_sort', $direction);
                    break;
                case 'country':
                    $this->getQuery()->addSort('country_sort', $direction);
                    break;
                case 'organisation':
                    $this->getQuery()->addSort('organisation_sort', $direction);
                    break;
                case 'projects':
                    $this->getQuery()->addSort('projects', $direction);
                    break;

                default:
                    $this->getQuery()->addSort('contact_id', Query::SORT_DESC);
                    break;
            }
        }

        if ($hasTerm) {
            $this->getQuery()->addSort('score', Query::SORT_DESC);
        } elseif (! $hasSort) {
            $this->getQuery()->addSort('contact_id', Query::SORT_DESC);
        }

        $facetSet = $this->getQuery()->getFacetSet();
        $facetSet->createFacetField('org_type')->setField('organisation_type')->setSort('index')->setMinCount(0)
            ->setExcludes(['organisation_type']);
        if (('*' !== $searchTerm) && (strlen($searchTerm) > 2)) {
            $facetSet->createFacetField('organisation')->setField('organisation')->setSort('index')->setMinCount(1);
        }
        $facetSet->createFacetField('country')->setField('country')->setSort('index')->setMinCount(1)->setExcludes(
            ['country']
        );
        $facetSet->createFacetField('office')->setField('is_office_text')->setSort('index')->setMinCount(1)
            ->setExcludes(
                ['is_office_text']
            );
        $facetSet->createFacetField('funder')->setField('is_funder_text')->setSort('index')->setMinCount(1)
            ->setExcludes(
                ['is_funder_text']
            );
        $facetSet->createFacetField('access')->setField('access')->setSort('index')->setMinCount(1)->setExcludes(
            ['access']
        );
        $facetSet->createFacetField('opt_in')->setField('optin')->setSort('index')->setMinCount(1)->setExcludes(
            ['optin']
        );


        return $this;
    }
}
