<?php

namespace DataFabric\SDK\Tests;

use DataFabric\SDK\KycCheckResponse;

class KycCheckResponseTest extends BaseTestCase
{
    public function testGetCheckIdWithCheckIdField(): void
    {
        $response = new KycCheckResponse([
            'check_id' => 'chk_test_123456',
            'status' => 'pending',
        ]);

        $this->assertEquals('chk_test_123456', $response->getCheckId());
    }

    public function testGetCheckIdWithIdField(): void
    {
        // API may return 'id' instead of 'check_id'
        $response = new KycCheckResponse([
            'id' => 'chk_test_789012',
            'status' => 'pending',
        ]);

        $this->assertEquals('chk_test_789012', $response->getCheckId());
    }

    public function testGetCheckIdWithIntegerId(): void
    {
        // API may return integer ID
        $response = new KycCheckResponse([
            'id' => 12345,
            'status' => 'pending',
        ]);

        $this->assertEquals('12345', $response->getCheckId());
    }

    public function testGetCheckIdFallbackOrder(): void
    {
        // 'check_id' should take precedence over 'id'
        $response = new KycCheckResponse([
            'check_id' => 'chk_preferred',
            'id' => 'chk_fallback',
            'status' => 'pending',
        ]);

        $this->assertEquals('chk_preferred', $response->getCheckId());
    }

    public function testGetCheckIdReturnsEmptyStringWhenMissing(): void
    {
        $response = new KycCheckResponse([
            'status' => 'pending',
        ]);

        $this->assertEquals('', $response->getCheckId());
    }

    public function testGetStatusWithKycStatusField(): void
    {
        $response = new KycCheckResponse([
            'id' => 'chk_test_123',
            'kyc_status' => 'completed',
        ]);

        $this->assertEquals('completed', $response->getStatus());
    }

    public function testGetStatusWithStatusField(): void
    {
        $response = new KycCheckResponse([
            'id' => 'chk_test_123',
            'status' => 'in_progress',
        ]);

        $this->assertEquals('in_progress', $response->getStatus());
    }

    public function testIsApproved(): void
    {
        $response = new KycCheckResponse([
            'id' => 'chk_test_123',
            'status' => 'completed',
            'result' => 'approved',
        ]);

        $this->assertTrue($response->isApproved());
        $this->assertFalse($response->isRejected());
        $this->assertFalse($response->requiresReview());
    }

    public function testIsRejected(): void
    {
        $response = new KycCheckResponse([
            'id' => 'chk_test_123',
            'status' => 'completed',
            'result' => 'rejected',
        ]);

        $this->assertTrue($response->isRejected());
        $this->assertFalse($response->isApproved());
        $this->assertFalse($response->requiresReview());
    }

    public function testRequiresReview(): void
    {
        $response = new KycCheckResponse([
            'id' => 'chk_test_123',
            'status' => 'completed',
            'result' => 'review_required',
        ]);

        $this->assertTrue($response->requiresReview());
        $this->assertFalse($response->isApproved());
        $this->assertFalse($response->isRejected());
    }

    public function testIsPending(): void
    {
        $responsePending = new KycCheckResponse([
            'id' => 'chk_test_123',
            'status' => 'pending',
        ]);

        $responseInProgress = new KycCheckResponse([
            'id' => 'chk_test_456',
            'status' => 'in_progress',
        ]);

        $this->assertTrue($responsePending->isPending());
        $this->assertTrue($responseInProgress->isPending());
    }

    public function testIsCompleted(): void
    {
        $response = new KycCheckResponse([
            'id' => 'chk_test_123',
            'status' => 'completed',
        ]);

        $this->assertTrue($response->isCompleted());
        $this->assertFalse($response->isPending());
    }

    public function testGetRiskScore(): void
    {
        $response = new KycCheckResponse([
            'id' => 'chk_test_123',
            'risk_score' => 'medium',
        ]);

        $this->assertEquals('medium', $response->getRiskScore());
    }

