<?php

namespace NSWDPC\Messaging\Taggable;

use Egulias\EmailValidator\EmailValidator;
use SilverStripe\Core\Config\Config;
use SilverStripe\Control\Email\Email;

/**
 * A class to handle Taggable email
 * @author James
 */
class TaggableEmail extends Email
{
    use Taggable {
        setNotificationTags as setTaggableNotificationTags;
    }

    /**
     * Set tag headers on the email message, based on project configuration
     * Classing this method will set tags as headers on the SwiftMessage
     */
    public function setNotificationTags(array $tags): static
    {
        $this->setTaggableNotificationTags($tags);

        // If there is no header provided, then tags aren't handled by email headers
        // The Mailer should handle tags set on this message
        $headerName = Config::inst()->get(ProjectTags::class, 'tag_email_header_name');
        if (!$headerName) {
            return $this;
        }

        // The header configured must be X- prefixe
        // avoids stomping standard headers
        if (stripos((string) $headerName, "X-") !== 0) {
            return $this;
        }

        // get notification tags already set, if any
        $tags = $this->getNotificationTags();
        if ($tags === []) {
            return $this;
        }

        $serialiser = Config::inst()->get(ProjectTags::class, 'tag_email_header_serialisation');
        switch ($serialiser) {
            case ProjectTags::HEADER_SERIALISATION_JSON:
                $this->getSwiftMessage()->getHeaders()->addTextHeader($headerName, json_encode($tags));
                break;
            case ProjectTags::HEADER_SERIALISATION_CSV:
                // Get delimited or fall back to ,
                $delimiter = Config::inst()->get(ProjectTags::class, 'tag_email_header_value_delimiter');
                if ($delimiter === '') {
                    $delimiter = ",";
                }

                $this->getSwiftMessage()->getHeaders()->addTextHeader($headerName, implode($delimiter, $tags));
                break;
            default:
                // need to set a header with an index
                $factory = new \Swift_Mime_SimpleHeaderFactory(
                    new \Swift_Mime_HeaderEncoder_Base64HeaderEncoder(),
                    new \Swift_Encoder_Base64Encoder(),
                    new EmailValidator()
                );
                // each tag
                foreach ($tags as $index => $tag) {
                    // create a header
                    $header = $factory->createTextHeader($headerName, $tag);
                    // set at this index
                    $this->getSwiftMessage()->getHeaders()->set($header, $index);
                }

                break;
        }

        return $this;
    }
}
