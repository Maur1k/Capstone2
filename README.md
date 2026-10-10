# Click2Serve - Smart Municipal Kiosk System

A comprehensive digital kiosk system for municipal services including civil registry, business permits, and other government services.

## 🚀 Quick Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js 16+ and npm
- MySQL/MariaDB
- XAMPP (recommended for Windows)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/YOUR_USERNAME/Click2Serve.git
   cd Click2Serve
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   cd print-service && npm install
   cd ../chatbot-service && npm install
   cd ..
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   - Create a MySQL database named `click2serve`
   - Update `.env` file with your database credentials:
     ```
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=click2serve
     DB_USERNAME=root
     DB_PASSWORD=
     ```

6. **Run migrations**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Start services**
   ```bash
   # Terminal 1 - Laravel server
   php artisan serve
   
   # Terminal 2 - Print service
   cd print-service
   npm start
   
   # Terminal 3 - Chatbot service
   cd chatbot-service
   npm start
   ```

8. **Access the application**
   - Main Kiosk: http://localhost:8000
   - Admin Panel: http://localhost:8000/admin
   - Officer Login: http://localhost:8000/officer

## 📋 Features

- **Civil Registry Services**
  - Certificate of Live Birth (On-time & Delayed)
  - Certificate of Marriage
  - Tabbed form interface for better UX

- **Other Services**
  - Business Permits (BPLO)
  - CTRU Services
  - Engineering Office
  - Social Welfare
  - Veterinary Services

- **Admin Features**
  - Request management
  - Officer management
  - Activity logging
  - Print service integration

## 🔧 Configuration

### Print Service
Configure printer settings in `print-service/server.js`. Use `.env` `PRINT_FONT_FAMILY` to set the thermal receipt font (defaults to Roboto).

### Feature Toggles
Super admins can enable/disable experimental experiences from `Admin → Feature Toggles`. Each switch maps to a key inside the `settings` table (`feature.*`) and is respected across the kiosk tracker, receipt printing, manual uploads, and upcoming public dashboards.

### Public Dashboard
- URL: `/public-dashboard`
- Displays live request counts, PSA progress milestones, and the latest supporting-document uploads.
- Controlled via the `feature.public_dashboard` toggle. Disable it if the city prefers to keep operations private.

### Chatbot Service
Configure AI settings in `chatbot-service/.env`

### Municipal Settings
Update municipal information in Admin Panel > Settings

## 📱 Usage

1. **For Citizens**: Use the kiosk interface to submit service requests
2. **For Officers**: Login at `/officer` to process requests
3. **For Admins**: Login at `/admin` to manage the system

## 🛠️ Development

- **Frontend**: Blade templates with custom CSS
- **Backend**: Laravel 10
- **Database**: MySQL
- **Print Service**: Node.js
- **Chatbot**: Node.js with AI integration

## 🔌 Companion Services & Environment

| Service | Location | Purpose | Key .env switches |
| --- | --- | --- | --- |
| Print Service | `print-service/server.js` | Receives payloads from `RequestController`/kiosk and forwards them to the configured thermal printer (default URL `http://localhost:4000`). | `PRINT_SERVICE_URL` (if you add one), ensure the service runs with `npm start`. |
| Chatbot Service | `chatbot-service/` | Bridges kiosk chat UI to Dialogflow/AI responses via `ChatbotController`. | `CHATBOT_SERVICE_URL`, `CHATBOT_SERVICE_TIMEOUT`, `CHATBOT_SERVICE_ENABLED`. |
| SMS Service | `sms-service/send_sms.cjs` (Node) plus optional Python helper | Sends status-change SMS via Semaphore; consumed by `NotificationService`/`NodeSmsService`. | `SEMAPHORE_API_KEY`, `SEMAPHORE_SENDER_NAME`, `SEMAPHORE_ENABLED`, `SEMAPHORE_TEST_NUMBER`, `SEMAPHORE_USE_TEST_NUMBER`, `SMS_SERVICE_URL`, `SMS_SERVICE_ENABLED`, `SMS_SERVICE_TIMEOUT`, `PYTHON_SMS_ENABLED`. |

### Sample .env snippet
```env
CHATBOT_SERVICE_URL=http://localhost:5000
CHATBOT_SERVICE_TIMEOUT=30
CHATBOT_SERVICE_ENABLED=true

SEMAPHORE_API_KEY=your_api_key
SEMAPHORE_SENDER_NAME=Click2Serve
SEMAPHORE_ENABLED=true
SEMAPHORE_TEST_NUMBER=639219580125
SEMAPHORE_USE_TEST_NUMBER=true
SMS_SERVICE_URL=http://localhost:3001
SMS_SERVICE_ENABLED=true
SMS_SERVICE_TIMEOUT=30

PYTHON_SMS_ENABLED=false
```

> When deploying, provision these services alongside the Laravel app and update the `.env` file accordingly so health checks, kiosk printing, and officer notifications continue to work.

## 📄 License

This project is licensed under the MIT License.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📞 Support

For support and questions, please contact the development team.