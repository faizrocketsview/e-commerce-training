<?php

namespace App\Http\Controllers;

use App\Examples\HarmonyUsageExample;
use App\Models\Article;
use Illuminate\Http\Request;

/**
 * Test Controller for Harmony Package
 */

class HarmonyTestController extends Controller
{
    protected $harmonyExample;

    public function __construct(HarmonyUsageExample $harmonyExample)
    {
        $this->harmonyExample = $harmonyExample;
    }

    /**
     * Test getting all articles
     */
    public function testGetArticles()
    {
        try {
            $articles = $this->harmonyExample->getAllArticles();
            return response()->json([
                'success' => true,
                'data' => $articles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test creating a new article
     */
    public function testCreateArticle(Request $request)
    {
        try {
            $article = $this->harmonyExample->createArticle();
            return response()->json([
                'success' => true,
                'data' => $article
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test updating an article
     */
    public function testUpdateArticle(Request $request, $articleId)
    {
        try {
            $article = new Article(['id' => $articleId]);
            $updatedArticle = $this->harmonyExample->updateArticle($article);
            return response()->json([
                'success' => true,
                'data' => $updatedArticle
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test getting a specific article
     */
    public function testGetArticle($articleId)
    {
        try {
            $article = new Article(['id' => $articleId]);
            $articleData = $this->harmonyExample->getArticle($article);
            return response()->json([
                'success' => true,
                'data' => $articleData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test deleting an article
     */
    public function testDeleteArticle($articleId)
    {
        try {
            $article = new Article(['id' => $articleId]);
            $result = $this->harmonyExample->deleteArticle($article);
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test searching articles
     */
    public function testSearchArticles(Request $request)
    {
        try {
            $searchResults = $this->harmonyExample->searchArticles();
            return response()->json([
                'success' => true,
                'data' => $searchResults
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test complete workflow
     */
    public function testCompleteWorkflow()
    {
        try {
            $result = $this->harmonyExample->completeWorkflow();
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test error handling
     */
    public function testErrorHandling()
    {
        try {
            $result = $this->harmonyExample->handleApiErrors();
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
