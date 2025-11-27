<?php

namespace DataFabric\SDK;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * DataFabric KYC SDK
 *
 * Provides easy integration with the DataFabric KYC API
 *
 * @package DataFabric\SDK
 * @version 1.0.0
 */
class KycClient
{
    protected Client $httpClient;
    protected string $apiKey;
    protected string $baseUrl;
    protected bool $testMode;

    /**
     * SDK Configuration
     */
    public const VERSION = '1.0.0';
    public const USER_AGENT = 'DataFabric-KYC-SDK/1.0.0';

    /**
     * Constructor
     *
     * @param string $apiKey Your DataFabric API key (dfb_test_* or dfb_live_*)
     * @param string $baseUrl Base URL for API (default: https://datafabric.hiroshiaki.com)
     * @param array<string, mixed> $options Additional Guzzle client options
     */
    public function __construct(
        string $apiKey,
        string $baseUrl = 'https://datafabric.hiroshiaki.com',
        array $options = []
    ) {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->testMode = str_starts_with($apiKey, 'dfb_test_');

        $defaultOptions = [
            'base_uri' => $this->baseUrl,
            'headers' => [
                'X-Api-Key' => $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'timeout' => 30,
            'connect_timeout' => 10,
        ];

        $this->httpClient = new Client(array_merge($defaultOptions, $options));
    }

    /**
     * Create a new KYC check
     *
     * @param array<string, mixed> $data KYC check data
     * @return KycCheckResponse
     * @throws KycException
     */
    public function createCheck(array $data): KycCheckResponse
    {
        $this->validateCheckData($data);

        try {
            $response = $this->httpClient->post('/api/v1/kyc/checks', [
                'json' => $data,
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            if (!is_array($body)) {
                throw new KycException('Invalid response from API');
            }

            // Handle both response formats:
            // Format 1: {"status": "success", "check_id": "...", ...}
            // Format 2: {"status": "success", "data": {"check_id": "...", ...}}
            $responseData = $this->extractResponseData($body);

            return new KycCheckResponse($responseData);
        } catch (GuzzleException $e) {
            throw new KycException(
                "Failed to create KYC check: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Get KYC check status
     *
     * @param string $checkId The check ID
     * @return KycCheckResponse
     * @throws KycException
     */
    public function getCheck(string $checkId): KycCheckResponse
    {
        try {
            $response = $this->httpClient->get("/api/v1/kyc/checks/{$checkId}");

            $body = json_decode($response->getBody()->getContents(), true);
            if (!is_array($body)) {
                throw new KycException('Invalid response from API');
            }

            $responseData = $this->extractResponseData($body);
            return new KycCheckResponse($responseData);
        } catch (GuzzleException $e) {
            throw new KycException(
                "Failed to get KYC check: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * List KYC checks with optional filters
     *
     * @param array<string, mixed> $filters Optional filters (status, result, user_reference, per_page)
     * @return KycCheckListResponse
     * @throws KycException
     */
    public function listChecks(array $filters = []): KycCheckListResponse
    {
        try {
            $response = $this->httpClient->get('/api/v1/kyc/checks', [
                'query' => $filters,
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            if (!is_array($body)) {
                throw new KycException('Invalid response from API');
            }

            $responseData = $this->extractResponseData($body);
            return new KycCheckListResponse($responseData);
        } catch (GuzzleException $e) {
            throw new KycException(
                "Failed to list KYC checks: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Reprocess a failed KYC check
     *
     * @param string $checkId The check ID
     * @return KycCheckResponse
     * @throws KycException
     */
    public function reprocessCheck(string $checkId): KycCheckResponse
    {
        try {
            $response = $this->httpClient->post("/api/v1/kyc/checks/{$checkId}/reprocess");

            $body = json_decode($response->getBody()->getContents(), true);
            if (!is_array($body)) {
                throw new KycException('Invalid response from API');
            }

            $responseData = $this->extractResponseData($body);
            return new KycCheckResponse($responseData);
        } catch (GuzzleException $e) {
            throw new KycException(
                "Failed to reprocess KYC check: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Upload document image with AI OCR extraction
     *
     * @param string $checkId The check ID
     * @param string $imagePath Path to the image file
     * @param string $imageType Image type: front|back|selfie|proof_of_address
     * @param bool $autoExtract Enable AI OCR extraction (default: true)
     * @return KycDocumentResponse
     * @throws KycException
     */
    public function uploadDocument(
        string $checkId,
        string $imagePath,
        string $imageType,
        bool $autoExtract = true
    ): KycDocumentResponse {
        $this->validateDocumentUpload($imagePath, $imageType);

        try {
            $response = $this->httpClient->post("/api/v1/kyc/checks/{$checkId}/documents", [
                'multipart' => [
                    [
                        'name' => 'image',
                        'contents' => fopen($imagePath, 'r'),
                        'filename' => basename($imagePath),
                    ],
                    [
                        'name' => 'image_type',
                        'contents' => $imageType,
                    ],
                    [
                        'name' => 'auto_extract',
                        'contents' => $autoExtract ? 'true' : 'false',
                    ],
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            if (!is_array($body)) {
                throw new KycException('Invalid response from API');
            }

            $responseData = $this->extractResponseData($body);
            return new KycDocumentResponse($responseData);
        } catch (GuzzleException $e) {
            throw new KycException(
                "Failed to upload document: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Get all documents for a KYC check
     *
     * @param string $checkId The check ID
     * @return KycDocumentListResponse
     * @throws KycException
     */
    public function getDocuments(string $checkId): KycDocumentListResponse
    {
        try {
            $response = $this->httpClient->get("/api/v1/kyc/checks/{$checkId}/documents");

            $body = json_decode($response->getBody()->getContents(), true);
            if (!is_array($body)) {
                throw new KycException('Invalid response from API');
            }

            $responseData = $this->extractResponseData($body);
            return new KycDocumentListResponse($responseData);
        } catch (GuzzleException $e) {
            throw new KycException(
                "Failed to get documents: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Validate check data before sending
     *
     * @param array<string, mixed> $data
     * @throws KycException
     */
    protected function validateCheckData(array $data): void
    {
        $required = ['first_name', 'last_name', 'date_of_birth', 'document_type', 'document_number'];

        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new KycException("Missing required field: {$field}");
            }
        }

        $validDocTypes = ['passport', 'drivers_license', 'national_id', 'residence_permit'];
        if (!in_array($data['document_type'], $validDocTypes)) {
            throw new KycException("Invalid document_type. Must be one of: " . implode(', ', $validDocTypes));
        }

        // Validate date format
        $dateOfBirth = $data['date_of_birth'];
        if (!is_string($dateOfBirth)) {
            throw new KycException('date_of_birth must be a string');
        }
        $date = \DateTime::createFromFormat('Y-m-d', $dateOfBirth);
        if (!$date || $date->format('Y-m-d') !== $dateOfBirth) {
            throw new KycException("Invalid date_of_birth format. Must be YYYY-MM-DD");
        }
    }

    /**
     * Validate document upload parameters
     *
     * @param string $imagePath Path to the image file
     * @param string $imageType Image type
     * @throws KycException
     */
    protected function validateDocumentUpload(string $imagePath, string $imageType): void
    {
        // Check if file exists
        if (!file_exists($imagePath)) {
            throw new KycException("Image file not found: {$imagePath}");
        }

        // Check if file is readable
        if (!is_readable($imagePath)) {
            throw new KycException("Image file is not readable: {$imagePath}");
        }

        // Validate image type
        $validImageTypes = ['front', 'back', 'selfie', 'proof_of_address'];
        if (!in_array($imageType, $validImageTypes)) {
            throw new KycException("Invalid image_type. Must be one of: " . implode(', ', $validImageTypes));
        }

        // Check file size (max 10MB)
        $fileSize = filesize($imagePath);
        if ($fileSize === false || $fileSize > 10 * 1024 * 1024) {
            throw new KycException("Image file size must not exceed 10MB");
        }

        // Validate file type
        $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            throw new KycException("Failed to detect image file type");
        }
        $mimeType = finfo_file($finfo, $imagePath);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimeTypes)) {
            throw new KycException("Invalid image format. Must be JPEG, PNG, or WebP");
        }
    }

    /**
     * Extract response data handling different API response formats
     *
     * @param array<string, mixed> $body
     * @return array<string, mixed>
     */
    protected function extractResponseData(array $body): array
    {
        // Handle wrapped response: {"status": "success", "data": {...}}
        if (isset($body['data']) && is_array($body['data'])) {
            // Preserve root-level fields like 'status' while extracting data
            return array_merge(
                array_filter($body, fn($key) => $key !== 'data', ARRAY_FILTER_USE_KEY),
                $body['data']
            );
        }

        // Handle direct response: {"status": "success", "check_id": "...", ...}
        return $body;
    }

    /**
     * Check if SDK is in test mode
     *
     * @return bool
     */
    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    /**
     * Get the base URL
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
