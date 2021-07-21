<?php

namespace NSWDPC\Notifications\Taggable;

use SilverStripe\Control\Email\Email;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\UserForms\Model\Recipient\EmailRecipient;

/**
 * Apply EmailTags to an email destined for an {@link SilverStripe\UserForms\Model\Recipient\EmailRecipient}
 * @author James
 */
class UserDefinedFormControllerExtension extends Extension {

    public function updateEmail( Email $email, EmailRecipient $recipient, array $emailData) {
        $tags = $recipient->EmailTags()->column('Name');
        if(empty($tags)) {
            // no tags
            return;
        }

        $traits = class_uses($email);
        if( in_array( Taggable::class , $traits ) ) {
            // Email class + Mailer handles tags internally
            $email->setNotificationTags( $tags );
        } else {
            // else, use standard email header handling
            $headerName = Config::inst()->get( ProjectTags::class, 'tag_email_header_name' );
            // ignore header if it is not prefixed X-, avoids stomping standard headers
            if(stripos( $headerName, "X-") !== 0) {
                return false;
            }
            if($headerName) {
                $delimiter = Config::inst()->get( ProjectTags::class, 'tag_email_header_value_delimiter' );
                if(!$delimiter) {
                    $delimiter = ",";
                }
                $serialiser = Config::inst()->get( ProjectTags::class, 'tag_email_header_serialisation' );
                switch($serialiser) {
                    case ProjectTags::HEADER_SERIALISATION_JSON:
                        $headerValue = json_encode($tags);
                        break;
                    default:
                        $headerValue = implode($delimiter, $tags);
                        break;
                }
                $email->getSwiftMessage()->getHeaders()->addTextHeader($headerName,$headerValue);
            }
        }
    }
}
