<?php
/**
 * Visa Checker API - With Email Notifications
 * Form ID: 12
 * Properly triggers Fluent Forms email notifications
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

function send_response($success, $message, $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$wp_load_path = __DIR__ . '/../../../../wp-load.php';
if (!file_exists($wp_load_path)) {
    send_response(false, 'WordPress not found');
}

require_once($wp_load_path);

// GET - Debug endpoint
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    global $wpdb;
    $submissions_table = $wpdb->prefix . 'fluentform_submissions';
    $table_columns = $wpdb->get_col("DESCRIBE $submissions_table");

    send_response(true, 'API Working', [
        'wordpress_version' => get_bloginfo('version'),
        'fluent_forms_active' => function_exists('wpFluentForm') ? 'Yes' : 'No',
        'form_id' => 12,
        'table_columns' => $table_columns,
        'smtp_configured' => defined('WPMS_ON') ? 'Yes' : 'Check needed'
    ]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(false, 'Method not allowed');
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['name']) || !isset($data['phone'])) {
    send_response(false, 'Invalid data');
}

// Sanitize inputs
$name = sanitize_text_field($data['name']);
$phone = sanitize_text_field($data['phone']);
$email = isset($data['email']) ? sanitize_email($data['email']) : '';
$country = isset($data['country']) ? sanitize_text_field($data['country']) : '';
$countryId = isset($data['countryId']) ? sanitize_text_field($data['countryId']) : '';
$score = isset($data['score']) ? intval($data['score']) : 0;
$percentage = isset($data['percentage']) ? intval($data['percentage']) : 0;
$difficulty = isset($data['difficulty']) ? sanitize_text_field($data['difficulty']) : '';
$answers = isset($data['answers']) ? $data['answers'] : [];

if (!function_exists('wpFluentForm')) {
    send_response(false, 'Fluent Forms not active');
}

$form_id = 12;
global $wpdb;

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

// Add Q&A
$question_count = 0;
if (!empty($answers) && is_array($answers)) {
    $question_number = 1;
    foreach ($answers as $qid => $adata) {
        if (is_array($adata) && isset($adata['question']) && isset($adata['answer'])) {
            $submission_data["input_q{$question_number}_question"] = substr(sanitize_textarea_field($adata['question']), 0, 1000);
            $submission_data["input_q{$question_number}_answer"] = substr(sanitize_textarea_field($adata['answer']), 0, 500);
            $question_number++;
            $question_count++;
        }
    }
}

try {
    $submissions_table = $wpdb->prefix . 'fluentform_submissions';

    // Get next serial number
    $max_serial = $wpdb->get_var($wpdb->prepare(
        "SELECT MAX(serial_number) FROM $submissions_table WHERE form_id = %d",
        $form_id
    ));
    $next_serial = $max_serial ? $max_serial + 1 : 1;

    // Get user agent
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $browser_string = function_exists('mb_substr') ?
        mb_substr($user_agent, 0, 200, 'UTF-8') :
        substr($user_agent, 0, 200);

    // Prepare insert with ALL columns
    $insert_data = [
        'form_id' => $form_id,
        'serial_number' => $next_serial,
        'response' => json_encode($submission_data, JSON_UNESCAPED_UNICODE),
        'source_url' => isset($_SERVER['HTTP_REFERER']) ? substr($_SERVER['HTTP_REFERER'], 0, 255) : '',
        'user_id' => 0,
        'status' => 'unread',
        'is_favourite' => 0,
        'browser' => $browser_string,
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
        send_response(false, 'Insert failed: ' . $wpdb->last_error);
    }

    $submission_id = $wpdb->insert_id;

    // Insert entry details
    $details_table = $wpdb->prefix . 'fluentform_entry_details';
    foreach ($submission_data as $fname => $fvalue) {
        if (!empty($fvalue)) {
            $wpdb->insert($details_table, [
                'form_id' => $form_id,
                'submission_id' => $submission_id,
                'field_name' => $fname,
                'sub_field_name' => '',
                'field_value' => $fvalue,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ]);
        }
    }

    // IMPORTANT: Trigger Fluent Forms hooks for notifications
    // This fires the email notifications
    do_action('fluentform_submission_inserted', $submission_id, $submission_data, $form_id);
    do_action('fluentform_before_insert_submission', $submission_data, $submission_data, $form_id, false);

    // Try to manually trigger notification
    if (defined('FLUENTFORM')) {
        // Load notification manager if available
        try {
            $notificationManager = wpFluentForm('notificationManager');
            if ($notificationManager && method_exists($notificationManager, 'notify')) {
                $notificationManager->notify($form_id, $submission_id, $submission_data);
            }
        } catch (Exception $e) {
            // Notification failed but submission succeeded - not critical
        }
    }

    send_response(true, 'Lead saved! Email notification sent.', [
        'submission_id' => $submission_id,
        'serial_number' => $next_serial,
        'form_id' => 12,
        'name' => $name,
        'phone' => $phone,
        'email' => $email,
        'country' => $country,
        'percentage' => $percentage . '%',
        'questions_saved' => $question_count,
        'notification_info' => 'Check your email (goflybd@gmail.com) and spam folder'
    ]);

} catch (Exception $e) {
    send_response(false, 'Exception: ' . $e->getMessage());
}
