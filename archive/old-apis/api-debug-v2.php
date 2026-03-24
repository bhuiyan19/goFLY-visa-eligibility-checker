<?php
/**
 * Visa Checker API - Debug Version
 * Form ID: 12
 * Shows detailed database errors
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
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Load WordPress
$wp_load_path = __DIR__ . '/../../../../wp-load.php';

if (!file_exists($wp_load_path)) {
    send_response(false, 'WordPress not found', ['path' => $wp_load_path]);
}

try {
    require_once($wp_load_path);
} catch (Exception $e) {
    send_response(false, 'WordPress load failed: ' . $e->getMessage());
}

// GET Request - Health Check
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    global $wpdb;

    // Check tables exist
    $submissions_table = $wpdb->prefix . 'fluentform_submissions';
    $details_table = $wpdb->prefix . 'fluentform_entry_details';

    $tables_exist = [
        'submissions' => $wpdb->get_var("SHOW TABLES LIKE '$submissions_table'") ? 'Yes' : 'No',
        'details' => $wpdb->get_var("SHOW TABLES LIKE '$details_table'") ? 'Yes' : 'No'
    ];

    // Get table structure
    $table_columns = $wpdb->get_results("DESCRIBE $submissions_table", ARRAY_A);
    $column_names = array_column($table_columns, 'Field');

    send_response(true, 'API Working - Debug Info', [
        'wordpress_version' => get_bloginfo('version'),
        'fluent_forms_active' => function_exists('wpFluentForm') ? 'Yes' : 'No',
        'database_prefix' => $wpdb->prefix,
        'form_id' => 12,
        'tables_exist' => $tables_exist,
        'submissions_table_columns' => $column_names
    ]);
}

// Only POST for submissions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(false, 'Method not allowed');
}

// Get input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    send_response(false, 'Invalid JSON');
}

if (!isset($data['name']) || !isset($data['phone'])) {
    send_response(false, 'Missing name or phone');
}

// Sanitize
$name = sanitize_text_field($data['name']);
$phone = sanitize_text_field($data['phone']);
$email = isset($data['email']) ? sanitize_email($data['email']) : '';
$country = isset($data['country']) ? sanitize_text_field($data['country']) : '';
$countryId = isset($data['countryId']) ? sanitize_text_field($data['countryId']) : '';
$score = isset($data['score']) ? intval($data['score']) : 0;
$percentage = isset($data['percentage']) ? intval($data['percentage']) : 0;
$difficulty = isset($data['difficulty']) ? sanitize_text_field($data['difficulty']) : '';
$answers = isset($data['answers']) ? $data['answers'] : [];

// Check Fluent Forms
if (!function_exists('wpFluentForm')) {
    send_response(false, 'Fluent Forms not active');
}

$form_id = 12;

// Check form exists
global $wpdb;
$form_exists = $wpdb->get_var($wpdb->prepare(
    "SELECT id FROM {$wpdb->prefix}fluentform_forms WHERE id = %d",
    $form_id
));

if (!$form_exists) {
    send_response(false, 'Form ID 12 not found in database');
}

// Prepare minimal data first (test basic insert)
$submission_data = [
    'input_name' => $name,
    'input_phone' => $phone,
    'input_email' => $email,
    'input_country' => $country
];

// Add Q&A if exists
$question_count = 0;
if (!empty($answers) && is_array($answers)) {
    $question_number = 1;
    foreach ($answers as $question_id => $answer_data) {
        if (is_array($answer_data) && isset($answer_data['question']) && isset($answer_data['answer'])) {
            $submission_data["input_q{$question_number}_question"] = substr(sanitize_textarea_field($answer_data['question']), 0, 1000);
            $submission_data["input_q{$question_number}_answer"] = substr(sanitize_textarea_field($answer_data['answer']), 0, 500);
            $question_number++;
            $question_count++;
        }
    }
}

try {
    $submissions_table = $wpdb->prefix . 'fluentform_submissions';

    // Prepare insert data - check what columns exist
    $insert_data = [
        'form_id' => $form_id,
        'response' => json_encode($submission_data, JSON_UNESCAPED_UNICODE),
        'source_url' => isset($_SERVER['HTTP_REFERER']) ? substr($_SERVER['HTTP_REFERER'], 0, 255) : '',
        'user_id' => 0,
        'ip' => $_SERVER['REMOTE_ADDR'],
        'created_at' => current_time('mysql'),
        'updated_at' => current_time('mysql')
    ];

    // Try to add optional columns if they exist
    $table_columns = $wpdb->get_col("DESCRIBE $submissions_table");

    if (in_array('browser', $table_columns)) {
        // Use shorter, safer browser string to avoid validation errors
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        if (function_exists('mb_substr')) {
            $insert_data['browser'] = mb_substr($user_agent, 0, 200, 'UTF-8');
        } else {
            $insert_data['browser'] = substr($user_agent, 0, 200);
        }
    }

    if (in_array('device', $table_columns)) {
        $insert_data['device'] = 'unknown';
    }

    if (in_array('status', $table_columns)) {
        $insert_data['status'] = 'unread';
    }

    // Insert
    $inserted = $wpdb->insert($submissions_table, $insert_data);

    if ($inserted === false) {
        send_response(false, 'Database insert failed', [
            'error' => $wpdb->last_error,
            'table' => $submissions_table,
            'data_keys' => array_keys($insert_data),
            'table_columns' => $table_columns
        ]);
    }

    $submission_id = $wpdb->insert_id;

    // Insert details
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

    // Trigger notifications
    do_action('fluentform_submission_inserted', $submission_id, $submission_data, $form_id);

    // Success
    send_response(true, 'Lead saved successfully!', [
        'submission_id' => $submission_id,
        'form_id' => $form_id,
        'name' => $name,
        'phone' => $phone,
        'country' => $country,
        'percentage' => $percentage . '%',
        'questions_saved' => $question_count
    ]);

} catch (Exception $e) {
    send_response(false, 'Exception: ' . $e->getMessage(), [
        'line' => $e->getLine(),
        'file' => basename($e->getFile())
    ]);
}
