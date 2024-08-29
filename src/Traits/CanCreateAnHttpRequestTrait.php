<?php

namespace LaraCombs\Table\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use LaraCombs\Table\Exceptions\MissingHttpRequestsArgumentsException;

trait CanCreateAnHttpRequestTrait
{
    /**
     * The url for this element.
     */
    protected string $url;

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
    public function request(string $method, string $url, string $queryKey = 'ids'): static
    {
        $this->method = $method;
        $this->url = $url;
        $this->queryKey = $queryKey;

        return $this;
    }

    /**
     * This element create and send an HTTP GET request.
     */
    public function get(string $url, string $queryKey = 'ids'): static
    {
        return $this->request('GET', $url, $queryKey);
    }

    /**
     * This element create and send an HTTP HEAD request.
     */
    public function head(string $url, string $queryKey = 'ids'): static
    {
        return $this->request('HEAD', $url, $queryKey);
    }

    /**
     * This element create and send an HTTP PUT request.
     */
    public function put(string $url, string $queryKey = 'ids'): static
    {
        return $this->request('PUT', $url, $queryKey);
    }

    /**
     * This element create and send an HTTP POST request.
     */
    public function post(string $url, string $queryKey = 'ids'): static
    {
        return $this->request('POST', $url, $queryKey);
    }

    /**
     * This element create and send an HTTP PATCH request.
     */
    public function patch(string $url, string $queryKey = 'ids'): static
    {
        return $this->request('PATCH', $url, $queryKey);
    }

    /**
     * This element create and send an HTTP DELETE request.
     */
    public function delete(string $url, string $queryKey = 'ids'): static
    {
        return $this->request('DELETE', $url, $queryKey);
    }

    /**
     * Specify additional data that should be serialized to JSON for the colum.
     */
    protected function sharedData(Request $request): array
    {
        if (empty($this->url) || empty($this->queryKey)) {
            throw new MissingHttpRequestsArgumentsException('Missing url or query key for HTTP request.');
        }

        return [
            'url' => $this->url,
            'method' => Str::lower($this->method),
            'queryKey' => $this->queryKey,
        ];
    }
}
