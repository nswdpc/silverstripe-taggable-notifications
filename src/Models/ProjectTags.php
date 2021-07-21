<?php

namespace NSWDPC\Notifications\Taggable;

use SilverStripe\Core\Config\Configurable;

/**
 * Project tag model
 * @author James
 */
class ProjectTags {

    use Configurable;

    const HEADER_SERIALISATION_CSV = 'csv';
    const HEADER_SERIALISATION_JSON = 'json';

    /**
     * @var int
     * Some services place a limit on tags per notification
     * The limit includes any project tags configured below + notification tags
     */
    private static $tag_limit = 0;

    /**
     * @var string
     * Single private tag for use at a project level eg. when notification system is shared with other projects
     */
    private static $tag = '';

    /**
     * @var string
     * The email header name to set on the Email class, used if the Email class does not use the Taggable trait
     */
    private static $tag_email_header_name = '';

    /**
     * @var string
     * How the tag values are serialised, if adding tags directly to email header
     */
    private static $tag_email_header_serialisation = 'csv';

    /**
     * @var string
     * How the tag values are delimited, if using serialisation
     */
    private static $tag_email_header_value_delimiter = ',';

}
