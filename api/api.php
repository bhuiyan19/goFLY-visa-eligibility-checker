<?php
/**
 * Visa Checker API - Full Working Version with Email Notifications
 * Form ID: 12
 * Handles First Name, Phone, Email, Country ID, Difficulty, Score, Percentage, Q&A
 * Browser field fixed + Multiple email notification hooks
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// JSON Response Helper
function send_response($success, $message, $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Load WordPress
$wp_load_path = __DIR__ . '/../../../../wp-load.php';
if (!file_exists($wp_load_path)) {
    send_response(false, 'WordPress not found at: ' . $wp_load_path);
}
require_once($wp_load_path);

// Debug: GET request returns API status
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    global $wpdb;
    send_response(true, 'API is ready!', [
        'wordpress_version' => get_bloginfo('version'),
        'fluent_forms_active' => function_exists('wpFluentForm') ? 'Yes' : 'No',
        'email_hooks' => 'Enhanced notification system enabled'
    ]);
}

// POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(false, 'Use POST method');
}

// Read input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['name']) || !isset($data['phone'])) {
    send_response(false, 'Missing required fields: name or phone', ['received' => $input]);
}

// Sanitize fields
$name = sanitize_text_field($data['name']);
$phone = sanitize_text_field($data['phone']);
$email = isset($data['email']) ? sanitize_email($data['email']) : '';
$countryId = isset($data['countryId']) ? sanitize_text_field($data['countryId']) : '';
$difficulty = isset($data['difficulty']) ? sanitize_text_field($data['difficulty']) : '';
$score = isset($data['score']) ? intval($data['score']) : 0;
$percentage = isset($data['percentage']) ? intval($data['percentage']) : 0;
$answers = isset($data['answers']) && is_array($data['answers']) ? $data['answers'] : [];

// FIXED: Get browser from server (not from POST data)
$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown';
$browser = function_exists('mb_substr') ?
    mb_substr($user_agent, 0, 200, 'UTF-8') :
    substr($user_agent, 0, 200);

if (!function_exists('wpFluentForm')) {
    send_response(false, 'Fluent Forms plugin is not active');
}

// Form ID
$form_id = 12;
global $wpdb;

// Check form exists
$form_exists = $wpdb->get_var($wpdb->prepare(
    "SELECT id FROM {$wpdb->prefix}fluentform_forms WHERE id = %d",
    $form_id
));
if (!$form_exists) {
    send_response(false, 'Fluent Form ID not found: ' . $form_id);
}

// Prepare submission data
$submission_data = [
    'input_name' => $name,
    'input_phone' => $phone,
    'input_email' => $email,
    'input_country_id' => $countryId,
    'input_difficulty' => $difficulty,
    'input_score' => $score,
    'input_percentage' => $percentage
];

// Add Q&A dynamically (max 10)
$question_number = 1;
foreach ($answers as $qa) {
    if (is_array($qa) && isset($qa['question']) && isset($qa['answer']) && $question_number <= 10) {
        $submission_data["input_q{$question_number}_question"] = substr(sanitize_text_field($qa['question']), 0, 1000);
        $submission_data["input_q{$question_number}_answer"] = substr(sanitize_text_field($qa['answer']), 0, 500);
        $question_number++;
    }
}

try {
    $submissions_table = $wpdb->prefix . 'fluentform_submissions';
    $details_table = $wpdb->prefix . 'fluentform_entry_details';

    // Next serial number
    $max_serial = $wpdb->get_var($wpdb->prepare(
        "SELECT MAX(serial_number) FROM $submissions_table WHERE form_id = %d",
        $form_id
    ));
    $next_serial = $max_serial ? $max_serial + 1 : 1;

    // Insert into submissions table
    $insert_data = [
        'form_id' => $form_id,
        'serial_number' => $next_serial,
        'response' => json_encode($submission_data, JSON_UNESCAPED_UNICODE),
        'source_url' => isset($_SERVER['HTTP_REFERER']) ? substr($_SERVER['HTTP_REFERER'], 0, 255) : '',
        'user_id' => 0,
        'status' => 'unread',
        'is_favourite' => 0,
        'browser' => $browser,
        'device' => 'unknown',
        'ip' => $_SERVER['REMOTE_ADDR'],
        'city' => NULL,
        'country' => NULL,
        'payment_status' => NULL,
        'payment_method' => NULL,
        'payment_type' => NULL,
        'currency' => NULL,
        'payment_total' => NULL,
        'total_paid' => NULL,
        'created_at' => current_time('mysql'),
        'updated_at' => current_time('mysql')
    ];

    $inserted = $wpdb->insert($submissions_table, $insert_data);
    if ($inserted === false) {
        send_response(false, 'Insert failed: ' . $wpdb->last_error, ['error' => $wpdb->last_error]);
    }

    $submission_id = $wpdb->insert_id;

    // Insert details
    foreach ($submission_data as $field => $value) {
        if (!empty($value)) {
            $wpdb->insert($details_table, [
                'form_id' => $form_id,
                'submission_id' => $submission_id,
                'field_name' => $field,
                'sub_field_name' => '',
                'field_value' => $value,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ]);
        }
    }

    // ========================================
    // ENHANCED: Multiple Email Notification Hooks
    // ========================================

    // Hook 1: New format (Fluent Forms 4.x+)
    do_action('fluentform/submission_inserted', $submission_id, $submission_data, $form_id);

    // Hook 2: Legacy format (Fluent Forms 3.x) - backwards compatible
    do_action('fluentform_submission_inserted', $submission_id, $submission_data, $form_id);

    // Hook 3: After submission actions (triggers integrations and notifications)
    do_action('fluentform/after_submission_actions', $submission_id, $submission_data, $form_id);

    // Hook 4: Global notification trigger (manual backup)
    try {
        if (defined('FLUENTFORM') || defined('FLUENTFORM_DIR_PATH')) {
            // Get form object
            $form = wpFluent()->table('fluentform_forms')->find($form_id);

            if ($form) {
                // Get submission object
                $submission = wpFluent()->table('fluentform_submissions')->find($submission_id);

                // Trigger global notification
                do_action('fluentform/global_notification_completed', $submission, $form);
            }
        }
    } catch (Exception $e) {
        // Continue even if manual trigger fails - other hooks should work
    }

    send_response(true, 'Lead saved successfully!', [
        'submission_id' => $submission_id,
        'serial_number' => $next_serial,
        'questions_saved' => $question_number - 1,
        'email_notification' => 'Hooks triggered - check goflybd@gmail.com'
    ]);

} catch (Exception $e) {
    send_response(false, 'Exception: ' . $e->getMessage(), [
        'line' => $e->getLine(),
        'file' => $e->getFile()
    ]);
}
