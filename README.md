# Taggable notifications for Silverstripe

A simple module supporting notification services that provide message tagging options (eg. Mailgun).

Depending on the service used, message tags be be used for message analytics, bottlenecks, rejection tracking and other failures (or successes).

## Features

- Project-level notification tags defined in configuration (optional)
- Tag limits (optional)
- A Trait for notification clients to use
- Userform extension to tag messages from a form, using `silverstripe/taxonomy`

## Requirements

There are no special requirements for using this module beyond the composer requirements and the configuration required for your notification service.

Some examples and tips exist [in the documentation](./docs/en/001_index.md).

## User forms

If your project uses userforms, each Email recipient will get a tag field allowing per-recipient message tagging.

## Taggable

Notification services that use the `Taggable` trait should use `setNotificationTags` and `getNotificationTags` to get and set message tags, respectively.

If your message service restricts the number of tags per message, this is handled by the Taggable trait. Tags are reduced to the number specified in configuration, with the remainder discarded.

## Installation

```shell
composer require silverstripe-taggable-notifications
```

## License

[BSD-3-Clause](./LICENSE.md)

## Documentation

* [Documentation](./docs/en/001_index.md)

## Configuration

Add project level tags. These are added to all notifications sent by the service.

```yaml
---
Name: app-notification-tagging
After:
  - '#nswdpc-taggable-notifications'
---
NSWDPC\Notifications\Taggable\ProjectTags:
 tags:
   - 'a-project-tag'
   - 'site name'
   - 'site version'
```

## Maintainers

+ [dpcdigital@NSWDPC:~$](https://dpc.nsw.gov.au)

> Add additional maintainers here and/or include [authors in composer](https://getcomposer.org/doc/04-schema.md#authors)

## Bugtracker

We welcome bug reports, pull requests and feature requests on the Github Issue tracker for this project.

Please review the [code of conduct](./code-of-conduct.md) prior to opening a new issue.

## Security

If you have found a security issue with this module, please email digital[@]dpc.nsw.gov.au in the first instance, detailing your findings.

## Development and contribution

If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.

Please review the [code of conduct](./code-of-conduct.md) prior to completing a pull request.
