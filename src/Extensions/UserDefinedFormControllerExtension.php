<?php

namespace NSWDPC\Messaging\Taggable;

use SilverStripe\Control\Email\Email;
use SilverStripe\Core\Extension;
use SilverStripe\UserForms\Model\Recipient\EmailRecipient;

/**
 * Apply EmailTags to an email destined for an {@link SilverStripe\UserForms\Model\Recipient\EmailRecipient}
 * @author James
 */
class UserDefinedFormControllerExtension extends Extension {

    use Taggable;

    public function updateEmail( Email $email, EmailRecipient $recipient, array $emailData) {
        $tags = $recipient->EmailTags()->sort('Name')->column('Name');
        if(empty($tags)) {
            // no tags
            return;
        }
        // set tags and headers
        $email->setNotificationTags( $tags );
    }
}
