/**
 * Fluent Forms Integration for goFLY Visa Eligibility Checker
 *
 * Instructions:
 * 1. Update WORDPRESS_URL with your site URL
 * 2. Update FORM_ID with your Fluent Forms form ID
 * 3. Choose integration method (API or Custom Endpoint)
 * 4. Replace the submitLead function in index.html with this code
 */

// ============================================
// CONFIGURATION - UPDATE THESE VALUES
// ============================================

const FLUENT_FORMS_CONFIG = {
    // Your WordPress site URL (without trailing slash)
    WORDPRESS_URL: 'https://goflybd.com',

    // Your Fluent Forms form ID (find in Fluent Forms → All Forms)
    FORM_ID: '123',

    // Integration method: 'api' or 'custom_endpoint'
    METHOD: 'custom_endpoint', // Change to 'api' if using Fluent Forms Pro API

    // API Key (only needed if METHOD = 'api')
    API_KEY: 'your_api_key_here',

    // Enable debug mode (shows console logs)
    DEBUG: true,

    // Field mapping - Update these to match your Fluent Forms field names
    FIELD_MAPPING: {
        name: 'names[first_name]',      // or 'input_text_1'
        phone: 'input_phone',            // or 'numeric_1'
        email: 'input_email',            // or 'email_1'
        country: 'input_country',        // or 'text_2'
        score: 'input_score',            // or 'numeric_2'
        percentage: 'input_percentage'   // or 'numeric_3'
    }
};

// ============================================
// MAIN INTEGRATION FUNCTIONS
// ============================================

/**
 * Submit lead - Replace this function in index.html
 */
function submitLead(event) {
    event.preventDefault();

    // Validate required fields
    if (!leadData.name || !leadData.phone) {
        alert('দয়া করে নাম এবং মোবাইল নম্বর দিন');
        return;
    }

    // Validate phone format
    if (!/^[0-9]{11}$/.test(leadData.phone)) {
        alert('দয়া করে সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন');
        return;
    }

    showLoading();

    // Prepare lead data
    const questionsData = QUESTIONS[selectedCountry.difficulty];
    const leadInfo = {
        name: leadData.name,
        phone: leadData.phone,
        email: leadData.email || '',
        country: selectedCountry.nameBn,
        countryId: selectedCountry.id,
        countryEnglish: selectedCountry.name,
        visaType: selectedCountry.visaType,
        difficulty: selectedCountry.difficulty,
        score: score,
        maxScore: questionsData.maxScore,
        percentage: Math.round((score / questionsData.maxScore) * 100),
        timestamp: new Date().toISOString(),
        answers: answers // Include all answers for detailed analysis
    };

    // Save to localStorage as backup
    saveToLocalStorage(leadInfo);

    // Submit to Fluent Forms based on method
    if (FLUENT_FORMS_CONFIG.METHOD === 'api') {
        submitViaFluentFormsAPI(leadInfo);
    } else {
        submitViaCustomEndpoint(leadInfo);
    }
}

/**
 * Save to localStorage (backup)
 */
function saveToLocalStorage(leadInfo) {
    try {
        const leads = JSON.parse(localStorage.getItem('visaLeads') || '[]');
        leads.push(leadInfo);
        localStorage.setItem('visaLeads', JSON.stringify(leads));

        if (FLUENT_FORMS_CONFIG.DEBUG) {
            console.log('✅ Saved to localStorage:', leadInfo);
        }
    } catch (error) {
        console.error('❌ localStorage error:', error);
    }
}

/**
 * Method 1: Submit via Fluent Forms REST API (Requires Fluent Forms Pro)
 */
