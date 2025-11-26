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
    protected array $data;

    /**
     * Constructor
     *
     * @param array $data Raw response data from API
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
        return $this->data['check_id'] ?? '';
    }

    /**
     * Get the check status
     *
     * @return string pending|in_progress|completed|failed
     */
    public function getStatus(): string
    {
        return $this->data['kyc_status'] ?? $this->data['status'] ?? '';
    }

    /**
     * Get the verification result
     *
     * @return string|null approved|rejected|review_required
     */
    public function getResult(): ?string
    {
        return $this->data['result'] ?? null;
    }

    /**
     * Get the risk score
     *
     * @return string|null low|medium|high
     */
    public function getRiskScore(): ?string
    {
        return $this->data['risk_score'] ?? null;
    }

    /**
     * Get verification details
     *
     * @return array
     */
    public function getVerificationDetails(): array
    {
        return $this->data['verification_details'] ?? [];
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
        return $this->data['request_id'] ?? null;
    }

    /**
     * Get raw response data
     *
     * @return array
     */
    public function getRawData(): array
    {
        return $this->data;
    }

    /**
     * Convert response to array
     *
     * @return array
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
