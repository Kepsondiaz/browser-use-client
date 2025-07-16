<?php

namespace KepsonDiaz\Manager;

use KepsonDiaz\BrowserUseClient\Http\BrowserUseHttpClient;

class BrowserUseHttpClientManager
{
   protected BrowserUseHttpClient $browserUseHttpClient;

   public function __construct(string $apiKey)
   {
      $this->browserUseHttpClient = new BrowserUseHttpClient($apiKey);
   }

   public function runTask(array $payload): array
   {
      return $this->browserUseHttpClient->runTask($payload);
   }

   public function stopTask(string $taskId): array
   {
      return $this->browserUseHttpClient->stopTask($taskId);
   }

   public function pauseTask(string $taskId): array
   {
      return $this->browserUseHttpClient->pauseTask($taskId);
   }

   public function resumeTask(string $taskId): array
   {
      return $this->browserUseHttpClient->resumeTask($taskId);
   }

   public function getTaskStatus(string $taskId): array
   {
      return $this->browserUseHttpClient->getTaskStatus($taskId);
   }

   public function getTask(string $taskId): array
   {
      return $this->browserUseHttpClient->getTask($taskId);
   }

   public function getTaskMedia(string $taskId): array
   {
      return $this->browserUseHttpClient->getTaskMedia($taskId);
   }

   public function uploadPresignedUrl(string $fileName, string $contentType): array
   {
      return $this->browserUseHttpClient->uploadPresignedUrl($fileName, $contentType);
   }
}