    public function testGetVerificationDetails(): void
    {
        $details = [
            'checks_passed' => ['identity', 'document'],
            'checks_failed' => ['address'],
        ];

        $response = new KycCheckResponse([
            'id' => 'chk_test_123',
            'verification_details' => $details,
        ]);

        $this->assertEquals($details, $response->getVerificationDetails());
    }

    public function testGetRequestId(): void
    {
        $response = new KycCheckResponse([
            'id' => 'chk_test_123',
            'request_id' => 'req_abc123',
        ]);

        $this->assertEquals('req_abc123', $response->getRequestId());
    }

    public function testGetExpiresAt(): void
    {
        $expiryDate = '2025-12-31T23:59:59Z';
        $response = new KycCheckResponse([
            'id' => 'chk_test_123',
            'expires_at' => $expiryDate,
        ]);

        $this->assertEquals($expiryDate, $response->getExpiresAt());
    }

    public function testIsExpiredReturnsFalseForFutureDate(): void
    {
        $futureDate = date('Y-m-d\TH:i:s\Z', strtotime('+1 year'));
        $response = new KycCheckResponse([
            'id' => 'chk_test_123',
            'expires_at' => $futureDate,
        ]);

        $this->assertFalse($response->isExpired());
    }

    public function testIsExpiredReturnsTrueForPastDate(): void
    {
        $pastDate = '2020-01-01T00:00:00Z';
        $response = new KycCheckResponse([
            'id' => 'chk_test_123',
            'expires_at' => $pastDate,
        ]);

        $this->assertTrue($response->isExpired());
    }

    public function testIsExpiredReturnsFalseWhenNoExpiryDate(): void
    {
        $response = new KycCheckResponse([
            'id' => 'chk_test_123',
        ]);

        $this->assertFalse($response->isExpired());
    }

    public function testGetRawData(): void
    {
        $data = [
            'id' => 'chk_test_123',
            'status' => 'pending',
            'custom_field' => 'custom_value',
        ];

        $response = new KycCheckResponse($data);

        $this->assertEquals($data, $response->getRawData());
    }

    public function testToArray(): void
    {
        $data = [
            'id' => 'chk_test_123',
            'status' => 'completed',
        ];

        $response = new KycCheckResponse($data);

        $this->assertEquals($data, $response->toArray());
    }

    public function testToJson(): void
    {
        $data = [
            'id' => 'chk_test_123',
            'status' => 'completed',
        ];

        $response = new KycCheckResponse($data);
        $json = $response->toJson();

        $this->assertJson($json);
        $this->assertEquals($data, json_decode($json, true));
    }

    public function testWrappedResponseWithDataField(): void
    {
        // Simulate API response with wrapped data: {"status": "success", "data": {...}}
        $wrappedData = [
            'status' => 'success',
            'data' => [
                'check_id' => 'chk_wrapped_123',
                'kyc_status' => 'completed',
                'result' => 'approved',
            ]
        ];

        // After extraction, data should be flattened
        $extractedData = [
            'status' => 'success',
            'check_id' => 'chk_wrapped_123',
            'kyc_status' => 'completed',
            'result' => 'approved',
        ];

        $response = new KycCheckResponse($extractedData);

        $this->assertEquals('chk_wrapped_123', $response->getCheckId());
        $this->assertEquals('completed', $response->getStatus());
        $this->assertEquals('approved', $response->getResult());
    }

    public function testDirectResponseWithoutDataField(): void
    {
        // Simulate direct API response: {"status": "success", "check_id": "...", ...}
        $directData = [
            'status' => 'success',
            'check_id' => 'chk_direct_456',
            'kyc_status' => 'pending',
        ];

        $response = new KycCheckResponse($directData);

        $this->assertEquals('chk_direct_456', $response->getCheckId());
        $this->assertEquals('pending', $response->getStatus());
    }
}
