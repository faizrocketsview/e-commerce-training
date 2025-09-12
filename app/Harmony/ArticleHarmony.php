<?php

namespace App\Harmony;

use Harmony\Collection;
use Harmony\Collection\Body;
use Harmony\Collection\Query;
use Harmony\Collection\Request;
use Harmony\Connector;
use Harmony\Connector\Authorization;
use Harmony\Connector\Header;
use Harmony\Harmony;
use App\Models\Article;

class ArticleHarmony
{
    public static $baseUrl = 'http://harmony.test/';
    public static $token = 'BQDVunfMCl0H3VVsEd_nkwyJnpUeQ0';
    // public static $username;
    // public static $password;

    public static function connector(): Connector
    {
        return Harmony::createConnector(function (Connector $connector) {
            $connector
            ->authorization(function (Authorization $authorization) {
                $authorization->type('bearer'); //none, bearer, basic
            })
            ->header(function (Header $header){
                $header->type('Content-Type', 'application/json');
                $header->type('Accept', 'application/json');
            });
        });
    }

    public function index(): Collection
    {
        return Harmony::createCollection(function (Collection $collection) {
            $collection->group(function (Request $request) {
                $request->endpoint('api/articles');
                $request->method('GET');

                $request->query(function (Query $query){
                    $query->field('title');
                    $query->field('keywords');
                });
            });
        });
    }

    public function update(Article $article): Collection
    {
        return Harmony::createCollection(function (Collection $collection) use ($article) {
            $collection->group(function (Request $request) use ($article) {
                $request->endpoint('api/articles/' . $article->id);
                $request->method('PUT');

                $request->body(function (Body $body) {
                    $body->group('data', function (Body $body) {
                        $body->group('attributes', function (Body $body) {
                            $body->field('title');
                            $body->field('keywords');
                            $body->field('content');
                            $body->field('status');
                        });
                    });
                });
            });
        });
    }

    public function create(): Collection
    {
        return Harmony::createCollection(function (Collection $collection) {
            $collection->group(function (Request $request) {
                $request->endpoint('api/articles');
                $request->method('POST');

                $request->body(function (Body $body) {
                    $body->group('data', function (Body $body) {
                        $body->group('attributes', function (Body $body) {
                            $body->field('title');
                            $body->field('keywords');
                            $body->field('content');
                            $body->field('status');
                            $body->field('author_id');
                        });
                    });
                });
            });
        });
    }

    public function show(Article $article): Collection
    {
        return Harmony::createCollection(function (Collection $collection) use ($article) {
            $collection->group(function (Request $request) use ($article) {
                $request->endpoint('api/articles/' . $article->id);
                $request->method('GET');
            });
        });
    }

    public function delete(Article $article): Collection
    {
        return Harmony::createCollection(function (Collection $collection) use ($article) {
            $collection->group(function (Request $request) use ($article) {
                $request->endpoint('api/articles/' . $article->id);
                $request->method('DELETE');
            });
        });
    }

    public function search(): Collection
    {
        return Harmony::createCollection(function (Collection $collection) {
            $collection->group(function (Request $request) {
                $request->endpoint('api/articles/search');
                $request->method('GET');

                $request->query(function (Query $query) {
                    $query->field('q');
                    $query->field('category');
                    $query->field('author');
                    $query->field('status');
                    $query->field('page');
                    $query->field('per_page');
                });
            });
        });
    }
}
