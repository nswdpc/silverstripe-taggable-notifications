<?php

namespace NSWDPC\Messaging\Taggable;

use SilverStripe\Taxonomy\TaxonomyTerm;
use SilverStripe\Taxonomy\TaxonomyType;

/**
 * Notification tag model
 * @author James
 */
class NotificationTags {

    /**
     * Find or make the parent taxonomy type for notification tags
     * @return SilverStripe\Taxonomy\TaxonomyType
     */
    public static function findOrMakeType() : TaxonomyType {
        $typeName = _t('Taggable.NOTIFICATION_TERM_TYPE', 'Notification Tags');
        $record = ['Name' => $typeName ];
        $type = TaxonomyType::get()->filter( $record )->first();
        if(!$type || !$type->isInDB()) {
            $type = TaxonomyType::create($record);
            $type->write();
        }
        return $type;
    }

    /**
     * Returns a map of Taxonomy terms under the configured notification type
     * @return DataList
     */
    public static function getAvailableTerms() {
        $type = self::findOrMakeType();
        $terms = TaxonomyTerm::get()
                    ->filter( ['TypeID' => $type->ID ] )
                    ->sort('Name ASC');
        return $terms;
    }

}
