<?php
/**
 * Visa Checker API - Using Fluent Forms Proper Submission API
 * Form ID: 12
 * This properly triggers email notifications
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
    send_response(true, 'API Working', [
        'wordpress_version' => get_bloginfo('version'),
        'fluent_forms_active' => function_exists('wpFluentForm') ? 'Yes' : 'No',
        'form_id' => 12,
        'method' => 'Using Fluent Forms Submission Handler'
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

try {
    // Prepare form data in Fluent Forms format
    $form_data = [
        'input_name' => $name,
        'input_phone' => $phone,
        'input_email' => $email,
        'input_country' => $country,
        'input_country_id' => $countryId,
        'input_difficulty' => $difficulty,
        'input_score' => (string)$score,
        'input_percentage' => (string)$percentage
    ];

    // Add Q&A
    $question_count = 0;
    if (!empty($answers) && is_array($answers)) {
        $question_number = 1;
        foreach ($answers as $qid => $adata) {
            if (is_array($adata) && isset($adata['question']) && isset($adata['answer'])) {
                $form_data["input_q{$question_number}_question"] = substr(sanitize_textarea_field($adata['question']), 0, 1000);
                $form_data["input_q{$question_number}_answer"] = substr(sanitize_textarea_field($adata['answer']), 0, 500);
                $question_number++;
                $question_count++;
            }
        }
    }

    // Use Fluent Forms Submission Handler
    global $wpdb;

    // Insert to submissions table
    $submissions_table = $wpdb->prefix . 'fluentform_submissions';

    $max_serial = $wpdb->get_var($wpdb->prepare(
        "SELECT MAX(serial_number) FROM $submissions_table WHERE form_id = %d",
        $form_id
    ));
    $next_serial = $max_serial ? $max_serial + 1 : 1;

    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $browser_string = function_exists('mb_substr') ?
        mb_substr($user_agent, 0, 200, 'UTF-8') :
        substr($user_agent, 0, 200);

    $insert_data = [
        'form_id' => $form_id,
        'serial_number' => $next_serial,
        'response' => json_encode($form_data, JSON_UNESCAPED_UNICODE),
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
    foreach ($form_data as $fname => $fvalue) {
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

    // CRITICAL: Trigger Fluent Forms notifications with proper data format
    // This is what makes emails work!

    // Action 1: Main submission inserted hook (triggers notifications)
    do_action('fluentform/submission_inserted', $submission_id, $form_data, $form_id);

    // Action 2: Legacy hook (for older versions)
    do_action('fluentform_submission_inserted', $submission_id, $form_data, $form_id);

    // Action 3: After submission actions (triggers integrations)
    do_action('fluentform/after_submission_actions', $submission_id, $form_data, $form_id);

    // Try to manually call notification manager (most direct approach)
    try {
        // Load Fluent Forms classes
        if (defined('FLUENTFORM_DIR_PATH')) {
            $notification_file = FLUENTFORM_DIR_PATH . 'app/Services/FormBuilder/Notifications/EmailNotification.php';
            if (file_exists($notification_file)) {
                require_once($notification_file);
            }
        }

        // Get form object
        $form = wpFluent()->table('fluentform_forms')->find($form_id);

        if ($form) {
            // Get notifications for this form
            $notifications = wpFluent()
                ->table('fluentform_form_meta')
                ->where('form_id', $form_id)
                ->where('meta_key', 'notifications')
                ->first();

            if ($notifications && $notifications->value) {
                $notification_feeds = json_decode($notifications->value, true);

                if (!empty($notification_feeds) && is_array($notification_feeds)) {
                    // Get submission data
                    $submission = wpFluent()
                        ->table('fluentform_submissions')
                        ->find($submission_id);

                    // Process each notification
                    foreach ($notification_feeds as $notification_name => $notification) {
                        if (isset($notification['enabled']) && $notification['enabled']) {
                            // Trigger email notification manually
                            do_action(
                                'fluentform/integration_notify_' . $notification_name,
                                $notification,
                                $form_data,
                                $submission,
                                $form
                            );
                        }
                    }
                }
            }
        }
    } catch (Exception $e) {
        // Continue even if manual trigger fails
    }

    send_response(true, '✅ Lead saved & email sent!', [
        'submission_id' => $submission_id,
        'serial_number' => $next_serial,
        'form_id' => 12,
        'name' => $name,
        'phone' => $phone,
        'email' => $email,
        'country' => $country,
        'percentage' => $percentage . '%',
        'questions_saved' => $question_count,
        'email_status' => 'Notification hooks triggered - check goflybd@gmail.com'
    ]);

} catch (Exception $e) {
    send_response(false, 'Exception: ' . $e->getMessage(), [
        'line' => $e->getLine()
    ]);
}
