<?php

namespace DataFabric\SDK;

/**
 * KYC Check List Response
 *
 * Represents a paginated list of KYC checks from the API
 *
 * @package DataFabric\SDK
 */
class KycCheckListResponse
{
    protected array $data;
    protected array $pagination;

    /**
     * Constructor
     *
     * @param array $response Raw response data from API
     */
    public function __construct(array $response)
    {
        $this->data = $response['data'] ?? [];
        $this->pagination = $response['pagination'] ?? [];
    }

    /**
     * Get array of KYC check responses
     *
     * @return KycCheckResponse[]
     */
    public function getChecks(): array
    {
        return array_map(fn($check) => new KycCheckResponse($check), $this->data);
    }

    /**
     * Get total number of checks
     *
     * @return int
     */
    public function getTotal(): int
    {
        return $this->pagination['total'] ?? 0;
    }

    /**
     * Get number of checks per page
     *
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->pagination['per_page'] ?? 20;
    }

    /**
     * Get current page number
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->pagination['current_page'] ?? 1;
    }

    /**
     * Get last page number
     *
     * @return int
     */
    public function getLastPage(): int
    {
        return $this->pagination['last_page'] ?? 1;
    }

    /**
     * Check if there are more pages available
     *
     * @return bool
     */
    public function hasMorePages(): bool
    {
        return $this->getCurrentPage() < $this->getLastPage();
    }

    /**
     * Get raw data array
     *
     * @return array
     */
    public function getRawData(): array
    {
        return $this->data;
    }
}
