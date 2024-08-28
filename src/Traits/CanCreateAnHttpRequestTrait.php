<?php

namespace LaraCombs\Table\Traits;

use Illuminate\Http\Request;
use LaraCombs\Table\Exceptions\MissingHttpRequestsArgumentsException;

trait CanCreateAnHttpRequestTrait
{
    /**
     * The route for this element.
     */
    protected string $route;

    /**
     * The method for this element.
     */
    protected string $method;

    /**
     * The Query String Parameter for this element.
     */
    protected string $queryKey = 'ids';

    /**
     * This element create and send an HTTP request.
     */
    public function request(string $method, string $route, string $queryKey = 'ids'): static
    {
        $this->method = $method;
        $this->route = $route;
        $this->queryKey = $queryKey;

        return $this;
    }

    /**
     * This element create and send an HTTP GET request.
     */
    public function get(string $route, string $queryKey = 'ids'): static
    {
        return $this->request('GET', $route, $queryKey);
    }

    /**
     * This element create and send an HTTP HEAD request.
     */
    public function head(string $route, string $queryKey = 'ids'): static
    {
        return $this->request('HEAD', $route, $queryKey);
    }

    /**
     * This element create and send an HTTP PUT request.
     */
    public function put(string $route, string $queryKey = 'ids'): static
    {
        return $this->request('PUT', $route, $queryKey);
    }

    /**
     * This element create and send an HTTP POST request.
     */
    public function post(string $route, string $queryKey = 'ids'): static
    {
        return $this->request('POST', $route, $queryKey);
    }

    /**
     * This element create and send an HTTP PATCH request.
     */
    public function patch(string $route, string $queryKey = 'ids'): static
    {
        return $this->request('PATCH', $route, $queryKey);
    }

    /**
     * This element create and send an HTTP DELETE request.
     */
    public function delete(string $route, string $queryKey = 'ids'): static
    {
        return $this->request('DELETE', $route, $queryKey);
    }

    /**
     * Specify additional data that should be serialized to JSON for the colum.
     */
    protected function sharedData(Request $request): array
    {
        if (empty($this->route) || empty($this->queryKey)) {
            throw new MissingHttpRequestsArgumentsException('Missing route or query key for HTTP request.');
        }

        return [
            'route' => route($this->route),
            'method' => $this->method,
            'queryKey' => $this->queryKey,
        ];
    }
}
