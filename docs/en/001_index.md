# Documentation

## Mailgun configuration

Mailgun allows for 3 tags per messages. If you use a project tag, the number of specific message tags is reduced to 2.

```yaml
---
Name: app-taggable-notifications
After:
  - '#nswdpc-taggable-notifications'
---
NSWDPC\Notifications\Taggable\ProjectTags:
  tag: ''
  tag_limit: 3
  # If sending via SMTP, the following is required
  tag_email_header_name: 'X-Mailgun-Tag'
  tag_email_header_serialisation: 'csv'
  tag_email_header_value_delimiter: ','
 ```
