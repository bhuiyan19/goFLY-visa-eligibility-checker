<?php
/**
 * goFLY Visa Checker - WordPress Backend Integration
 *
 * Installation Instructions:
 * 1. Add this code to your theme's functions.php
 * 2. OR create a custom plugin with this code
 * 3. Update FORM_ID with your Fluent Forms form ID
 * 4. Update email addresses for notifications
 * 5. Save and test
 */

// ============================================
// CONFIGURATION
// ============================================

define('GOFLY_FORM_ID', 123); // Your Fluent Forms form ID
define('GOFLY_ADMIN_EMAIL', 'info@goflybd.com');
define('GOFLY_SALES_PHONE', '8801713289170'); // For WhatsApp links

// ============================================
// REST API ENDPOINTS
// ============================================

/**
 * Register custom REST API routes
 */
add_action('rest_api_init', function () {
    // Main endpoint for lead submission
    register_rest_route('gofly/v1', '/submit-visa-lead', array(
        'methods' => 'POST',
        'callback' => 'gofly_submit_visa_lead',
        'permission_callback' => '__return_true',
    ));

    // Error logging endpoint
    register_rest_route('gofly/v1', '/log-error', array(
        'methods' => 'POST',
        'callback' => 'gofly_log_error',
        'permission_callback' => '__return_true',
    ));

    // Test endpoint
    register_rest_route('gofly/v1', '/test', array(
        'methods' => 'GET',
        'callback' => function() {
            return array('status' => 'ok', 'message' => 'goFLY API is working');
        },
        'permission_callback' => '__return_true',
    ));
});

/**
 * Enable CORS for API requests
 */
add_action('rest_api_init', function() {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    add_filter('rest_pre_serve_request', function($value) {
        // Allow requests from GitHub Pages
        $allowed_origins = array(
            'https://bhuiyan19.github.io',
            'https://goflybd.com',
            'http://localhost' // For local testing
        );

        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

        foreach ($allowed_origins as $allowed_origin) {
            if (strpos($origin, $allowed_origin) === 0) {
                header('Access-Control-Allow-Origin: ' . $origin);
                break;
            }
        }

        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        return $value;
    });
}, 15);

// ============================================
// MAIN SUBMISSION HANDLER
// ============================================

/**
 * Handle visa lead submission
 */
function gofly_submit_visa_lead($request) {
    // Get client IP for rate limiting
    $ip = gofly_get_client_ip();

    // Rate limiting check (10 submissions per hour per IP)
    if (!gofly_check_rate_limit($ip)) {
        return new WP_Error(
            'rate_limit_exceeded',
            'Too many submissions. Please try again later.',
            array('status' => 429)
        );
    }

    // Get request parameters
    $params = $request->get_json_params();

    // Validate required fields
    $validation_error = gofly_validate_lead_data($params);
    if (is_wp_error($validation_error)) {
        return $validation_error;
    }

    // Honeypot check (anti-bot)
    if (!empty($params['website'])) {
        // Bot detected - return fake success
        return array('success' => true, 'message' => 'Submission received');
    }

    // Sanitize data
    $lead_data = array(
        'name' => sanitize_text_field($params['name']),
        'phone' => sanitize_text_field($params['phone']),
        'email' => sanitize_email($params['email']),
        'country' => sanitize_text_field($params['country']),
        'country_id' => sanitize_text_field($params['countryId']),
        'country_english' => sanitize_text_field($params['countryEnglish']),
        'visa_type' => sanitize_text_field($params['visaType']),
        'difficulty' => sanitize_text_field($params['difficulty']),
        'score' => intval($params['score']),
        'max_score' => intval($params['maxScore']),
        'percentage' => intval($params['percentage']),
        'timestamp' => sanitize_text_field($params['timestamp']),
        'ip_address' => $ip,
        'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'])
    );

    // Submit to Fluent Forms
    $submission_id = gofly_save_to_fluent_forms($lead_data);

    if (!$submission_id) {
        return new WP_Error(
            'submission_failed',
            'Failed to save lead to database',
            array('status' => 500)
        );
    }

    // Send email notification to admin
    gofly_send_email_notification($lead_data, $submission_id);

    // Optional: Send SMS notification
    // gofly_send_sms_notification($lead_data);

    // Log submission for analytics
    gofly_log_submission($lead_data, $submission_id);

    // Return success response
    return array(
        'success' => true,
        'message' => 'Lead submitted successfully',
        'submission_id' => $submission_id,
        'debug' => WP_DEBUG ? $lead_data : null
    );
}

// ============================================
// FLUENT FORMS INTEGRATION
// ============================================

/**
 * Save lead to Fluent Forms
 */
