# Click2Serve Chatbot Service

A Node.js microservice that handles Dialogflow integration for the Click2Serve kiosk application.

## Setup

1. **Install Dependencies**
   ```bash
   cd chatbot-service
   npm install
   ```

2. **Environment Variables**
   Create a `.env` file in the chatbot-service directory:
   ```env
   DIALOGFLOW_KB_ID=your_knowledge_base_id
   GOOGLE_APPLICATION_CREDENTIALS=../storage/app/google/dialogflow-service-account.json
   PORT=5000
   ```

3. **Start the Service**
   ```bash
   npm start
   # or for development with auto-restart:
   npm run dev
   ```

## API Endpoints

- `POST /chatbot/message` - Process chatbot messages
- `GET /health` - Health check

## Request Format

```json
{
  "message": "How can I request a Business Permit?",
  "sessionId": "optional-session-id",
  "language": "en"
}
```

## Response Format

```json
{
  "reply": "To request a Business Permit, you need to...",
  "sessionId": "session-id"
}
```

## Integration with Laravel

The Laravel application calls this service via HTTP. Make sure the service URL is configured in your Laravel `.env`:

```env
CHATBOT_SERVICE_URL=http://localhost:5000
```
