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

            return new KycCheckResponse($body);
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

            return new KycCheckResponse($body);
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

            return new KycCheckListResponse($body);
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

            return new KycCheckResponse($body);
        } catch (GuzzleException $e) {
            throw new KycException(
                "Failed to reprocess KYC check: " . $e->getMessage(),
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
