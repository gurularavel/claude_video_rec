# Video Call Customer Support System

A production-ready Laravel-based video call customer support system with real-time notifications, WebSocket communication, and cloud recording capabilities.

## 🎯 Features

- ✅ **Unique Support Link Generation**: Operators generate unique, signed URLs for customers
- ✅ **Real-Time WebSocket Notifications**: Instant notifications when customers join
- ✅ **Race Condition Prevention**: Database transactions prevent double-booking
- ✅ **Video Calls with Recording**: Twilio Video integration with automatic cloud recording
- ✅ **Operator Dashboard**: Real-time Livewire dashboard
- ✅ **Professional Waiting Room**: Seamless customer experience
- ✅ **Recording Management**: Automatic storage of recordings with webhooks

## 🛠️ Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Blade + Livewire 3
- **Real-time**: Laravel Reverb (WebSocket)
- **Video**: Twilio Video API with Recording
- **Database**: SQLite/MySQL/PostgreSQL
- **Auth**: Laravel UI

## 📋 Installation

### Prerequisites

```bash
PHP 8.2+
Composer
Node.js 18+
Twilio Account
```

### Quick Start

```bash
# 1. Install dependencies
composer install
npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Update .env with Twilio credentials
TWILIO_ACCOUNT_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_API_KEY=your_api_key
TWILIO_API_SECRET=your_api_secret

# 4. Setup database
touch database/database.sqlite
php artisan migrate

# 5. Create operator user
php artisan tinker
User::create(['name'=>'Operator','email'=>'op@test.com','password'=>bcrypt('password'),'is_operator'=>true,'status'=>'available']);

# 6. Build assets
npm run build

# 7. Start servers (3 terminals)
php artisan serve                  # Terminal 1
php artisan reverb:start          # Terminal 2
php artisan queue:work            # Terminal 3
```

### Get Twilio Credentials

1. Sign up at https://www.twilio.com/
2. Get Account SID & Auth Token from Console
3. Create API Key at https://www.twilio.com/console/project/api-keys
4. Update `.env` file

## 🎮 Usage

### Operator Workflow

1. Login at `http://localhost:8000`
2. Generate support link
3. Send link to customer
4. Accept call when customer joins
5. Conduct video support session
6. End call (recording auto-saved)

### Customer Workflow

1. Click support link
2. Wait in waiting room
3. Auto-redirected when operator accepts
4. Join video call

## 📁 Project Structure

```
app/
├── Events/              # WebSocket broadcast events
│   ├── CustomerWaiting.php
│   ├── OperatorAccepted.php
│   ├── CallStarted.php
│   └── CallEnded.php
├── Http/Controllers/
│   ├── SupportSessionController.php  # Main session logic
│   └── WebhookController.php         # Twilio webhooks
├── Livewire/
│   ├── Operator/Dashboard.php        # Operator dashboard
│   └── Customer/WaitingRoom.php      # Customer waiting
├── Models/
│   ├── User.php                      # Operator model
│   ├── SupportSession.php            # Session model
│   └── Recording.php                 # Recording model
└── Services/
    └── TwilioService.php             # Twilio integration

database/migrations/
├── *_add_operator_fields_to_users_table.php
├── *_create_support_sessions_table.php
└── *_create_recordings_table.php

resources/views/
├── operator/
│   ├── dashboard.blade.php           # Operator UI
│   └── video-room.blade.php          # Operator video
└── customer/
    ├── waiting-room.blade.php        # Waiting UI
    └── video-room.blade.php          # Customer video
```

## 🔄 Workflow Architecture

```
1. LINK GENERATION
   Operator → Generate Link → SupportSession (pending)

2. CUSTOMER JOINS
   Customer → Waiting Room → Session (waiting) → Broadcast CustomerWaiting

3. OPERATOR ACCEPTS
   Operator → Accept (DB Lock) → Session (active) → Broadcast OperatorAccepted

4. VIDEO CALL
   Both → Connect to Twilio Room → Recording Starts Automatically

5. CALL END
   Operator → End Call → Room Completed → Webhook → Save Recording
```

## 🔒 Security Features

- **Race Condition Prevention**: Row-level database locking
- **CSRF Protection**: All forms protected (except webhooks)
- **UUID Sessions**: Prevents enumeration attacks
- **Signed Routes**: Support links are signed and time-limited

## 📡 API Endpoints

### Operator (Auth Required)
- `POST /support/generate` - Generate link
- `POST /support/{uuid}/accept` - Accept call
- `POST /support/{uuid}/start-call` - Start video
- `POST /support/{uuid}/end-call` - End call
- `GET /support/{uuid}/video` - Video room

### Customer (Public)
- `GET /support/call/{uuid}` - Waiting room
- `POST /support/call/{uuid}/join` - Join waiting
- `GET /support/call/{uuid}/video` - Video room

### Webhooks (No CSRF)
- `POST /webhooks/twilio/room-status`
- `POST /webhooks/twilio/recording-status`

## 🎯 WebSocket Events

| Event | Channel | Triggered When |
|-------|---------|----------------|
| `CustomerWaiting` | `operators` | Customer joins waiting room |
| `OperatorAccepted` | `operators`, `support-session.{uuid}` | Operator accepts call |
| `CallStarted` | `support-session.{uuid}` | Video room created |
| `CallEnded` | `operators`, `support-session.{uuid}` | Call ends |

## 🚀 Deployment

### Production Setup

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql

BROADCAST_CONNECTION=reverb
REVERB_SCHEME=https
REVERB_HOST=yourdomain.com

QUEUE_CONNECTION=redis
```

### Supervisor Configuration

```ini
[program:reverb]
command=php /path/to/artisan reverb:start
autostart=true
autorestart=true

[program:queue]
command=php /path/to/artisan queue:work --tries=3
autostart=true
autorestart=true
```

### Twilio Webhooks

Configure in Twilio Console:
- Room Status: `https://yourdomain.com/webhooks/twilio/room-status`
- Recording Status: `https://yourdomain.com/webhooks/twilio/recording-status`

## 🐛 Troubleshooting

**WebSocket not connecting?**
- Ensure Reverb server is running
- Check port 8080 is open
- Verify REVERB_* env variables

**Video not working?**
- Verify Twilio credentials
- Check browser permissions (camera/mic)
- Test at: https://www.twilio.com/console/video/diagnostics

**Recordings not saving?**
- Verify webhook URLs are publicly accessible
- Check `storage/logs/laravel.log`
- Confirm Twilio webhook configuration

## 📝 License

MIT License - Open source and production-ready!

## 🙋 Support

Created as a comprehensive video support system. For questions, review the code comments and logs.
