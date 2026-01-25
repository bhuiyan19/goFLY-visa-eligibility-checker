<?php
/**
 * Visa Checker API - With Question Answers
 * Saves both lead data and all question answers
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Function to send JSON response
function send_response($success, $message, $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Check if WordPress is accessible
$wp_load_path = __DIR__ . '/../../../../wp-load.php';

if (!file_exists($wp_load_path)) {
    send_response(false, 'WordPress not found at: ' . $wp_load_path, [
        'current_dir' => __DIR__,
        'checking_path' => $wp_load_path
    ]);
}

// Load WordPress
try {
    require_once($wp_load_path);
} catch (Exception $e) {
    send_response(false, 'Failed to load WordPress: ' . $e->getMessage());
}

// GET Request Support - For Testing
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    send_response(true, 'API is working! WordPress loaded successfully.', [
        'wordpress_version' => get_bloginfo('version'),
        'fluent_forms_active' => function_exists('wpFluentForm') ? 'Yes' : 'No',
        'database_prefix' => $GLOBALS['wpdb']->prefix
    ]);
}

// Only accept POST for submissions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(false, 'Method not allowed. Use POST.');
}

// Get and validate input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    send_response(false, 'Invalid JSON data', ['received' => $input]);
}

if (!isset($data['name']) || !isset($data['phone'])) {
    send_response(false, 'Missing required fields', ['received_fields' => array_keys($data)]);
}

// Sanitize basic data
$name = sanitize_text_field($data['name']);
$phone = sanitize_text_field($data['phone']);
$email = isset($data['email']) ? sanitize_email($data['email']) : '';
$country = isset($data['country']) ? sanitize_text_field($data['country']) : '';
$countryId = isset($data['countryId']) ? sanitize_text_field($data['countryId']) : '';
$score = isset($data['score']) ? intval($data['score']) : 0;
$percentage = isset($data['percentage']) ? intval($data['percentage']) : 0;
$difficulty = isset($data['difficulty']) ? sanitize_text_field($data['difficulty']) : '';
$answers = isset($data['answers']) ? $data['answers'] : [];

// Check if Fluent Forms is active
if (!function_exists('wpFluentForm')) {
    send_response(false, 'Fluent Forms plugin is not active', [
        'suggestion' => 'Please activate Fluent Forms plugin in WordPress'
    ]);
}

// 🔧 CHANGE THIS to your actual Form ID!
$form_id = 6959; // ← Update with your Form ID

// Check if form exists
global $wpdb;
$form_exists = $wpdb->get_var($wpdb->prepare(
    "SELECT id FROM {$wpdb->prefix}fluentform_forms WHERE id = %d",
    $form_id
));

if (!$form_exists) {
    send_response(false, 'Form not found', [
        'form_id' => $form_id,
        'suggestion' => 'Please update $form_id in api.php with your actual Fluent Forms form ID'
    ]);
}

// Prepare submission data
$submission_data = [
    'input_name' => $name,
    'input_phone' => $phone,
    'input_email' => $email,
    'input_country' => $country,
    'input_country_id' => $countryId,
    'input_difficulty' => $difficulty,
    'input_score' => $score,
    'input_percentage' => $percentage
];

// Add each question and answer as separate fields
$question_number = 1;
foreach ($answers as $question_id => $answer_data) {
    if (is_array($answer_data)) {
        $submission_data["input_q{$question_number}_question"] = $answer_data['question'];
        $submission_data["input_q{$question_number}_answer"] = $answer_data['answer'];
        $question_number++;
    }
}

try {
    // Insert into submissions table
    $submissions_table = $wpdb->prefix . 'fluentform_submissions';

    $insert_data = [
        'form_id' => $form_id,
        'response' => json_encode($submission_data),
        'source_url' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
        'user_id' => 0,
        'browser' => isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 254) : '',
        'device' => 'unknown',
        'ip' => $_SERVER['REMOTE_ADDR'],
        'created_at' => current_time('mysql'),
        'updated_at' => current_time('mysql'),
        'status' => 'unread'
    ];

    $inserted = $wpdb->insert($submissions_table, $insert_data);

    if ($inserted === false) {
        send_response(false, 'Database insert failed', [
            'error' => $wpdb->last_error,
            'table' => $submissions_table
        ]);
    }

    $submission_id = $wpdb->insert_id;

    // Insert into entry details table
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

    // Trigger Fluent Forms notifications
    do_action('fluentform_submission_inserted', $submission_id, $submission_data, $form_id);

    // Success!
    send_response(true, 'Lead saved successfully with all answers!', [
        'submission_id' => $submission_id,
        'name' => $name,
        'phone' => $phone,
        'country' => $country,
        'percentage' => $percentage . '%',
        'questions_saved' => $question_number - 1
    ]);

} catch (Exception $e) {
    send_response(false, 'Exception occurred', [
        'error' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => $e->getFile()
    ]);
}