function gofly_save_to_fluent_forms($lead_data) {
    global $wpdb;

    // Check if Fluent Forms is installed
    if (!function_exists('wpFluent')) {
        error_log('goFLY Error: Fluent Forms is not installed');
        return false;
    }

    $form_id = GOFLY_FORM_ID;

    // Prepare form data in Fluent Forms format
    $form_data = array(
        'names' => array('first_name' => $lead_data['name']),
        'input_phone' => $lead_data['phone'],
        'input_email' => $lead_data['email'],
        'input_country' => $lead_data['country'] . ' (' . $lead_data['country_english'] . ')',
        'input_visa_type' => $lead_data['visa_type'],
        'input_difficulty' => gofly_get_difficulty_label($lead_data['difficulty']),
        'input_score' => $lead_data['score'] . '/' . $lead_data['max_score'],
        'input_percentage' => $lead_data['percentage'] . '%',
        'input_timestamp' => $lead_data['timestamp']
    );

    try {
        // Insert into Fluent Forms submissions table
        $submission_id = wpFluent()->table('fluentform_submissions')
            ->insertGetId(array(
                'form_id' => $form_id,
                'serial_number' => gofly_get_next_serial_number($form_id),
                'response' => json_encode($form_data),
                'source_url' => 'https://bhuiyan19.github.io/goFLY-visa-eligibility-checker/',
                'user_id' => 0,
                'browser' => $lead_data['user_agent'],
                'device' => gofly_detect_device($lead_data['user_agent']),
                'ip' => $lead_data['ip_address'],
                'status' => 'unread',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ));

        // Also insert into submission details table
        foreach ($form_data as $field_name => $field_value) {
            if (is_array($field_value)) {
                $field_value = json_encode($field_value);
            }

            wpFluent()->table('fluentform_submission_data')
                ->insert(array(
                    'submission_id' => $submission_id,
                    'field_name' => $field_name,
                    'sub_field_name' => '',
                    'field_value' => $field_value
                ));
        }

        return $submission_id;

    } catch (Exception $e) {
        error_log('goFLY Fluent Forms Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get next serial number for form submissions
 */
function gofly_get_next_serial_number($form_id) {
    $last_submission = wpFluent()->table('fluentform_submissions')
        ->where('form_id', $form_id)
        ->orderBy('id', 'DESC')
        ->first();

    return $last_submission ? ($last_submission->serial_number + 1) : 1;
}

/**
 * Get difficulty label in Bengali
 */
function gofly_get_difficulty_label($difficulty) {
    $labels = array(
        'easy' => 'সহজ ভিসা',
        'medium' => 'স্ট্যান্ডার্ড',
        'hard' => 'প্রিমিয়াম',
        'very-hard' => 'এক্সক্লুসিভ'
    );

    return isset($labels[$difficulty]) ? $labels[$difficulty] : $difficulty;
}

// ============================================
// NOTIFICATIONS
// ============================================

/**
 * Send email notification to admin
 */
function gofly_send_email_notification($lead_data, $submission_id) {
    $to = GOFLY_ADMIN_EMAIL;
    $subject = '🎯 New Visa Lead: ' . $lead_data['country'] . ' (' . $lead_data['percentage'] . '%)';

    // Email body
    $message = "You have received a new visa eligibility lead!\n\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "📋 LEAD INFORMATION\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $message .= "Name:        " . $lead_data['name'] . "\n";
    $message .= "Phone:       " . $lead_data['phone'] . "\n";
    $message .= "Email:       " . $lead_data['email'] . "\n";
    $message .= "Country:     " . $lead_data['country'] . " (" . $lead_data['country_english'] . ")\n";
    $message .= "Visa Type:   " . $lead_data['visa_type'] . "\n";
    $message .= "Difficulty:  " . gofly_get_difficulty_label($lead_data['difficulty']) . "\n\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "📊 ELIGIBILITY SCORE\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $message .= "Score:       " . $lead_data['score'] . "/" . $lead_data['max_score'] . " (" . $lead_data['percentage'] . "%)\n";
    $message .= "Priority:    " . gofly_get_priority_label($lead_data['percentage']) . "\n\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "🔗 QUICK ACTIONS\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $message .= "WhatsApp:    https://wa.me/" . GOFLY_SALES_PHONE . "?text=Hello%20" . urlencode($lead_data['name']) . "\n";
    $message .= "Call:        tel:+" . GOFLY_SALES_PHONE . "\n";
    $message .= "View Entry:  " . admin_url('admin.php?page=fluent_forms&route=entries&form_id=' . GOFLY_FORM_ID . '#/entries/' . $submission_id) . "\n\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "Submitted:   " . $lead_data['timestamp'] . "\n";
    $message .= "IP Address:  " . $lead_data['ip_address'] . "\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    // Email headers
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: goFLY Visa Checker <noreply@goflybd.com>'
    );

    // Send email
    $sent = wp_mail($to, $subject, $message, $headers);

    if (!$sent) {
        error_log('goFLY Error: Failed to send email notification');
    }

    return $sent;
}

/**
 * Get priority label based on percentage
 */
function gofly_get_priority_label($percentage) {
    if ($percentage >= 80) return '🟢 HIGH (Strong Profile)';
    if ($percentage >= 65) return '🟡 MEDIUM (Good Profile)';
    if ($percentage >= 50) return '🟠 MEDIUM (Needs Preparation)';
    return '🔴 LOW (Challenging Case)';
}

// ============================================
// VALIDATION & SECURITY
// ============================================

/**
 * Validate lead data
 */
function gofly_validate_lead_data($params) {
    // Check required fields
    if (empty($params['name'])) {
        return new WP_Error('missing_name', 'Name is required', array('status' => 400));
    }

    if (empty($params['phone'])) {
        return new WP_Error('missing_phone', 'Phone is required', array('status' => 400));
    }

    // Validate phone format (11 digits)
    if (!preg_match('/^[0-9]{11}$/', $params['phone'])) {
        return new WP_Error('invalid_phone', 'Phone must be 11 digits', array('status' => 400));
    }

    // Validate email if provided
    if (!empty($params['email']) && !is_email($params['email'])) {
        return new WP_Error('invalid_email', 'Invalid email format', array('status' => 400));
    }

    // Validate score range
    if (isset($params['score']) && ($params['score'] < 0 || $params['score'] > 100)) {
        return new WP_Error('invalid_score', 'Score must be between 0 and 100', array('status' => 400));
    }

    return true;
}

/**
 * Rate limiting check
 */
function gofly_check_rate_limit($ip) {
    $transient_key = 'gofly_rate_limit_' . md5($ip);
    $requests = get_transient($transient_key);

    // Allow 10 requests per hour
    if ($requests && $requests >= 10) {
        return false;
    }

    set_transient($transient_key, ($requests ? $requests + 1 : 1), HOUR_IN_SECONDS);
    return true;
}

/**
 * Get client IP address
 */
function gofly_get_client_ip() {
    $ip = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return sanitize_text_field($ip);
}

/**
 * Detect device type from user agent
 */
function gofly_detect_device($user_agent) {
    if (preg_match('/mobile|android|iphone|ipad|phone/i', $user_agent)) {
        return 'mobile';
    }
    if (preg_match('/tablet|ipad/i', $user_agent)) {
        return 'tablet';
    }
    return 'desktop';
}

// ============================================
// LOGGING & ANALYTICS
// ============================================

/**
 * Log submission for analytics
 */
function gofly_log_submission($lead_data, $submission_id) {
    // Log to custom table or analytics service
    $log_entry = array(
        'submission_id' => $submission_id,
        'country' => $lead_data['country_id'],
        'difficulty' => $lead_data['difficulty'],
        'percentage' => $lead_data['percentage'],
        'timestamp' => current_time('mysql'),
        'ip' => $lead_data['ip_address'],
        'device' => gofly_detect_device($lead_data['user_agent'])
    );

    // Save to custom analytics (optional)
    // You could create a custom table for analytics
    do_action('gofly_lead_submitted', $log_entry);
}

/**
 * Log errors from frontend
 */
function gofly_log_error($request) {
    $params = $request->get_json_params();

    $error_log = array(
        'error' => sanitize_text_field($params['error']),
        'timestamp' => sanitize_text_field($params['timestamp']),
        'user_agent' => sanitize_text_field($params['userAgent']),
        'lead_info' => $params['leadInfo'] // Already an array
    );

    error_log('goFLY Frontend Error: ' . json_encode($error_log));

    return array('success' => true, 'message' => 'Error logged');
}

// ============================================
// ADMIN DASHBOARD WIDGET (Optional)
// ============================================

/**
 * Add dashboard widget for recent leads
 */
add_action('wp_dashboard_setup', 'gofly_add_dashboard_widget');

function gofly_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'gofly_recent_leads',
        '🎯 Recent Visa Leads',
        'gofly_dashboard_widget_display'
    );
}

