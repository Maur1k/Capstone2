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
Configure printer settings in `print-service/server.js`

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