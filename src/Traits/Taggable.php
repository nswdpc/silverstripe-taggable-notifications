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
        // tags
        $tags = [];
        // a project tag, if configured
        if($projectTag = Config::inst()->get( ProjectTags::class, 'tag' )) {
            $tags[] = $projectTag;
        }
        // tags for this notification
        $tags = array_merge($tags, $this->notificationTags);
        // get unique values
        $tags = array_unique($tags);
        // truncate based on tag limit
        $tagLimit = Config::inst()->get( ProjectTags::class, 'tag_limit' );
        if($tagLimit > 0) {
            return array_slice($tags, 0, $tagLimit);
        } else {
            return $tags;
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
