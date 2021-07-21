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

    use Taggable;

    public function updateEmail( Email $email, EmailRecipient $recipient, array $emailData) {
        $tags = $recipient->EmailTags()->sort('Name')->column('Name');
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
            if(!$headerName || stripos( $headerName, "X-") !== 0) {
                return false;
            }

            // Process tags
            $this->setNotificationTags( $tags );
            $tags = $this->getNotificationTags();

            $serialiser = Config::inst()->get( ProjectTags::class, 'tag_email_header_serialisation' );
            switch($serialiser) {
                case ProjectTags::HEADER_SERIALISATION_JSON:
                    $email->getSwiftMessage()->getHeaders()->addTextHeader( $headerName, json_encode($tags) );
                    break;
                case ProjectTags::HEADER_SERIALISATION_CSV:
                    // Get delimited or fall back to ,
                    $delimiter = Config::inst()->get( ProjectTags::class, 'tag_email_header_value_delimiter' );
                    if($delimiter === '') {
                        $delimiter = ",";
                    }
                    $email->getSwiftMessage()->getHeaders()->addTextHeader( $headerName, implode($delimiter, $tags) );
                    break;
                default:
                    // need to set a header with an index
                    $factory = new \Swift_Mime_SimpleHeaderFactory(
                        new \Swift_Mime_HeaderEncoder_Base64HeaderEncoder(),
                        new \Swift_Encoder_Base64Encoder(),
                        new \Swift_Mime_Grammar()
                    );
                    // each tag
                    foreach($tags as $index => $tag) {
                        // create a header
                        $header = $factory->createTextHeader($headerName, $tag);
                        // set at this index
                        $email->getSwiftMessage()->getHeaders()->set($header, 'tag-header-' . $index);
                    }
                    break;
            }
        }
    }
}
