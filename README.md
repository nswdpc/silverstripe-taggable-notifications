# Taggable notifications for Silverstripe

A simple module supporting notification services that provide message tagging options (eg. Mailgun).

Depending on the service used, message tags be be used for message analytics, bottlenecks, rejection tracking and other failures (or successes).

## Features

- Project-level notification tags defined in configuration (optional)
- Tag limits (optional)
- A Trait for notification clients to use
- Userform extension to tag messages from a form, using `silverstripe/taxonomy`

## Requirements

There are no special requirements for using this module beyond the composer requirements and [a configuration](./docs/en/001_index.md) required for your notification service.

To set tags on a notification that uses the Taggable trait:

```php
$message = \My\App::getNotificationMessage($name, $number);
$tags = ['tag1','tag2','tag3'];
$message->setNotificationTags( $tags );
$message->deliver();
```

### Email

See the [Email section](./docs/en/001_index.md) for email configuration and examples.

#### User forms

If your project uses userforms, each Email recipient will get a tag field allowing per-recipient message tagging.

## Installation

```shell
composer require silverstripe-taggable-notifications
```

## License

[BSD-3-Clause](./LICENSE.md)

## Documentation

* [Documentation](./docs/en/001_index.md)

## Maintainers

+ PD Web Team

## Bugtracker

We welcome bug reports, pull requests and feature requests on the Github Issue tracker for this project.

Please review the [code of conduct](./code-of-conduct.md) prior to opening a new issue.

## Security

If you have found a security issue with this module, please email digital[@]dpc.nsw.gov.au in the first instance, detailing your findings.

## Development and contribution

If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.

Please review the [code of conduct](./code-of-conduct.md) prior to completing a pull request.
