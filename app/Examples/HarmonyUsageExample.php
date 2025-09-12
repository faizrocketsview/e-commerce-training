<?php

namespace App\Examples;

use App\Models\Article;
use App\Harmony\ArticleHarmony;
use Harmony\Facade\Harmony;

/**
 * Harmony Package Usage Examples
 * 
 * This class demonstrates how to use the Harmony package for making API requests
 * to third-party services in a structured and maintainable way.
 */
class HarmonyUsageExample
{
    /**
     * Example 1: Get all articles with query parameters
     */
    public function getAllArticles()
    {
        // Define query parameters
        $query = [
            'title' => 'Laravel Testing',
            'keywords' => 'php,laravel,api'
        ];

        // Make the API request
        $response = Harmony::send(new ArticleHarmony()->index(), null, $query);

        // Handle the response
        if ($response->successful()) {
            $articles = $response->json();
            return $articles;
        }

        return $response->throw();
    }

    /**
     * Example 2: Create a new article
     */
    public function createArticle()
    {
        // Define the article data
        $body = [
            'title' => 'New Laravel Article',
            'keywords' => 'laravel,php,web-development',
            'content' => 'This is a comprehensive guide to Laravel development...',
            'status' => 'published',
            'author_id' => 1
        ];

        // Make the API request
        $response = Harmony::send(new ArticleHarmony()->create(), $body);

        // Handle the response
        if ($response->successful()) {
            $newArticle = $response->json();
            return $newArticle;
        }

        return $response->throw();
    }

    /**
     * Example 3: Update an existing article
     */
    public function updateArticle(Article $article)
    {
        // Define the updated data
        $body = [
            'title' => 'Updated Laravel Article Title',
            'keywords' => 'laravel,php,api,updated',
            'content' => 'This is the updated content for the article...',
            'status' => 'published'
        ];

        // Make the API request
        $response = Harmony::send(new ArticleHarmony()->update($article), $body);

        // Handle the response
        if ($response->successful()) {
            $updatedArticle = $response->json();
            return $updatedArticle;
        }

        return $response->throw();
    }

    /**
     * Example 4: Get a specific article
     */
    public function getArticle(Article $article)
    {
        // Make the API request
        $response = Harmony::send(new ArticleHarmony()->show($article));

        // Handle the response
        if ($response->successful()) {
            $articleData = $response->json();
            return $articleData;
        }

        return $response->throw();
    }

    /**
     * Example 5: Delete an article
     */
    public function deleteArticle(Article $article)
    {
        // Make the API request
        $response = Harmony::send(new ArticleHarmony()->delete($article));

        // Handle the response
        if ($response->successful()) {
            return ['message' => 'Article deleted successfully'];
        }

        return $response->throw();
    }

    /**
     * Example 6: Search articles with complex query parameters
     */
    public function searchArticles()
    {
        // Define search parameters
        $query = [
            'q' => 'Laravel API',
            'category' => 'web-development',
            'author' => 'john-doe',
            'status' => 'published',
            'page' => 1,
            'per_page' => 10
        ];

        // Make the API request
        $response = Harmony::send(new ArticleHarmony()->search(), null, $query);

        // Handle the response
        if ($response->successful()) {
            $searchResults = $response->json();
            return $searchResults;
        }

        return $response->throw();
    }

    /**
     * Example 7: Complete CRUD workflow
     */
    public function completeWorkflow()
    {
        try {
            // 1. Create a new article
            $newArticle = $this->createArticle();
            echo "Created article: " . $newArticle['data']['attributes']['title'] . "\n";

            // 2. Get all articles
            $articles = $this->getAllArticles();
            echo "Retrieved " . count($articles['data']) . " articles\n";

            // 3. Search for specific articles
            $searchResults = $this->searchArticles();
            echo "Found " . count($searchResults['data']) . " articles in search\n";

            // 4. Update an article (if we have one)
            if (!empty($articles['data'])) {
                $articleId = $articles['data'][0]['id'];
                $article = new Article(['id' => $articleId]);
                $updatedArticle = $this->updateArticle($article);
                echo "Updated article: " . $updatedArticle['data']['attributes']['title'] . "\n";
            }

            return ['success' => true, 'message' => 'Workflow completed successfully'];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Example 8: Error handling and logging
     */
    public function handleApiErrors()
    {
        try {
            $response = Harmony::send(new ArticleHarmony()->index());

            if ($response->successful()) {
                return $response->json();
            }

            // Handle different HTTP status codes
            switch ($response->status()) {
                case 401:
                    throw new \Exception('Unauthorized: Check your API token');
                case 403:
                    throw new \Exception('Forbidden: You do not have permission to access this resource');
                case 404:
                    throw new \Exception('Not Found: The requested resource was not found');
                case 422:
                    throw new \Exception('Validation Error: ' . $response->json('message'));
                case 500:
                    throw new \Exception('Server Error: The API server encountered an error');
                default:
                    throw new \Exception('API Error: ' . $response->body());
            }

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Harmony API Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => $response->status() ?? 'unknown'
            ];
        }
    }
}
