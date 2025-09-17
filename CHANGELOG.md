## v1.3.0

#### Published: 2025-09-17

Added support for custom attribute names in blacklist error messages.

- `checkFields()` method now accepts an optional `$attributes` array to map field names to human-readable labels.
- Error messages use these attribute names instead of raw field keys, improving UX and consistency with Laravel
  validation.
- This allows seamless integration with Laravel's built-in validation attribute naming conventions.

## v1.2.2

#### Published: 2025-08-29

- Enhance README with responsive badge links
- Adds a list of other Laravel packages to the README
- Improved DISCLAIMER in README

## v1.2.1

#### Published at: 2025-08-09

- Updated readme file
- More bad words added

## v1.2.0

#### Published at: 2025-04-26

- **IMPORTANT**: Improved word matching to use whole word boundaries instead of substring matching (prevents false
  positives)
- Fixed tests to properly validate whole word matching
- Updated log message format for better clarity
- Fixed risky tests by adding explicit assertions

## v1.1.0

#### Published at: 2025-04-26

- Added new profanity/offensive words list
- Added configuration option to choose which word lists to use (blacklist, profanity, or both)
- Enhanced error messages to indicate which list a matched term belongs to
- Updated documentation with new features

## v1.0.0

#### Published at: 2025-04-26

- Initial release
