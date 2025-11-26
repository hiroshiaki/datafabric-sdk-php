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
    /** @var array<mixed> */
    protected array $data;
    /** @var array<string, mixed> */
    protected array $pagination;

    /**
     * Constructor
     *
     * @param array<string, mixed> $response Raw response data from API
     */
    public function __construct(array $response)
    {
        $this->data = is_array($response['data'] ?? null) ? $response['data'] : [];
        $this->pagination = is_array($response['pagination'] ?? null) ? $response['pagination'] : [];
    }

    /**
     * Get array of KYC check responses
     *
     * @return KycCheckResponse[]
     */
    public function getChecks(): array
    {
        return array_map(fn($check) => new KycCheckResponse(is_array($check) ? $check : []), $this->data);
    }

    /**
     * Get total number of checks
     *
     * @return int
     */
    public function getTotal(): int
    {
        $total = $this->pagination['total'] ?? 0;
        return is_int($total) ? $total : 0;
    }

    /**
     * Get number of checks per page
     *
     * @return int
     */
    public function getPerPage(): int
    {
        $perPage = $this->pagination['per_page'] ?? 20;
        return is_int($perPage) ? $perPage : 20;
    }

    /**
     * Get current page number
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        $currentPage = $this->pagination['current_page'] ?? 1;
        return is_int($currentPage) ? $currentPage : 1;
    }

    /**
     * Get last page number
     *
     * @return int
     */
    public function getLastPage(): int
    {
        $lastPage = $this->pagination['last_page'] ?? 1;
        return is_int($lastPage) ? $lastPage : 1;
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
     * @return array<mixed>
     */
    public function getRawData(): array
    {
        return $this->data;
    }
}
