<?php

namespace NSWDPC\Notifications\Taggable;

use SilverStripe\Core\Config\Config;

/**
 * Taggable trait for notification handling
 * @author James
 */
trait Taggable {

    /**
     * @var array
     */
    protected $notificationTags = [];

    /**
     * Get tags used for this notification
     */
    public function getNotificationTags() : array {

        // tags for this notification
        $result = $this->notificationTags;

        // a project tag, if configured
        if($projectTag = Config::inst()->get( ProjectTags::class, 'tag' )) {
            $result[] = $projectTag;
        }

        // get unique values
        $result = array_unique($result);

        // truncate based on tag limit
        $tagLimit = Config::inst()->get( ProjectTags::class, 'tag_limit' );
        if($tagLimit > 0) {
            return array_slice($result, 0, $tagLimit);
        } else {
            return $result;
        }

    }

    /**
     * Set tags used for this notification
     * @param array $tags
     */
    public function setNotificationTags(array $tags) {
        $this->notificationTags = $tags;
        return $this;
    }
}
