<?php

namespace NSWDPC\Messaging\Taggable;

use SilverStripe\Control\Email\Email;
use SilverStripe\Core\Extension;
use SilverStripe\UserForms\Model\Recipient\EmailRecipient;

/**
 * Extension for the {@link SilverStripe\UserForms\Control\UserDefinedFormController} to update email data prior to sending
 * @author James
 * @extends \SilverStripe\Core\Extension<static>
 */
class UserDefinedFormControllerExtension extends Extension
{
    use Taggable;

    /**
     * Apply EmailTags to an email destined for an {@link SilverStripe\UserForms\Model\Recipient\EmailRecipient}
     * @phpstan-ignore class.notFound
     */
    public function updateEmail(Email $email, EmailRecipient $recipient, array $emailData)
    {

        // Bail if the $email instance doesn't support tagging
        if (!($email instanceof TaggableEmail)) {
            return;
        }

        // @phpstan-ignore class.notFound
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