async function submitViaFluentFormsAPI(leadInfo) {
    const { WORDPRESS_URL, FORM_ID, API_KEY, FIELD_MAPPING } = FLUENT_FORMS_CONFIG;

    try {
        // Map data to Fluent Forms field names
        const formData = {};
        formData[FIELD_MAPPING.name] = leadInfo.name;
        formData[FIELD_MAPPING.phone] = leadInfo.phone;
        formData[FIELD_MAPPING.email] = leadInfo.email;
        formData[FIELD_MAPPING.country] = `${leadInfo.country} (${leadInfo.countryEnglish})`;
        formData[FIELD_MAPPING.score] = `${leadInfo.score}/${leadInfo.maxScore}`;
        formData[FIELD_MAPPING.percentage] = `${leadInfo.percentage}%`;

        if (FLUENT_FORMS_CONFIG.DEBUG) {
            console.log('📤 Submitting to Fluent Forms API:', formData);
        }

        const response = await fetch(`${WORDPRESS_URL}/wp-json/fluentform/v1/forms/${FORM_ID}/submissions`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${API_KEY}`
            },
            body: JSON.stringify({
                data: formData
            })
        });

        const result = await response.json();

        if (response.ok && (result.message === 'success' || result.status === 'success')) {
            handleSubmissionSuccess(leadInfo);
        } else {
            throw new Error(result.message || result.errors || 'Submission failed');
        }

    } catch (error) {
        handleSubmissionError(error, leadInfo);
    }
}

/**
 * Method 2: Submit via Custom WordPress Endpoint (Recommended)
 */
async function submitViaCustomEndpoint(leadInfo) {
    const { WORDPRESS_URL } = FLUENT_FORMS_CONFIG;

    try {
        if (FLUENT_FORMS_CONFIG.DEBUG) {
            console.log('📤 Submitting to custom endpoint:', leadInfo);
        }

        const response = await fetch(`${WORDPRESS_URL}/wp-json/gofly/v1/submit-visa-lead`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(leadInfo)
        });

        const result = await response.json();

        if (response.ok && result.success) {
            handleSubmissionSuccess(leadInfo);
        } else {
            throw new Error(result.message || 'Submission failed');
        }

    } catch (error) {
        handleSubmissionError(error, leadInfo);
    }
}

/**
 * Handle successful submission
 */
function handleSubmissionSuccess(leadInfo) {
    if (FLUENT_FORMS_CONFIG.DEBUG) {
        console.log('✅ Lead submitted successfully:', leadInfo);
    }

    // Move to result page
    currentStep = 'result';
    saveSession();
    render();
    hideLoading();

    // Optional: Track conversion with Google Analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', 'lead_submitted', {
            'event_category': 'Visa Checker',
            'event_label': leadInfo.country,
            'value': leadInfo.percentage
        });
    }
}

/**
 * Handle submission error
 */
function handleSubmissionError(error, leadInfo) {
    console.error('❌ Submission error:', error);

    if (FLUENT_FORMS_CONFIG.DEBUG) {
        alert(`Error: ${error.message}\n\nData saved locally. Please call 01713-289170`);
    } else {
        alert('তথ্য সংরক্ষণে সমস্যা হয়েছে, তবে আপনার ফলাফল দেখতে পারবেন।\nদয়া করে সরাসরি কল করুন: 01713-289170');
    }

    // Still show results even if submission fails
    currentStep = 'result';
    saveSession();
    render();
    hideLoading();

    // Log error for debugging
    logErrorToBackend(error, leadInfo);
}

/**
 * Log errors to backend for debugging
 */
async function logErrorToBackend(error, leadInfo) {
    try {
        await fetch(`${FLUENT_FORMS_CONFIG.WORDPRESS_URL}/wp-json/gofly/v1/log-error`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                error: error.message,
                stack: error.stack,
                leadInfo: leadInfo,
                timestamp: new Date().toISOString(),
                userAgent: navigator.userAgent
            })
        });
    } catch (logError) {
        console.error('Failed to log error:', logError);
    }
}

/**
 * Test connection to backend
 */
async function testFluentFormsConnection() {
    const { WORDPRESS_URL } = FLUENT_FORMS_CONFIG;

    try {
        const response = await fetch(`${WORDPRESS_URL}/wp-json/`, {
            method: 'GET'
        });

        if (response.ok) {
            console.log('✅ WordPress REST API is accessible');
            return true;
        } else {
            console.error('❌ WordPress REST API returned error:', response.status);
            return false;
        }
    } catch (error) {
        console.error('❌ Cannot connect to WordPress:', error);
        return false;
    }
}

// Test connection on page load (optional)
if (FLUENT_FORMS_CONFIG.DEBUG) {
    window.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            testFluentFormsConnection();
        }, 2000);
    });
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

/**
 * Get field name from Fluent Forms
 * Helper function to inspect your form's field names
 */
function getFluentFormsFieldNames() {
    console.log(`
    📋 How to get Fluent Forms field names:

    1. Go to Fluent Forms → Edit your form
    2. Click on each field
    3. Look for "Field Name" or "Input Name" in settings
    4. Update FIELD_MAPPING in config above

    Common field name patterns:
    - Text fields: input_text_1, input_text_2, etc.
    - Name fields: names[first_name], names[last_name]
    - Phone fields: input_phone, phone_1
    - Email fields: input_email, email_1
    - Number fields: numeric_1, numeric_2
    `);
}

/**
 * Export all leads from localStorage
 */
function exportLeadsToCSV() {
    const leads = JSON.parse(localStorage.getItem('visaLeads') || '[]');

    if (leads.length === 0) {
        alert('No leads found in localStorage');
        return;
    }

    // Create CSV content
    const headers = ['Name', 'Phone', 'Email', 'Country', 'Score', 'Percentage', 'Timestamp'];
    const rows = leads.map(lead => [
        lead.name,
        lead.phone,
        lead.email,
        lead.country,
        `${lead.score}/${lead.maxScore}`,
        `${lead.percentage}%`,
        lead.timestamp
    ]);

    const csv = [headers, ...rows]
        .map(row => row.join(','))
        .join('\n');

    // Download CSV
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `visa-leads-${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    console.log(`✅ Exported ${leads.length} leads to CSV`);
}

/**
 * Clear all leads from localStorage
 */
function clearAllLeads() {
    if (confirm('Are you sure you want to clear all leads from localStorage?')) {
        localStorage.removeItem('visaLeads');
        console.log('✅ All leads cleared from localStorage');
    }
}

// Expose utility functions to console
window.fluentFormsUtils = {
    testConnection: testFluentFormsConnection,
    getFieldNames: getFluentFormsFieldNames,
    exportLeads: exportLeadsToCSV,
    clearLeads: clearAllLeads
};

console.log('🔧 Fluent Forms integration loaded. Type fluentFormsUtils to see available commands.');
