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

You can install the package via Composer:

```bash
composer require crunchzapp/crunchzapp-php-sdk
```

### For Laravel Users

The package will be automatically discovered and registered in Laravel applications.

1.  **Publish the configuration file:**

    ```bash
    php artisan vendor:publish --tag=crunchzapp-config
    ```

    This will create a `config/crunchzapp.php` file in your application that you can modify to configure the SDK.

2.  **Set your API Token:**

    Add your CrunchzApp API token to your `.env` file. You can get your token from the [CrunchzApp Dashboard](https://crunchz.app).

    ```env
    CRUNCHZAPP_TOKEN="your-api-token"
    ```

### For Non-Laravel Users

If you are not using Laravel, you can instantiate the `CrunchzApp` class manually:

```php
require 'vendor/autoload.php';

use CrunchzApp\CrunchzApp;

$token = 'your-api-token';
$crunchz = new CrunchzApp($token);

// You can now use the $crunchz object to interact with the API
$response = $crunchz->channel()->health();
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
$validationResponse = $otpService
    ->contact('1234567890@c.us')
    ->validate('123456');
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

The SDK is designed to be fluent, allowing you to chain methods together to build your requests.

### `CrunchzApp`

The main entry point of the SDK.

- `new CrunchzApp(?string $token = null)`: Creates a new SDK instance. The token is optional if you have it configured in your `.env` file (for Laravel users).
- `channel(): ChannelService`: Returns an instance of the `ChannelService` for handling messaging, contacts, groups, and chats.
- `otp(string $type): OtpService`: Returns an instance of the `OtpService` for handling One-Time Passwords. The `$type` can be either `'code'` or `'link'`.

### `ChannelService`

Provides methods for all channel-related interactions.

**Execution Methods:**

- `send(): array`: Sends a single request. This should be the last method in a chain for single requests.
- `sendPool(): array`: Sends multiple requests in parallel.

**Messaging:**

- `contact(string $contactId)`: Sets the recipient's contact ID.
- `text(string $message)`: Sends a plain text message.
- `image(string $url, ?string $caption = null, ...)`: Sends an image from a URL.
- `video(string $videoUrl, ?string $caption = null)`: Sends a video from a URL.
- `voice(string $audioUrl)`: Sends a voice message from a URL.
- `location(float $latitude, float $longitude, ?string $title = null)`: Sends a location.
- `react(string $messageId, string $reaction)`: Reacts to a specific message.
- `polling(string $title, array $options, bool $isMultipleAnswer = false)`: Creates a poll.
- `star(string $messageId, bool $starred = true)`: Stars or unstars a message.
- `delete(string $messageId)`: Deletes a message.
- `seen(string $messageId)`: Marks a message as seen.
- `startTyping()`: Shows a "typing..." indicator in the chat.
- `stopTyping()`: Hides the "typing..." indicator.

**Contact Management:**

- `allContact()`: Retrieves a list of all contacts.
- `detail(string $contactId)`: Gets detailed information about a specific contact.
- `picture(string $contactId)`: Gets the profile picture URL of a contact.
- `checkPhoneNumber(string $phoneNumber, bool $toVariable = false)`: Checks if a phone number is registered on WhatsApp.

**Chat Management:**

- `allChat()`: Retrieves a list of all chats.
- `chatDetail(string $contactId)`: Gets detailed information about a specific chat.
- `archiveChat(string $contactId)`: Archives a chat.
- `unArchiveChat(string $contactId)`: Unarchives a chat.

**Group Management:**

- `allGroup()`: Retrieves a list of all groups.
- `createGroup(string $name, array $participants)`: Creates a new group.
- `participants(string $groupId)`: Retrieves the list of participants in a group.

### `OtpService`

Provides methods for sending and validating OTPs.

- `contact(string $contactId)`: Sets the recipient's contact ID for the OTP.
- `send(): array`: Sends the configured OTP.
- `validate(string $code): array`: Validates a code-based OTP.
- `code(string $code)`: Sets the code for validation (for code-based OTP).
- `prompt(string $message)`: Sets a custom prompt message (for link-based OTP).
- `responseMessage(?string $success, ?string $failed, ?string $expired)`: Sets custom success, failure, and expired messages (for link-based OTP).
- `callback(?string $successUrl, ?string $failedUrl)`: Sets callback URLs for success and failure events (for link-based OTP).

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
