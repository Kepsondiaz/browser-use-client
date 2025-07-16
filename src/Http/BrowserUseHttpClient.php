<?php

namespace KepsonDiaz\BrowserUseClient\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Exception;

class BrowserUseHttpClient
{
    private string $apiKey;
    private string $baseUrl = 'https://api.browser-use.com/v1';
    private PendingRequest $httpClient;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(300); // 5 minutes timeout for long-running tasks
    }

    /**
    * Run a task on Browser Use Cloud API
    * 
    * @param array $payload {
    *     The task configuration data
    *     @var string $task Required. Instructions for what the agent should do.
    *     @var array $secrets Optional. Dictionary of secrets to be used by the agent
    *     @var string[] $allowed_domains Optional. List of allowed domains.
    *     @var bool $save_browser_data Optional. If set to True, the browser cookies and other data will be saved.
    *     @var string $structured_output_json Optional.If set, the agent will use this JSON schema as the output model.
    *     @var string $llm_model Optional. LLM model to use (e.g. "gpt-4o").
    *     @var bool $use_adblock Optional.If set to True, the agent will use an adblocker.
    *     @var bool $use_proxy Optional. If set to True, the agent will use a (mobile) proxy.
    *     @var string $proxy_country_code Optional. Country code for residential proxy.
    *     @var bool $highlight_elements Optional. If set to True, the agent will highlight the elements on the page.
    *     @var string[] $included_file_names Optional. List of files to include.
    *     @var int $browser_viewport_width Optional. Browser viewport width in pixels.
    *     @var int $browser_viewport_height Optional. Browser viewport height in pixels.
    *     @var int $max_agent_steps Optional. Maximum number of agent steps.
    *     @var bool $enable_public_share Optional. Whether to enable public sharing.
    * }
     * @return array The response from the API
     * @throws Exception When the API request fails
     */
    public function runTask(array $payload): array
    {
        try {
            $response = $this->httpClient->post($this->baseUrl . '/run-task', $payload);
            
            return $this->handleResponse($response);
        } catch (Exception $e) {
            throw new Exception('Failed to run task: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Stop running task
     * 
     * @param string $taskId The task ID to get results for
     * @return array The task results
     * @throws Exception When the API request fails
     */
    public function stopTask(string $taskId): array
    {
        try {
            $response = $this->httpClient->put($this->baseUrl . '/stop-task' . $taskId );
            
            return $this->handleResponse($response);
        } catch (Exception $e) {
            throw new Exception('stop running task failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * pause running task
     * 
     * @param string $taskId The task ID to get results for
     * @return array The task results
     * @throws Exception When the API request fails
     */
    public function pauseTask(string $taskId): array
    {
        try {
            $response = $this->httpClient->put($this->baseUrl . '/pause-task' . $taskId );
            
            return $this->handleResponse($response);
        } catch (Exception $e) {
            throw new Exception('pause running task failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * resume running task
     * 
     * @param string $taskId The task ID to get results for
     * @return array The task results
     * @throws Exception When the API request fails
     */
    public function resumeTask(string $taskId): array
    {
        try {
            $response = $this->httpClient->put($this->baseUrl . '/resume-task' . $taskId );
            
            return $this->handleResponse($response);
        } catch (Exception $e) {
            throw new Exception('resume running task failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get task status by task ID
     * 
     * @param string $taskId The task ID to check
     * @return array The task status response
     * @throws Exception When the API request fails
     */
    public function getTaskStatus(string $taskId): array
    {
        try {
            $response = $this->httpClient->get($this->baseUrl . $taskId . '/status');
            
            return $this->handleResponse($response);
        } catch (Exception $e) {
            throw new Exception('Failed to get task status: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get task by task ID
     * 
     * @param string $taskId The task ID to get results for
     * @return array The task results
     * @throws Exception When the API request fails
     */
    public function getTask(string $taskId): array
    {
        try {
            $response = $this->httpClient->get($this->baseUrl . $taskId);
            
            return $this->handleResponse($response);
        } catch (Exception $e) {
            throw new Exception('Failed to get task: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get task media
     * 
     * @param string $taskId The task ID to get media for
     * @return array The task media
     * @throws Exception When the API request fails
     */
    public function getTaskMedia(string $taskId): array
    {
        try {
            $response = $this->httpClient->get($this->baseUrl . $taskId . '/media');
            
            return $this->handleResponse($response);
        } catch (Exception $e) {
            throw new Exception('Failed to get task media: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get a presigned URL for uploading a file
     * 
     * @param string $fileName Required. Name of the file to upload (e.g., 'data.csv', 'image.png')
     * @param string $contentType Required. Content type of the file (e.g., 'text/csv', 'image/png', 'application/pdf')
     * @return array The response containing the upload URL
     * @throws Exception When the API request fails
     */
    public function uploadPresignedUrl(string $fileName, string $contentType): array
    {
        try {
            $payload = [
                'file_name' => $fileName,
                'content_type' => $contentType
            ];
            
            $response = $this->httpClient->post($this->baseUrl . '/uploads/presigned-url', $payload);
            
            return $this->handleResponse($response);
        } catch (Exception $e) {
            throw new Exception('Failed to get upload presigned URL: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Handle the HTTP response and return appropriate data
     * 
     * @param Response $response The HTTP response
     * @return array The processed response data
     * @throws Exception When the response indicates an error
     */
    private function handleResponse(Response $response): array
    {
        if ($response->successful()) {
            return $response->json() ?? [];
        }

        $statusCode = $response->status();
        $errorData = $response->json();
        
        $errorMessage = $errorData['message'] ?? $errorData['error'] ?? 'Unknown error occurred';
        
        throw new Exception("API request failed with status {$statusCode}: {$errorMessage}", $statusCode);
    }

    /**
     * Set a custom base URL for the API
     * 
     * @param string $baseUrl The custom base URL
     * @return self
     */
    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    /**
     * Set custom timeout for HTTP requests
     * 
     * @param int $timeout Timeout in seconds
     * @return self
     */
    public function setTimeout(int $timeout): self
    {
        $this->httpClient = $this->httpClient->timeout($timeout);
        return $this;
    }
}