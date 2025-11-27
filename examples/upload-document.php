<?php

/**
 * DataFabric SDK - Upload Document with AI OCR Example
 *
 * This example demonstrates how to upload document images with AI-powered OCR extraction.
 * The API will automatically extract structured data from ID cards, passports, and driver's licenses.
 */

require __DIR__ . '/../vendor/autoload.php';

use DataFabric\SDK\KycClient;
use DataFabric\SDK\KycException;

// Initialize the client
$apiKey = getenv('DATAFABRIC_API_KEY') ?: 'dfb_test_your_api_key_here';
$client = new KycClient($apiKey);

echo "=== DataFabric SDK - Document Upload with AI OCR ===\n\n";

try {
    // Step 1: Create a KYC check
    echo "Step 1: Creating KYC check...\n";
    $checkResponse = $client->createCheck([
        'user_reference' => 'user_' . time(),
        'first_name' => 'Ahmad',
        'last_name' => 'Ibrahim',
        'date_of_birth' => '1995-03-20',
        'document_type' => 'national_id',
        'document_number' => 'TEMP123456',
        'country' => 'MY',
        'email' => 'ahmad@example.com'
    ]);

    $checkId = $checkResponse->getCheckId();
    echo "✅ KYC Check created: {$checkId}\n";
    echo "Status: " . $checkResponse->getStatus() . "\n\n";

    // Step 2: Upload front of ID card with AI OCR
    echo "Step 2: Uploading document image with AI OCR...\n";
    echo "Note: Replace the path below with your actual image file path\n";

    // Example image path - replace with your actual file
    $imagePath = __DIR__ . '/sample-id-front.jpg';

    // Check if file exists
    if (!file_exists($imagePath)) {
        echo "⚠️  Sample image not found at: {$imagePath}\n";
        echo "To use this example:\n";
        echo "1. Place your ID card image at: {$imagePath}\n";
        echo "2. Or modify the \$imagePath variable to point to your image\n";
        echo "3. Supported formats: JPEG, PNG, WebP (max 10MB)\n";
        echo "4. Recommended: min 1000px width for better OCR accuracy\n\n";
        exit(1);
    }

    // Upload with auto OCR extraction enabled
    $documentResponse = $client->uploadDocument(
        $checkId,
        $imagePath,
        'front',  // Image type: front|back|selfie|proof_of_address
        true      // Enable AI OCR extraction
    );

    echo "✅ Document uploaded successfully!\n";
    echo "Document ID: " . $documentResponse->getId() . "\n";
    echo "Image Type: " . $documentResponse->getImageType() . "\n";
    echo "Has OCR Data: " . ($documentResponse->hasOcrData() ? 'Yes' : 'No') . "\n\n";

    // Step 3: Display AI-extracted data
    if ($documentResponse->hasOcrData()) {
        echo "=== AI-Extracted Data (OCR) ===\n";
        echo "Document Type: " . ($documentResponse->getExtractedDocumentType() ?? 'N/A') . "\n";
        echo "Full Name: " . ($documentResponse->getExtractedFullName() ?? 'N/A') . "\n";
        echo "First Name: " . ($documentResponse->getExtractedFirstName() ?? 'N/A') . "\n";
        echo "Last Name: " . ($documentResponse->getExtractedLastName() ?? 'N/A') . "\n";
        echo "ID Number: " . ($documentResponse->getExtractedIdNumber() ?? 'N/A') . "\n";
        echo "Date of Birth: " . ($documentResponse->getExtractedDateOfBirth() ?? 'N/A') . "\n";
        echo "Gender: " . ($documentResponse->getExtractedGender() ?? 'N/A') . "\n";
        echo "Nationality: " . ($documentResponse->getExtractedNationality() ?? 'N/A') . "\n";
        echo "Address: " . ($documentResponse->getExtractedAddress() ?? 'N/A') . "\n";
        echo "Confidence: " . ($documentResponse->getConfidence() ?? 0) . "%\n";
        echo "OCR Provider: " . ($documentResponse->getProvider() ?? 'N/A') . "\n\n";

        // Step 4: You can now update the KYC check with the extracted data
        echo "💡 Tip: You can use the extracted data to update or verify the KYC check\n";
        echo "Example: Compare extracted name with the submitted name\n\n";
    }

    // Step 5: Upload additional documents if needed
    echo "Step 4: You can upload more documents...\n";
    echo "- Back of ID card: uploadDocument(\$checkId, \$backImagePath, 'back')\n";
    echo "- Selfie: uploadDocument(\$checkId, \$selfiePath, 'selfie')\n";
    echo "- Proof of address: uploadDocument(\$checkId, \$proofPath, 'proof_of_address')\n\n";

    // Step 6: Retrieve all documents
    echo "Step 5: Retrieving all uploaded documents...\n";
    $documentsResponse = $client->getDocuments($checkId);
    echo "Total Documents: " . $documentsResponse->getCount() . "\n";

    foreach ($documentsResponse->getDocuments() as $doc) {
        echo "- Document #" . ($doc['id'] ?? 'N/A') . " (" . ($doc['image_type'] ?? 'unknown') . ")";
        if (($doc['has_ocr_data'] ?? false)) {
            echo " ✨ Has OCR data";
        }
        echo "\n";
    }
    echo "\n";

    // Step 7: Check final status
    echo "Step 6: Checking final KYC status...\n";
    $finalCheck = $client->getCheck($checkId);
    echo "Status: " . $finalCheck->getStatus() . "\n";
    echo "Result: " . ($finalCheck->getResult() ?? 'Processing') . "\n";

    if ($finalCheck->isApproved()) {
        echo "✅ KYC Approved!\n";
        echo "Risk Score: " . $finalCheck->getRiskScore() . "\n";
    } elseif ($finalCheck->requiresReview()) {
        echo "⚠️  Manual review required\n";
    } elseif ($finalCheck->isPending()) {
        echo "⏳ Still processing...\n";
        echo "Tip: Use webhooks instead of polling for status updates\n";
    }

    if ($finalCheck->getExpiresAt()) {
        echo "Expires At: " . $finalCheck->getExpiresAt() . "\n";
        echo "Is Expired: " . ($finalCheck->isExpired() ? 'Yes' : 'No') . "\n";
    }

    echo "\n=== Tips for Best Results ===\n";
    echo "1. Upload high-quality images (min 1000px width)\n";
    echo "2. Ensure documents are clearly visible and well-lit\n";
    echo "3. Avoid glare, shadows, or obstructions\n";
    echo "4. Supported documents: Malaysian IC, Passports, Driver's Licenses, National IDs\n";
    echo "5. AI Models used: Google Gemini 2.0 Flash, GPT-4o Vision, Claude 3 Opus\n";
    echo "6. OCR processing takes 2-5 seconds\n";
    echo "7. Use webhooks for async notifications instead of polling\n";

} catch (KycException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";

    if (str_contains($e->getMessage(), 'not found')) {
        echo "\n💡 Make sure the image file exists and the path is correct\n";
    } elseif (str_contains($e->getMessage(), 'readable')) {
        echo "\n💡 Check file permissions - the file must be readable\n";
    } elseif (str_contains($e->getMessage(), 'size')) {
        echo "\n💡 Image must be under 10MB\n";
    } elseif (str_contains($e->getMessage(), 'format')) {
        echo "\n💡 Only JPEG, PNG, and WebP images are supported\n";
    }
}

echo "\n=== Example Complete ===\n";
