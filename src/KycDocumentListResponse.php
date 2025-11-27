<?php

namespace DataFabric\SDK;

/**
 * KYC Document List Response
 *
 * Represents a list of documents for a KYC check
 *
 * @package DataFabric\SDK
 */
class KycDocumentListResponse
{
    /** @var array<mixed> */
    protected array $data;

    /**
     * Constructor
     *
     * @param array<string, mixed> $response Raw response data from API
     */
    public function __construct(array $response)
    {
        $this->data = is_array($response['documents'] ?? null) ? $response['documents'] : [];
    }

    /**
     * Get array of document responses
     *
     * @return array<string, mixed>[]
     */
    public function getDocuments(): array
    {
        return array_map(function ($doc) {
            return is_array($doc) ? $doc : [];
        }, $this->data);
    }

    /**
     * Get total number of documents
     *
     * @return int
     */
    public function getCount(): int
    {
        return count($this->data);
    }

    /**
     * Check if any documents have OCR data
     *
     * @return bool
     */
    public function hasOcrData(): bool
    {
        foreach ($this->data as $doc) {
            if (is_array($doc) && ($doc['has_ocr_data'] ?? false) === true) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get documents by image type
     *
     * @param string $imageType front|back|selfie|proof_of_address
     * @return array<string, mixed>[]
     */
    public function getDocumentsByType(string $imageType): array
    {
        return array_filter($this->data, function ($doc) use ($imageType) {
            return is_array($doc) && ($doc['image_type'] ?? null) === $imageType;
        });
    }

    /**
     * Get raw data array
     *
     * @return array<mixed>
     */
    public function getRawData(): array
    {
        return $this->data;
    }
}
