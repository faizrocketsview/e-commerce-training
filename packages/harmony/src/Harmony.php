<?php

namespace Harmony;

use Closure;
use Harmony\Connector;
use Harmony\Collection;
use Illuminate\Support\Facades\Http;

class Harmony
{
    public static $className;

    public static function createConnector(Closure $callback): Connector
    {
        return new Connector($callback);
    }

    public static function createCollection(Closure $callback): Collection
    {
        self::$className = debug_backtrace()[1]['class'];
        return new Collection($callback);
    }

    public function send($api, $body = null, $query = null)
    {
        $harmony = new $this::$className;

        $baseUrl = $harmony::$baseUrl;
        $endpoint = $api->request->endpoint;
        $method = strtolower($api->request->method);
        $queryKeys = isset($api->request->query) ? $api->request->query->items : [];
        $bodyKeys = isset($api->request->body) ? $api->request->body->items : [];

        $queryParameters = $this->prepareQuery($queryKeys, $query);
        $bodyParameters = $this->prepareBody($bodyKeys, $body);

        $httpRequest = Http::withHeaders($harmony->connector()->header->headers)->withQueryParameters($queryParameters);

        if ($harmony->connector()->authorization->type == 'bearer') {
            $httpRequest->withToken($harmony::$token);
        }elseif ($harmony->connector()->authorization->type == 'basic') {
            $httpRequest->withBasicAuth($harmony::$username, $harmony::$password);
        }

        $httpRequest = $httpRequest->$method($baseUrl.$endpoint, $bodyParameters);
        $response = $httpRequest;

        return $response;
    }

    public function prepareQuery($queryKeys, $query)
    {
        $newQuery = [];
        foreach($queryKeys as $queryKey){
            $newQuery[$queryKey] = isset($query[$queryKey]) ? $query[$queryKey] : null;
        }

        return $newQuery;
    }

    public function prepareBody($bodyKeys, $body)
    {
        return $this->mapBodyKeys($bodyKeys, $body);
    }

    private function mapBodyKeys($keys, $data)
    {
        $result = [];

        foreach ($keys as $key) {
            if (is_array($key)) {
                // Handle nested structures: use $key for associative arrays
                foreach ($key as $key2 => $value2) {
                    $result[$key2] = $this->mapBodyKeys($key[$key2], isset($data[$key2]) ? $data[$key2] : []);
                }
            } else {
                // Directly assign the value from $data if present, or null if not
                $result[$key] = isset($data[$key]) ? $data[$key] : null;
            }
        }

        return $result;
    }
}