function gofly_dashboard_widget_display() {
    // Get recent 5 submissions
    $submissions = wpFluent()->table('fluentform_submissions')
        ->where('form_id', GOFLY_FORM_ID)
        ->orderBy('id', 'DESC')
        ->limit(5)
        ->get();

    if (empty($submissions)) {
        echo '<p>No leads yet.</p>';
        return;
    }

    echo '<ul>';
    foreach ($submissions as $submission) {
        $data = json_decode($submission->response, true);
        $name = isset($data['names']['first_name']) ? $data['names']['first_name'] : 'Unknown';
        $country = isset($data['input_country']) ? $data['input_country'] : 'Unknown';
        $percentage = isset($data['input_percentage']) ? $data['input_percentage'] : '0%';

        echo '<li>';
        echo '<strong>' . esc_html($name) . '</strong> - ';
        echo esc_html($country) . ' (' . esc_html($percentage) . ')';
        echo '<br><small>' . human_time_diff(strtotime($submission->created_at)) . ' ago</small>';
        echo '</li>';
    }
    echo '</ul>';

    echo '<p><a href="' . admin_url('admin.php?page=fluent_forms&route=entries&form_id=' . GOFLY_FORM_ID) . '">View All Leads →</a></p>';
}

// ============================================
// CLEANUP & MAINTENANCE
// ============================================

/**
 * Clean up old rate limit transients (runs daily)
 */
add_action('wp_scheduled_delete', 'gofly_cleanup_rate_limits');

function gofly_cleanup_rate_limits() {
    global $wpdb;

    $wpdb->query(
        "DELETE FROM {$wpdb->options}
         WHERE option_name LIKE '_transient_gofly_rate_limit_%'
         OR option_name LIKE '_transient_timeout_gofly_rate_limit_%'"
    );
}

?>
