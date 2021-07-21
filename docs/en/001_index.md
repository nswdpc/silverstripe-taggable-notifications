# Documentation

## Limits

The Taggable trait will slice off any tags that are above the limit configured. If there is no limit, set it as 0.

If you have a project tag configured and 5 message tags, but your email service limits tagging to 5 per message, the project tag + 4 message tags will be sent.

## Header naming

Your header name should begin `X-` otherwise it will be ignored.

## Example: userforms

Configure the module based on the requirements of your mail transport:

```yaml
---
Name: 'app-taggable-notifications'
After:
  - '#nswdpc-taggable-notifications'
---
NSWDPC\Notifications\Taggable\ProjectTags:
  # tag every message with this tag
  tag: 'my-app'
  # a tag limit of 10 exists
  tag_limit: 10
  tag_email_header_name: 'X-MyPipeDelimited-Tags'
  # implode the tags
  tag_email_header_serialisation: 'csv'
  # delimited by a pipe character
  tag_email_header_value_delimiter: '|'
```

The header in the email will appears as follows

```
X-MyPipeDelimited-Tags: my-app|tag1|tag2|tag3
```

## Example: Mailgun configuration

Mailgun allows for 3 tags per messages. This means that if you use a project tag, the number of specific message tags is reduced to 2.

If you pass more than 3 tags to a Mailgun message, Mailgun will add the final 3 in the message.

```yaml
---
Name: 'app-taggable-notifications'
After:
  - '#nswdpc-taggable-notifications'
---
NSWDPC\Notifications\Taggable\ProjectTags:
  # API and SMTP transport configuration
  tag: ''
  tag_limit: 3
  # The following applies to SMTP only
  tag_email_header_name: 'X-Mailgun-Tag'
  # Send one X-Mailgun-Tag header per tag
  tag_email_header_serialisation: 'multi'
  tag_email_header_value_delimiter: ''
```

The headers in the email will appears as follows

```
X-Mailgun-Tag: tag1
X-Mailgun-Tag: tag2
X-Mailgun-Tag: tag3
```
