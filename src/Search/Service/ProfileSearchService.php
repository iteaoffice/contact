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

/***
 * Class ProfileSearchService
 *
 * @package Contact\Search\Service
 */
class ProfileSearchService extends AbstractSearchService
{
    public const SOLR_CONNECTION = 'contact_profile';

    public function setSearch(
        string $searchTerm,
        array $searchFields = [],
        string $order = '',
        string $direction = Query::SORT_ASC
    ): SearchServiceInterface {
        $this->setQuery($this->getSolrClient()->createSelect());
        $this->getQuery()->setQuery(static::parseQuery($searchTerm, $searchFields));

        $hasTerm = !in_array($searchTerm, ['*', ''], true);
        $hasSort = ($order !== '');

        if ($hasSort) {
            $this->getQuery()->addSort($order, $direction);
        }
        if ($hasTerm) {
            $this->getQuery()->addSort('score', Query::SORT_DESC);
        } else {
            $this->getQuery()->addSort('lastname_sort', Query::SORT_ASC);
        }

        $facetSet = $this->getQuery()->getFacetSet();
        $facetSet->createFacetField('organisation_type')->setField('organisation_type')->setMinCount(0)
            ->setExcludes(['organisation_type']);
        if (('*' !== $searchTerm) && (strlen($searchTerm) > 2)) {
            $facetSet->createFacetField('organisation')->setField('organisation')->setMinCount(1);
        }
        $facetSet->createFacetField('country')->setField('country')->setMinCount(1)->setExcludes(['country']);

        return $this;
    }
}
