# CrunchzApp - WhatsApp PHP SDK

[![Latest Version](https://img.shields.io/packagist/v/crunchzapp/crunchzapp-php-sdk.svg?style=flat-square)](https://packagist.org/packages/crunchzapp/crunchzapp-php-sdk)
[![PHP Version](https://img.shields.io/packagist/php-v/crunchzapp/crunchzapp-php-sdk.svg?style=flat-square)](https://packagist.org/packages/crunchzapp/crunchzapp-php-sdk)
[![License](https://img.shields.io/packagist/l/crunchzapp/crunchzapp-php-sdk.svg?style=flat-square)](https://packagist.org/packages/crunchzapp/crunchzapp-php-sdk)

**CrunchzApp - WhatsApp PHP SDK** provides a comprehensive and easy-to-use interface for integrating WhatsApp automation into your PHP applications using the CrunchzApp API. This SDK enables developers to send messages, manage contacts, handle OTP authentication, manage groups and chats, and automate WhatsApp interactions with minimal effort.

## 🚀 Features

- **📱 Message Management**: Send text, images, videos, voice messages, locations, and reactions
- **🔐 OTP Services**: Generate and validate OTP codes and links
- **👥 Contact Management**: Retrieve contact details, pictures, and manage contact lists
- **💬 Chat Management**: Archive/unarchive chats and retrieve chat details
- **👨‍👩‍👧‍👦 Group Management**: Create groups, manage participants, and retrieve group information
- **⚡ Batch Operations**: Send multiple requests in parallel using HTTP pools
- **🛡️ Type Safety**: Full type hints and comprehensive error handling
- **🔧 Laravel Integration**: Native Laravel service provider and configuration

## 📋 Requirements

- PHP 8.0 or higher
- Laravel 9.0 or higher
- Illuminate HTTP Client
- Valid CrunchzApp API token

## 📦 Installation

Install the package via Composer:

```bash
composer require crunchzapp/crunchzapp-php-sdk
```

### Laravel Setup

1. **Publish the configuration file:**

```bash
php artisan vendor:publish --tag=crunchzapp-config
```

2. **Set your API token in `.env`:**

```env
CRUNCHZAPP_TOKEN=your_api_token_here
CRUNCHZAPP_API_URL=https://api.crunchz.app
CRUNCHZAPP_TIMEOUT=30
```

3. **Configure OTP settings in `config/crunchzapp.php`:**

```php
return [
    'timeout' => env('CRUNCHZAPP_TIMEOUT', 30),
    'token' => env('CRUNCHZAPP_TOKEN'),
    'api_url' => env('CRUNCHZAPP_API_URL', 'https://api.crunchz.app'),
    
    'otp' => [
        'code' => [
            'length' => 6,
            'useLetter' => false,
            'useNumber' => true,
            'allCapital' => false,
            'name' => 'Your App Name',
            'expires' => 300, // 5 minutes
            // ... more configuration
        ],
        'link' => [
            'name' => 'Your App Name',
            'expires' => 300,
            // ... more configuration
        ]
    ]
];
```

## 🔧 Basic Usage

### Sending Messages

```php
use CrunchzApp\CrunchzApp;

// Initialize the service
$crunchz = app(CrunchzApp::class);

// Send a text message
$response = $crunchz->channel()
    ->contact('1234567890@c.us')
    ->text('Hello, World!')
    ->send();

// Send an image with caption
$response = $crunchz->channel()
    ->contact('1234567890@c.us')
    ->image('https://example.com/image.jpg', 'Check this out!')
    ->send();

// Send a location
$response = $crunchz->channel()
    ->contact('1234567890@c.us')
    ->location(-6.2088, 106.8456, 'Jakarta, Indonesia')
    ->send();

// Send a video with caption
$response = $crunchz->channel()
    ->contact('1234567890@c.us')
    ->video('https://example.com/video.mp4', 'Amazing video!')
    ->send();
```

### Batch Operations

```php
// Send multiple messages in parallel
$responses = $crunchz->channel()
    ->contact('1234567890@c.us')
    ->text('First message')
    ->contact('0987654321@c.us')
    ->text('Second message')
    ->sendPool();

foreach ($responses as $response) {
    echo "Path: {$response['path']}\n";
    echo "Result: " . json_encode($response['result']) . "\n";
}
```

### OTP Services

#### Code-based OTP

```php
// Generate and send OTP code
$otpService = $crunchz->otp('code');

$response = $otpService
    ->contact('1234567890@c.us')
    ->send();

// Validate OTP code
$validationResponse = $otpService
    ->contact('1234567890@c.us')
    ->validate('123456');

// Alternative: Set code first, then validate
$otpService
    ->contact('1234567890@c.us')
    ->code('123456');
    
$validationResponse = $otpService->validateOtp();
```

#### Link-based OTP

```php
// Generate and send OTP link
$otpService = $crunchz->otp('link');

$response = $otpService
    ->contact('1234567890@c.us')
    ->send();
```

### Contact Management

```php
// Get all contacts
$contacts = $crunchz->channel()
    ->allContact()
    ->send();

// Get contact details
$contactDetails = $crunchz->channel()
    ->detail('1234567890@c.us')
    ->send();

// Get contact picture
$contactPicture = $crunchz->channel()
    ->picture('1234567890@c.us')
    ->send();
```

### Chat Management

```php
// Get all chats
$chats = $crunchz->channel()
    ->allChat()
    ->send();

// Get chat details
$chatDetails = $crunchz->channel()
    ->chatDetail('1234567890@c.us')
    ->send();

// Archive a chat
$crunchz->channel()
    ->archiveChat('1234567890@c.us')
    ->send();

// Unarchive a chat
$crunchz->channel()
    ->unArchiveChat('1234567890@c.us')
    ->send();
```

### Group Management

```php
// Get all groups
$groups = $crunchz->channel()
    ->allGroup()
    ->send();

// Create a new group
$newGroup = $crunchz->channel()
    ->createGroup('My Group', [
        '1234567890@c.us',
        '0987654321@c.us'
    ])
    ->send();

// Get group participants
$participants = $crunchz->channel()
    ->participants('group_id_here')
    ->send();
```

### Advanced Message Features

```php
// React to a message
$crunchz->channel()
    ->contact('1234567890@c.us')
    ->react('message_id_here', '👍')
    ->send();

// Send a poll
$crunchz->channel()
    ->contact('1234567890@c.us')
    ->polling('What\'s your favorite color?', [
        'Red', 'Blue', 'Green', 'Yellow'
    ], false) // false = single answer, true = multiple answers
    ->send();

// Start/stop typing indicator
$crunchz->channel()
    ->contact('1234567890@c.us')
    ->startTyping()
    ->send();

$crunchz->channel()
    ->contact('1234567890@c.us')
    ->stopTyping()
    ->send();
```

## 🛡️ Error Handling

The SDK provides comprehensive error handling:

```php
use CrunchzApp\CrunchzApp;
use RuntimeException;
use InvalidArgumentException;

try {
    $response = $crunchz->channel()
        ->contact('invalid_contact')
        ->text('Hello')
        ->send();
} catch (InvalidArgumentException $e) {
    // Handle validation errors (empty messages, invalid contact IDs, etc.)
    echo "Validation Error: " . $e->getMessage();
} catch (RuntimeException $e) {
    // Handle API errors (network issues, authentication, etc.)
    echo "API Error: " . $e->getMessage();
}
```

## 🔧 Configuration

The configuration file `config/crunchzapp.php` allows you to customize:

- **API Settings**: Timeout, base URL, authentication token
- **OTP Configuration**: Length, character sets, expiration times
- **Message Templates**: Success/failure messages, prompts
- **Callback URLs**: Success and failure webhook endpoints

## 📚 API Reference

### CrunchzApp Main Class

- `channel()`: Get channel service for messaging and contact management
- `otp(string $type)`: Get OTP service ('code' or 'link')

### Channel Service Methods

#### Messaging
- `contact(string $contactId)`: Set target contact
- `text(string $message)`: Send text message
- `image(string $url, ?string $caption)`: Send image
- `video(string $url, ?string $caption)`: Send video
- `voice(string $audioUrl)`: Send voice message
- `location(float $lat, float $lng, ?string $title)`: Send location
- `react(string $messageId, string $reaction)`: React to message
- `polling(string $title, array $options, bool $multiple)`: Send poll
- `startTyping()` / `stopTyping()`: Typing indicators

#### Contacts
- `allContact()`: Get all contacts
- `detail(string $contactId)`: Get contact details
- `picture(string $contactId)`: Get contact picture

#### Chats
- `allChat()`: Get all chats
- `chatDetail(string $contactId)`: Get chat details
- `archiveChat(string $contactId)`: Archive chat
- `unArchiveChat(string $contactId)`: Unarchive chat

#### Groups
- `allGroup()`: Get all groups
- `createGroup(string $name, array $participants)`: Create group
- `participants(string $groupId)`: Get group participants

#### Execution
- `send()`: Execute single request
- `sendPool()`: Execute multiple requests in parallel

### OTP Service Methods

- `contact(string $contactId)`: Set target contact
- `code(string $code)`: Set OTP code (for validation)
- `send()`: Send OTP
- `validate(string $code)`: Validate OTP code
- `validateOtp()`: Validate previously set OTP code
- `generate()`: Alias for send()

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## 🔗 Links

- **Official Documentation**: [https://crunczhapp.readme.io/reference/laravel-php](https://crunczhapp.readme.io/reference/laravel-php)
- **Website**: [https://www.crunchz.app](https://www.crunchz.app)
- **Support**: [Contact Support](https://www.crunchz.app/support)

## 💰 Special Offer

Register and use voucher code **"25OFFCRZAPP"** to get **25% OFF** your subscription!

---

**Made with ❤️ by the CrunchzApp Team**
