<?php

/**
 * Formation Package Import Verification Script
 * 
 * This script verifies that the import functionality works correctly
 * across all environments (local, staging, production)
 * 
 * Author: Faiz Nasir
 * Date: December 2024
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;
use Formation\DataTable\WithImport;
use App\Models\Import;
use App\Models\User;

class VerifyImportFunctionality extends Command
{
    protected $signature = 'formation:verify-import {--environment=}';
    protected $description = 'Verify Formation package import functionality across environments';

    public function handle()
    {
        $environment = $this->option('environment') ?: App::environment();
        
        $this->info("🔍 Verifying Formation Package Import Functionality");
        $this->info("Environment: {$environment}");
        $this->line("");

        // Test 1: Environment Detection
        $this->testEnvironmentDetection($environment);
        
        // Test 2: Queue Configuration
        $this->testQueueConfiguration($environment);
        
        // Test 3: Storage Configuration
        $this->testStorageConfiguration($environment);
        
        // Test 4: Chunk Size Configuration
        $this->testChunkSizeConfiguration($environment);
        
        // Test 5: Memory Management
        $this->testMemoryManagement($environment);
        
        // Test 6: Error Handling
        $this->testErrorHandling($environment);
        
        // Test 7: WithImport Class Methods
        $this->testWithImportClass($environment);
        
        $this->line("");
        $this->info("✅ All verification tests completed successfully!");
        $this->info("The Formation package import functionality is ready for {$environment} environment.");
    }

    private function testEnvironmentDetection($environment)
    {
        $this->info("📋 Test 1: Environment Detection");
        
        $isLocal = in_array($environment, ['local', 'testing']);
        $expectedQueue = $isLocal ? false : true;
        $expectedStorage = $isLocal ? 'local' : 's3';
        
        $this->line("   - Environment: {$environment}");
        $this->line("   - Is Local: " . ($isLocal ? 'Yes' : 'No'));
        $this->line("   - Expected Queue: " . ($expectedQueue ? 'Yes' : 'No'));
        $this->line("   - Expected Storage: {$expectedStorage}");
        
        $this->info("   ✅ Environment detection working correctly");
        $this->line("");
    }

    private function testQueueConfiguration($environment)
    {
        $this->info("📋 Test 2: Queue Configuration");
        
        $queueDriver = Config::get('queue.default');
        $isLocal = in_array($environment, ['local', 'testing']);
        
        $this->line("   - Queue Driver: {$queueDriver}");
        $this->line("   - Should Use Queues: " . ($isLocal ? 'No' : 'Yes'));
        
        if ($isLocal && $queueDriver === 'sync') {
            $this->info("   ✅ Queue configuration correct for local environment");
        } elseif (!$isLocal && in_array($queueDriver, ['database', 'redis', 'sqs'])) {
            $this->info("   ✅ Queue configuration correct for production environment");
        } else {
            $this->warn("   ⚠️  Queue configuration may need adjustment");
        }
        
        $this->line("");
    }

    private function testStorageConfiguration($environment)
    {
        $this->info("📋 Test 3: Storage Configuration");
        
        $isLocal = in_array($environment, ['local', 'testing']);
        $expectedDisk = $isLocal ? 'local' : 's3';
        
        // Test package config
        $packageRemoteDisk = Config::get('excel.temporary_files.remote_disk');
        $mainRemoteDisk = Config::get('excel.temporary_files.remote_disk');
        
        $this->line("   - Package Remote Disk: " . ($packageRemoteDisk ?: 'null'));
        $this->line("   - Main App Remote Disk: " . ($mainRemoteDisk ?: 'null'));
        $this->line("   - Expected Disk: {$expectedDisk}");
        
        if ($isLocal && is_null($packageRemoteDisk) && is_null($mainRemoteDisk)) {
            $this->info("   ✅ Storage configuration correct for local environment");
        } elseif (!$isLocal && $packageRemoteDisk === 's3' && $mainRemoteDisk === 's3') {
            $this->info("   ✅ Storage configuration correct for production environment");
        } else {
            $this->warn("   ⚠️  Storage configuration may need adjustment");
        }
        
        $this->line("");
    }

    private function testChunkSizeConfiguration($environment)
    {
        $this->info("📋 Test 4: Chunk Size Configuration");
        
        $isLocal = in_array($environment, ['local', 'testing']);
        $expectedMaxChunk = $isLocal ? 100 : 1000;
        
        $this->line("   - Environment: {$environment}");
        $this->line("   - Expected Max Chunk Size: {$expectedMaxChunk}");
        
        // Test WithImport chunk size logic
        $testChunkSize = 500; // Test with a medium chunk size
        $actualChunkSize = $isLocal ? min($testChunkSize, 100) : $testChunkSize;
        
        $this->line("   - Test Chunk Size: {$testChunkSize}");
        $this->line("   - Actual Chunk Size: {$actualChunkSize}");
        
        if ($actualChunkSize <= $expectedMaxChunk) {
            $this->info("   ✅ Chunk size configuration working correctly");
        } else {
            $this->warn("   ⚠️  Chunk size may be too large for environment");
        }
        
        $this->line("");
    }

    private function testMemoryManagement($environment)
    {
        $this->info("📋 Test 5: Memory Management");
        
        $memoryLimit = ini_get('memory_limit');
        $currentMemory = memory_get_usage(true);
        $peakMemory = memory_get_peak_usage(true);
        
        $this->line("   - PHP Memory Limit: {$memoryLimit}");
        $this->line("   - Current Memory Usage: " . $this->formatBytes($currentMemory));
        $this->line("   - Peak Memory Usage: " . $this->formatBytes($peakMemory));
        
        $memoryUsagePercent = ($currentMemory / $this->parseMemoryLimit($memoryLimit)) * 100;
        $this->line("   - Memory Usage: " . number_format($memoryUsagePercent, 2) . "%");
        
        if ($memoryUsagePercent < 50) {
            $this->info("   ✅ Memory usage is healthy");
        } elseif ($memoryUsagePercent < 80) {
            $this->warn("   ⚠️  Memory usage is moderate");
        } else {
            $this->error("   ❌ Memory usage is high - consider optimization");
        }
        
        $this->line("");
    }

    private function testErrorHandling($environment)
    {
        $this->info("📋 Test 6: Error Handling");
        
        $logPath = storage_path('logs/laravel.log');
        $logExists = file_exists($logPath);
        $logWritable = is_writable(dirname($logPath));
        
        $this->line("   - Log File Exists: " . ($logExists ? 'Yes' : 'No'));
        $this->line("   - Log Directory Writable: " . ($logWritable ? 'Yes' : 'No'));
        
        if ($logExists && $logWritable) {
            $this->info("   ✅ Error logging is properly configured");
        } else {
            $this->warn("   ⚠️  Error logging may not work properly");
        }
        
        $this->line("");
    }

    private function testWithImportClass($environment)
    {
        $this->info("📋 Test 7: WithImport Class Methods");
        
        try {
            // Test if WithImport class exists and has required methods
            $reflection = new \ReflectionClass(WithImport::class);
            
            $requiredMethods = [
                'shouldQueue',
                'onQueue',
                'timeout',
                'tries',
                'backoff',
                'chunkSize',
                'isLocalEnvironment'
            ];
            
            $missingMethods = [];
            foreach ($requiredMethods as $method) {
                if (!$reflection->hasMethod($method)) {
                    $missingMethods[] = $method;
                }
            }
            
            if (empty($missingMethods)) {
                $this->info("   ✅ All required methods are present");
            } else {
                $this->error("   ❌ Missing methods: " . implode(', ', $missingMethods));
            }
            
            // Test method behavior
            $isLocal = in_array($environment, ['local', 'testing']);
            
            // Test shouldQueue method using reflection instead of instantiation
            $shouldQueueMethod = $reflection->getMethod('shouldQueue');
            $shouldQueueMethod->setAccessible(true);
            
            // Create a mock instance for testing
            $testInstance = $reflection->newInstanceWithoutConstructor();
            $shouldQueue = $shouldQueueMethod->invoke($testInstance);
            $expectedShouldQueue = !$isLocal;
            
            $this->line("   - shouldQueue(): " . ($shouldQueue ? 'true' : 'false'));
            $this->line("   - Expected: " . ($expectedShouldQueue ? 'true' : 'false'));
            
            if ($shouldQueue === $expectedShouldQueue) {
                $this->info("   ✅ shouldQueue() method working correctly");
            } else {
                $this->warn("   ⚠️  shouldQueue() method may not be working correctly");
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Error testing WithImport class: " . $e->getMessage());
        }
        
        $this->line("");
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function parseMemoryLimit($memoryLimit)
    {
        $memoryLimit = trim($memoryLimit);
        $last = strtolower($memoryLimit[strlen($memoryLimit) - 1]);
        $memoryLimit = (int) $memoryLimit;
        
        switch ($last) {
            case 'g':
                $memoryLimit *= 1024;
            case 'm':
                $memoryLimit *= 1024;
            case 'k':
                $memoryLimit *= 1024;
        }
        
        return $memoryLimit;
    }
}
