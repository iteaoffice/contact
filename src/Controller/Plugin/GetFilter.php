<?php

/**
 * Jield BV all rights reserved
 *
 * @category    Application
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2004-2017 Jield BV (https://jield.nl)
 */

declare(strict_types=1);

namespace Contact\Controller\Plugin;

use Laminas\Http\Request;
use Laminas\Json\Json;
use Laminas\Mvc\Application;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Router\Http\RouteMatch;

use function base64_decode;
use function base64_encode;

/**
 * Class GetFilter
 *
 * @package Program\Controller\Plugin
 */
final class GetFilter extends AbstractPlugin
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var RouteMatch
     */
    private $routeMatch;
    /**
     * @var array
     */
    private $filter = [];
    /**
     * @var string|null
     */
    private $query;

    public function __construct(Application $application)
    {
        $this->routeMatch = $application->getMvcEvent()->getRouteMatch();
        $this->request    = $application->getMvcEvent()->getRequest();
    }

    public function __invoke(array $defaults = []): self
    {
        $encodedFilter = urldecode((string) $this->routeMatch->getParam('encodedFilter'));

        // Take the filter from the URL
        $filter = $defaults;
        if (! empty($base64decodedFilter = base64_decode($encodedFilter))) {
            $filter = (array) Json::decode($base64decodedFilter);
        }

        // If the form is submitted, refresh the URL
        if ($this->request->isGet() && null !== $this->request->getQuery('submit')) {
            $filter = $this->request->getQuery()->toArray()['filter'];
        }

        $filter['order'] = $this->request->getQuery('order') ?? $defaults['order'] ?? 'id';
        $filter['direction'] = $this->request->getQuery('direction') ?? $defaults['direction'] ?? 'desc';

        $this->filter = $filter;

        return $this;
    }

    public function getHash(): string
    {
        return base64_encode(Json::encode($this->filter));
    }

    public function getFilter(): array
    {
        return $this->filter;
    }

    public function getOrder(): ?string
    {
        return $this->filter['order'];
    }

    public function getDirection(): ?string
    {
        return $this->filter['direction'];
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }
}
