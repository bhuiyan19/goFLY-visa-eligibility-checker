/**
 * Visa Checker API - Vercel Serverless Function
 * Handles form submissions and stores data
 */

export default async function handler(req, res) {
  // CORS headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  // Handle OPTIONS request
  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  // Handle GET request - API status
  if (req.method === 'GET') {
    return res.status(200).json({
      success: true,
      message: 'Visa Checker API is ready!',
      data: {
        version: '1.0',
        status: 'active',
        timestamp: new Date().toISOString()
      }
    });
  }

  // Handle POST request - form submission
  if (req.method === 'POST') {
    try {
      const {
        name,
        phone,
        email,
        country,
        countryId,
        score,
        percentage,
        difficulty,
        answers,
        timestamp
      } = req.body;

      // Validate required fields
      if (!name || !phone) {
        return res.status(400).json({
          success: false,
          message: 'Missing required fields: name or phone',
          data: { received: req.body }
        });
      }

      // Log submission data (in production, save to a database)
      console.log('📝 New visa checker submission:', {
        name,
        phone,
        email,
        country,
        countryId,
        score,
        percentage,
        difficulty,
        answersCount: Object.keys(answers || {}).length,
        timestamp
      });

      // Get user info
      const userAgent = req.headers['user-agent'] || 'unknown';
      const ip = req.headers['x-forwarded-for'] || req.headers['x-real-ip'] || 'unknown';

      // Prepare submission data
      const submissionData = {
        id: `SUB-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`,
        name,
        phone,
        email: email || '',
        country: country || '',
        countryId: countryId || '',
        score: score || 0,
        percentage: percentage || 0,
        difficulty: difficulty || '',
        answers: answers || {},
        timestamp: timestamp || new Date().toISOString(),
        metadata: {
          userAgent: userAgent.substring(0, 200),
          ip,
          submittedAt: new Date().toISOString()
        }
      };

      // In a production environment, you would:
      // 1. Save to a database (Vercel Postgres, MongoDB, etc.)
      // 2. Send email notifications
      // 3. Integrate with CRM/marketing tools

      // For now, we'll just log and return success
      console.log('✅ Submission processed:', submissionData);

      // Return success response
      return res.status(200).json({
        success: true,
        message: 'Lead saved successfully!',
        data: {
          submissionId: submissionData.id,
          name: name,
          country: country,
          score: score,
          percentage: percentage
        }
      });

    } catch (error) {
      console.error('❌ Error processing submission:', error);
      return res.status(500).json({
        success: false,
        message: 'Server error processing submission',
        data: {
          error: error.message
        }
      });
    }
  }

  // Method not allowed
  return res.status(405).json({
    success: false,
    message: 'Method not allowed'
  });
}
