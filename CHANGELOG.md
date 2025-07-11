# Changelog

All notable changes to the CrunchzApp PHP SDK will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial release of CrunchzApp PHP SDK
- WhatsApp messaging capabilities through ChannelService
- OTP (One-Time Password) functionality with code and link-based verification
- Comprehensive message types support (text, image, location, voice, video, reactions, polling)
- Contact management features
- Chat management with archive/unarchive functionality
- Group management capabilities
- Laravel service provider integration
- Configuration file with environment variable support
- Parallel request processing using HTTP pools
- Fluent API interface for easy method chaining

### Features

#### Core SDK
- **CrunchzApp Factory Class**: Main entry point with static factory methods
- **Service Provider**: Laravel integration with configuration publishing
- **Configuration Management**: Comprehensive config file with environment variable support

#### WhatsApp Channel Service
- **Message Types**:
  - Text messages with validation
  - Image messages with caption and file support
  - Location messages with coordinates validation
  - Voice messages with audio URL support
  - Video messages with caption support
  - Message reactions with emoji support
  - Polling messages with multiple options
  - Message starring/unstarring
  - Message deletion
  - Message seen status
  - Typing indicators (start/stop)

- **Contact Management**:
  - Retrieve all contacts
  - Get contact details by ID
  - Fetch contact pictures
  - Phone number existence validation

- **Chat Management**:
  - List all chats
  - Get chat details for specific contacts
  - Archive/unarchive chats

- **Group Management**:
  - Retrieve all groups
  - Create new groups with participants
  - Get group participants list

- **Advanced Features**:
  - Parallel request processing with HTTP pools
  - Single request mode for individual operations
  - Channel health monitoring
  - Phone number validation on WhatsApp

#### OTP Service
- **Code-based OTP**:
  - Configurable code length and character sets
  - Support for letters, numbers, and case options
  - Customizable expiration times
  - Application name branding

- **Link-based OTP**:
  - Custom prompt messages
  - Success/failure/expired response messages
  - Callback URL support for success/failure events
  - Configurable expiration times

- **Validation Features**:
  - Direct code validation with error handling
  - Fluent interface for setting codes before validation
  - Comprehensive error messages and exception handling

### Technical Improvements

#### Code Quality
- **Error Handling**: Comprehensive exception handling with descriptive messages
- **Validation**: Input validation for all parameters with appropriate error messages
- **Type Safety**: Strict typing throughout the codebase
- **Documentation**: Extensive PHPDoc comments for all methods and classes

#### Architecture
- **Base Classes**: Abstract base classes for shared functionality
- **Traits**: Modular trait system for feature organization
- **Service Pattern**: Clean service-based architecture
- **Factory Pattern**: Static factory methods for service instantiation

#### Laravel Integration
- **Service Provider**: Automatic registration and configuration publishing
- **Configuration**: Environment-based configuration with sensible defaults
- **HTTP Client**: Laravel HTTP client integration with token authentication
- **Facade Support**: Easy integration with Laravel's service container

### Configuration

#### Environment Variables
- `CRUNCHZAPP_TOKEN`: API authentication token
- `CRUNCHZAPP_API_URL`: Custom API endpoint URL
- `CRUNCHZAPP_TIMEOUT`: Request timeout configuration
- `CRUNCHZAPP_OTP_*`: Comprehensive OTP configuration options

#### Default Settings
- API timeout: 30 seconds
- OTP code length: 4 characters
- OTP expiration: 30 minutes (1800 seconds)
- Character sets: Letters and numbers enabled by default

### Security
- **Token Validation**: Mandatory token validation for all API operations
- **Input Sanitization**: Comprehensive input validation and sanitization
- **URL Validation**: Strict URL format validation for media and callbacks
- **Error Handling**: Secure error messages without sensitive data exposure

### Performance
- **HTTP Pools**: Parallel request processing for batch operations
- **Efficient Validation**: Early validation to prevent unnecessary API calls
- **Optimized Payloads**: Minimal request payloads with required data only
- **Connection Reuse**: HTTP client connection reuse for multiple requests

### Developer Experience
- **Fluent Interface**: Method chaining for intuitive API usage
- **Clear Error Messages**: Descriptive error messages for debugging
- **Comprehensive Examples**: Usage examples in documentation
- **IDE Support**: Full PHPDoc annotations for IDE autocompletion

### Dependencies
- **PHP**: ^8.1
- **Laravel Framework**: ^9.0|^10.0|^11.0
- **Illuminate/Support**: For Laravel integration
- **Illuminate/HTTP**: For HTTP client functionality

### Installation
- **Composer**: Available via Composer package manager
- **Laravel**: Automatic service provider discovery
- **Configuration**: Publishable configuration file

---

## Version History

### [1.0.0] - Initial Release
- Complete WhatsApp messaging SDK
- OTP verification system
- Laravel integration
- Comprehensive documentation
- Production-ready codebase

---

## Migration Guide

This is the initial release, so no migration is required.

## Breaking Changes

None in this initial release.

## Deprecations

None in this initial release.

## Security Updates

None in this initial release.

---

*For more information about specific features and usage examples, please refer to the [README.md](README.md) file.*