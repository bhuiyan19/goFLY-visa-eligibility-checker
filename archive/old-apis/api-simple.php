<?php
/**
 * Visa Checker API Endpoint
 * Receives lead data and saves to Fluent Forms
 */

// CORS headers - allow requests from your Vercel domain
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit;
}

// Load WordPress
require_once(__DIR__ . '/../../../../wp-load.php');

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate data
if (!$data || !isset($data['name']) || !isset($data['phone'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields: name and phone'
    ]);
    exit;
}

// Sanitize input data
$name = sanitize_text_field($data['name']);
$phone = sanitize_text_field($data['phone']);
$email = isset($data['email']) ? sanitize_email($data['email']) : '';
$country = isset($data['country']) ? sanitize_text_field($data['country']) : '';
$countryId = isset($data['countryId']) ? sanitize_text_field($data['countryId']) : '';
$score = isset($data['score']) ? intval($data['score']) : 0;
$percentage = isset($data['percentage']) ? intval($data['percentage']) : 0;
$timestamp = isset($data['timestamp']) ? sanitize_text_field($data['timestamp']) : current_time('mysql');

try {
    // Check if Fluent Forms is active
    if (!function_exists('wpFluentForm')) {
        throw new Exception('Fluent Forms is not active');
    }

    // Your Fluent Forms Form ID
    $form_id = 6959; // CHANGE THIS to your actual Form ID

    // Prepare submission data for Fluent Forms
    $submission_data = [
        'input_name' => $name,
        'input_phone' => $phone,
        'input_email' => $email,
        'input_country' => $country,
        'input_country_id' => $countryId,
        'input_score' => $score,
        'input_percentage' => $percentage
    ];

    // Use Fluent Forms API to create submission
    global $wpdb;

    // Insert into Fluent Forms submissions table
    $table_name = $wpdb->prefix . 'fluentform_submissions';

    $insert_data = [
        'form_id' => $form_id,
        'serial_number' => null, // Auto-generated
        'response' => json_encode($submission_data),
        'source_url' => isset($data['source_url']) ? $data['source_url'] : '',
        'user_id' => get_current_user_id(),
        'browser' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
        'device' => 'unknown',
        'ip' => $_SERVER['REMOTE_ADDR'],
        'created_at' => current_time('mysql'),
        'updated_at' => current_time('mysql'),
        'status' => 'unread',
        'is_favourite' => 0
    ];

    $inserted = $wpdb->insert($table_name, $insert_data);

    if ($inserted === false) {
        throw new Exception('Database insert failed: ' . $wpdb->last_error);
    }

    $submission_id = $wpdb->insert_id;

    // Also insert into entry_details table for better compatibility
    $details_table = $wpdb->prefix . 'fluentform_entry_details';

    foreach ($submission_data as $field_name => $field_value) {
        if (!empty($field_value)) {
            $wpdb->insert($details_table, [
                'form_id' => $form_id,
                'submission_id' => $submission_id,
                'field_name' => $field_name,
                'sub_field_name' => '',
                'field_value' => $field_value,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ]);
        }
    }

    // Send email notification if configured in Fluent Forms
    if (function_exists('wpFluentForm')) {
        do_action('fluentform_submission_inserted', $submission_id, $submission_data, $form_id);
    }

    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Lead saved successfully',
        'submission_id' => $submission_id,
        'data' => [
            'name' => $name,
            'phone' => $phone,
            'country' => $country,
            'percentage' => $percentage
        ]
    ]);

    // Log for debugging
    error_log("Visa Lead Saved: ID=$submission_id, Name=$name, Phone=$phone, Country=$country");

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => $e->getMessage()
    ]);

    // Log error
    error_log("Visa Lead Error: " . $e->getMessage());
}

exit;
