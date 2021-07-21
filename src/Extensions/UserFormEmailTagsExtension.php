<?php

namespace NSWDPC\Notifications\Taggable;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Taxonomy\TaxonomyTerm;
use SilverStripe\Taxonomy\TaxonomyType;
use SilverStripe\UserForms\Model\Recipient\EmailRecipient;

/**
 * Decorate a {@link SilverStripe\Taxonomy\TaxonomyTerm}
 * Each term can be linked to multiple recipients and
 * one configured {@link SilverStripe\Taxonomy\TaxonomyType}
 * @author James
 */
class UserFormEmailTagsExtension extends DataExtension {

    /**
     * @var array
     */
    private static $belongs_many_many = [
        'Recipients' => EmailRecipient::class,
    ];

    /**
     * Find or make the parent taxonomy type for notification tags
     */
    public function findOrMakeNotificationType() : TaxonomyType {
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
     */
    public function getNotificationTags() {
        $type = $this->owner->findOrMakeNotificationType();
        $terms = TaxonomyTerm::get()
                    ->filter( ['TypeID' => $type->ID ] )
                    ->sort('Name ASC');
        return $terms;
    }

    /**
     * Assign type on write
     */
    public function onBeforeWrite() {
        $type = $this->findOrMakeNotificationType();
        $this->owner->TypeID = $type->ID;
    }

}
