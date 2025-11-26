<?php

namespace DataFabric\SDK;

/**
 * KYC Check Response
 *
 * Represents a single KYC check response from the API
 *
 * @package DataFabric\SDK
 */
class KycCheckResponse
{
    /** @var array<string, mixed> */
    protected array $data;

    /**
     * Constructor
     *
     * @param array<string, mixed> $data Raw response data from API
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the check ID
     *
     * @return string
     */
    public function getCheckId(): string
    {
        $checkId = $this->data['check_id'] ?? '';
        return is_string($checkId) ? $checkId : '';
    }

    /**
     * Get the check status
     *
     * @return string pending|in_progress|completed|failed
     */
    public function getStatus(): string
    {
        $status = $this->data['kyc_status'] ?? $this->data['status'] ?? '';
        return is_string($status) ? $status : '';
    }

    /**
     * Get the verification result
     *
     * @return string|null approved|rejected|review_required
     */
    public function getResult(): ?string
    {
        $result = $this->data['result'] ?? null;
        return is_string($result) ? $result : null;
    }

    /**
     * Get the risk score
     *
     * @return string|null low|medium|high
     */
    public function getRiskScore(): ?string
    {
        $score = $this->data['risk_score'] ?? null;
        return is_string($score) ? $score : null;
    }

    /**
     * Get verification details
     *
     * @return array<string, mixed>
     */
    public function getVerificationDetails(): array
    {
        $details = $this->data['verification_details'] ?? [];
        return is_array($details) ? $details : [];
    }

    /**
     * Check if the verification was approved
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->getResult() === 'approved';
    }

    /**
     * Check if the verification was rejected
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->getResult() === 'rejected';
    }

    /**
     * Check if the verification requires manual review
     *
     * @return bool
     */
    public function requiresReview(): bool
    {
        return $this->getResult() === 'review_required';
    }

    /**
     * Check if the verification is pending
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return in_array($this->getStatus(), ['pending', 'in_progress']);
    }

    /**
     * Check if the verification is completed
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->getStatus() === 'completed';
    }

    /**
     * Get the request ID for debugging
     *
     * @return string|null
     */
    public function getRequestId(): ?string
    {
        $requestId = $this->data['request_id'] ?? null;
        return is_string($requestId) ? $requestId : null;
    }

    /**
     * Get raw response data
     *
     * @return array<string, mixed>
     */
    public function getRawData(): array
    {
        return $this->data;
    }

    /**
     * Convert response to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Convert response to JSON string
     *
     * @return string
     */
    public function toJson(): string
    {
        $json = json_encode($this->data, JSON_PRETTY_PRINT);
        if ($json === false) {
            return '{}';
        }
        return $json;
    }
}
