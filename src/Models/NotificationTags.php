<?php

namespace NSWDPC\Messaging\Taggable;

use SilverStripe\ORM\DataList;
use SilverStripe\ORM\SS_List;
use SilverStripe\Taxonomy\TaxonomyTerm;
use SilverStripe\Taxonomy\TaxonomyType;

/**
 * Notification tag model
 * @author James
 */
class NotificationTags
{
    /**
     * Find or make the parent taxonomy type for notification tags
     */
    public static function findOrMakeType(): TaxonomyType
    {
        $typeName = _t('Taggable.NOTIFICATION_TERM_TYPE', 'Notification Tags');
        $record = ['Name' => $typeName ];
        $type = TaxonomyType::get()->filter($record)->first();
        if (!$type || !$type->isInDB()) {
            $type = TaxonomyType::create($record);
            $type->write();
        }
        return $type;
    }

    /**
     * Returns a map of Taxonomy terms under the configured notification type
     */
    public static function getAvailableTerms(): DataList
    {
        $type = self::findOrMakeType();
        $terms = TaxonomyTerm::get()
                    ->filter(['TypeID' => $type->ID ])
                    ->sort('Name ASC');
        return $terms;
    }

    /**
     * Given a list of TaxonomyTerm records, filter them by the Terms that are available as notification terms
     * @param \SilverStripe\ORM\DataList|\SilverStripe\ORM\UnsavedRelationList $terms
     */
    public static function filterTermsByAvailable(SS_List $terms): array
    {
        if ($terms->count() == 0) {
            return [];
        }
        $availableTerms = self::getAvailableTerms();
        if ($availableTerms->count() > 0) {
            // filter on these term(s)
            $terms = $terms->filter('ID', $availableTerms->column('ID'));
            $terms = $terms->column('Name');
        } else {
            // no available terms, none should be returned
            $terms = [];
        }
        return $terms;
    }
}
