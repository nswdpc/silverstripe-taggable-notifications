<?php

namespace NSWDPC\Messaging\Taggable;

use SilverStripe\Control\Email\Email;
use SilverStripe\Core\Extension;
use SilverStripe\UserForms\Model\Recipient\EmailRecipient;

/**
 * Extension for the {@link SilverStripe\UserForms\Control\UserDefinedFormController} to update email data prior to sending
 * @author James
 */
class UserDefinedFormControllerExtension extends Extension
{
    use Taggable;

    /**
     * Apply EmailTags to an email destined for an {@link SilverStripe\UserForms\Model\Recipient\EmailRecipient}
     */
    public function updateEmail(Email $email, EmailRecipient $recipient, array $emailData)
    {
        // @phpstan-ignore method.notFound
        $tags = $recipient->EmailTags()->sort('Name');
        $availableTags = NotificationTags::filterTermsByAvailable($tags);
        if ($availableTags === []) {
            // no tags
            return;
        }

        // set tags and headers
        /** @var \NSWDPC\Messaging\Taggable\TaggableEmail $email */
        $email->setNotificationTags($availableTags);
    }
}
