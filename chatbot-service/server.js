const express = require('express');
const bodyParser = require('body-parser');
const dialogflow = require('@google-cloud/dialogflow').v2beta1;
const path = require('path');

const app = express();
app.use(bodyParser.json());

// Configuration
const projectId = 'click2servekiosk-seqh';
const knowledgeBaseId = process.env.DIALOGFLOW_KB_ID || 'OTA0OTA2NzI4MzEzNzE2NzM2MQ';
const credentialsPath = process.env.GOOGLE_APPLICATION_CREDENTIALS || path.join(__dirname, '../storage/app/google/dialogflow-service-account.json');

// Set credentials
process.env.GOOGLE_APPLICATION_CREDENTIALS = credentialsPath;

app.post('/chatbot/message', async (req, res) => {
    const { message, sessionId = Date.now().toString(), language = 'en' } = req.body;

    if (!message) {
        return res.status(400).json({
            reply: 'Message is required.',
        });
    }

    try {
        const sessionClient = new dialogflow.SessionsClient();
        const sessionPath = sessionClient.projectAgentSessionPath(projectId, sessionId);

        // Convert language codes
        const languageCode = language === 'fil' ? 'fil-PH' : 'en-US';

        const request = {
            session: sessionPath,
            queryInput: {
                text: {
                    text: message,
                    languageCode: languageCode,
                },
            },
            queryParams: {
                knowledgeBaseNames: [
                    `projects/${projectId}/knowledgeBases/${knowledgeBaseId}`,
                ],
            },
        };

        const [response] = await sessionClient.detectIntent(request);

        let fulfillment = response.queryResult.fulfillmentText || '';

        // Override with Knowledge Base answer if available
        if (response.queryResult.knowledgeAnswers?.answers?.length > 0) {
            fulfillment = response.queryResult.knowledgeAnswers.answers[0].answer;
        }

        // Fallback response
        if (!fulfillment) {
            fulfillment = 'I did not understand that. Could you rephrase?';
        }

        // Debug logging
        console.log('Dialogflow response:', {
            text: message,
            fulfillment: fulfillment,
            intent: response.queryResult.intent?.displayName || 'none',
            confidence: response.queryResult.intentDetectionConfidence || 0,
            knowledge_answers_count: response.queryResult.knowledgeAnswers?.answers?.length || 0,
        });

        return res.json({
            reply: fulfillment,
            sessionId: sessionId,
        });
    } catch (err) {
        console.error('Dialogflow error:', err);
        return res.status(500).json({
            reply: 'Sorry, something went wrong connecting to the assistant. Error: ' + err.message,
        });
    }
});

// Health check endpoint
app.get('/health', (req, res) => {
    res.json({ status: 'OK', service: 'chatbot-service' });
});

const PORT = process.env.PORT || 5000;
app.listen(PORT, () => {
    console.log(`Chatbot service running on port ${PORT}`);
    console.log(`Knowledge Base ID: ${knowledgeBaseId}`);
    console.log(`Credentials: ${credentialsPath}`);
});
