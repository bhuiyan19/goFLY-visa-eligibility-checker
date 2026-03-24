/**
 * Visa Checker API - Vercel Serverless Function
 * Handles form submissions and sends email notifications
 */

import { Resend } from 'resend';

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

      // Send email notification
      try {
        const resend = new Resend(process.env.RESEND_API_KEY);

        // Format answers for email
        let answersHtml = '';
        if (answers && Object.keys(answers).length > 0) {
          answersHtml = '<h3>প্রশ্ন ও উত্তর:</h3><ul>';
          let qNum = 1;
          for (const [qid, qdata] of Object.entries(answers)) {
            if (qdata.question && qdata.answer) {
              answersHtml += `<li><strong>Q${qNum}:</strong> ${qdata.question}<br><strong>উত্তর:</strong> ${qdata.answer}</li>`;
              qNum++;
            }
          }
          answersHtml += '</ul>';
        }

        await resend.emails.send({
          from: 'goFLY Visa Checker <onboarding@resend.dev>',
          to: ['goflybd@gmail.com'],
          subject: `🎯 New Lead: ${name} - ${country || 'Country not selected'}`,
          html: `
            <h2>নতুন লিড সাবমিশন</h2>
            <p><strong>Submission ID:</strong> ${submissionData.id}</p>
            <hr>
            <h3>ব্যক্তিগত তথ্য:</h3>
            <ul>
              <li><strong>নাম:</strong> ${name}</li>
              <li><strong>ফোন:</strong> ${phone}</li>
              <li><strong>ইমেইল:</strong> ${email || 'N/A'}</li>
            </ul>
            <h3>ভিসা তথ্য:</h3>
            <ul>
              <li><strong>দেশ:</strong> ${country || 'N/A'}</li>
              <li><strong>Country ID:</strong> ${countryId || 'N/A'}</li>
              <li><strong>Difficulty:</strong> ${difficulty || 'N/A'}</li>
              <li><strong>Score:</strong> ${score}</li>
              <li><strong>Percentage:</strong> ${percentage}%</li>
            </ul>
            ${answersHtml}
            <hr>
            <p><strong>IP:</strong> ${ip}</p>
            <p><strong>User Agent:</strong> ${userAgent.substring(0, 100)}</p>
            <p><strong>Submitted at:</strong> ${submissionData.metadata.submittedAt}</p>
          `
        });

        console.log('✅ Email sent successfully');
      } catch (emailError) {
        // Log error but don't fail the request
        console.error('⚠️ Email sending failed:', emailError.message);
      }

      // Log submission (backup)
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
