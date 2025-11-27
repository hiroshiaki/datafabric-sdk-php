<?php

namespace DataFabric\SDK;

/**
 * KYC Document Response
 *
 * Represents a document upload response with AI OCR data
 *
 * @package DataFabric\SDK
 */
class KycDocumentResponse
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
     * Get the document ID
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        $document = $this->data['document'] ?? [];
        if (!is_array($document)) {
            return null;
        }
        $id = $document['id'] ?? null;
        return is_int($id) ? $id : null;
    }

    /**
     * Get the image type
     *
     * @return string|null front|back|selfie|proof_of_address
     */
    public function getImageType(): ?string
    {
        $document = $this->data['document'] ?? [];
        if (!is_array($document)) {
            return null;
        }
        $imageType = $document['image_type'] ?? null;
        return is_string($imageType) ? $imageType : null;
    }

    /**
     * Check if document has OCR data
     *
     * @return bool
     */
    public function hasOcrData(): bool
    {
        $document = $this->data['document'] ?? [];
        if (!is_array($document)) {
            return false;
        }
        return ($document['has_ocr_data'] ?? false) === true;
    }

    /**
     * Get OCR extracted data
     *
     * @return array<string, mixed>|null
     */
    public function getOcrData(): ?array
    {
        $document = $this->data['document'] ?? [];
        if (!is_array($document)) {
            return null;
        }
        $ocrData = $document['ocr_data'] ?? null;
        return is_array($ocrData) ? $ocrData : null;
    }

    /**
     * Get extracted document type from OCR
     *
     * @return string|null
     */
    public function getExtractedDocumentType(): ?string
    {
        $ocrData = $this->getOcrData();
        if ($ocrData === null) {
            return null;
        }
        $docType = $ocrData['document_type'] ?? null;
        return is_string($docType) ? $docType : null;
    }

    /**
     * Get extracted full name from OCR
     *
     * @return string|null
     */
    public function getExtractedFullName(): ?string
    {
        $ocrData = $this->getOcrData();
        if ($ocrData === null) {
            return null;
        }
        $fullName = $ocrData['full_name'] ?? null;
        return is_string($fullName) ? $fullName : null;
    }

    /**
     * Get extracted first name from OCR
     *
     * @return string|null
     */
    public function getExtractedFirstName(): ?string
    {
        $ocrData = $this->getOcrData();
        if ($ocrData === null) {
            return null;
        }
        $firstName = $ocrData['first_name'] ?? null;
        return is_string($firstName) ? $firstName : null;
    }

    /**
     * Get extracted last name from OCR
     *
     * @return string|null
     */
    public function getExtractedLastName(): ?string
    {
        $ocrData = $this->getOcrData();
        if ($ocrData === null) {
            return null;
        }
        $lastName = $ocrData['last_name'] ?? null;
        return is_string($lastName) ? $lastName : null;
    }

    /**
     * Get extracted ID/document number from OCR
     *
     * @return string|null
     */
    public function getExtractedIdNumber(): ?string
    {
        $ocrData = $this->getOcrData();
        if ($ocrData === null) {
            return null;
        }
        $idNumber = $ocrData['id_number'] ?? null;
        return is_string($idNumber) ? $idNumber : null;
    }

    /**
     * Get extracted date of birth from OCR
     *
     * @return string|null
     */
    public function getExtractedDateOfBirth(): ?string
    {
        $ocrData = $this->getOcrData();
        if ($ocrData === null) {
            return null;
        }
        $dob = $ocrData['date_of_birth'] ?? null;
        return is_string($dob) ? $dob : null;
    }

    /**
     * Get extracted gender from OCR
     *
     * @return string|null
     */
    public function getExtractedGender(): ?string
    {
        $ocrData = $this->getOcrData();
        if ($ocrData === null) {
            return null;
        }
        $gender = $ocrData['gender'] ?? null;
        return is_string($gender) ? $gender : null;
    }

    /**
     * Get extracted nationality from OCR
     *
     * @return string|null
     */
    public function getExtractedNationality(): ?string
    {
        $ocrData = $this->getOcrData();
        if ($ocrData === null) {
            return null;
        }
        $nationality = $ocrData['nationality'] ?? null;
        return is_string($nationality) ? $nationality : null;
    }

    /**
     * Get extracted address from OCR
     *
     * @return string|null
     */
    public function getExtractedAddress(): ?string
    {
        $ocrData = $this->getOcrData();
        if ($ocrData === null) {
            return null;
        }
        $address = $ocrData['address'] ?? null;
        return is_string($address) ? $address : null;
    }

    /**
     * Get OCR confidence score
     *
     * @return int|null Confidence score (0-100)
     */
    public function getConfidence(): ?int
    {
        $ocrData = $this->getOcrData();
        if ($ocrData === null) {
            return null;
        }
        $confidence = $ocrData['confidence'] ?? null;
        return is_int($confidence) ? $confidence : null;
    }

    /**
     * Get OCR provider name
     *
     * @return string|null
     */
    public function getProvider(): ?string
    {
        $ocrData = $this->getOcrData();
        if ($ocrData === null) {
            return null;
        }
        $provider = $ocrData['provider'] ?? null;
        return is_string($provider) ? $provider : null;
    }

    /**
     * Get the status
     *
     * @return string
     */
    public function getStatus(): string
    {
        $status = $this->data['status'] ?? '';
        return is_string($status) ? $status : '';
    }

    /**
     * Check if upload was successful
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->getStatus() === 'success';
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
