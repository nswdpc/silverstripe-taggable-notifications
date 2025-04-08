<?php

namespace NSWDPC\Messaging\Taggable;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Taxonomy\TaxonomyTerm;
use SilverStripe\Taxonomy\TaxonomyType;
use SilverStripe\UserForms\Model\Recipient\EmailRecipient;

/**
 * Decorate a {@link SilverStripe\Taxonomy\TaxonomyTerm}
 * Each term can be linked to multiple recipients and
 * one configured {@link SilverStripe\Taxonomy\TaxonomyType}
 * @author James
 * @method \SilverStripe\ORM\ManyManyList<mixed> Recipients()
 * @extends \SilverStripe\ORM\DataExtension<static>
 */
class UserFormEmailTagsExtension extends DataExtension
{
    private static array $belongs_many_many = [
        // @phpstan-ignore class.notFound
        'Recipients' => EmailRecipient::class,
    ];

    /**
     * Retained for BC
     * @deprecated
     */
    public static function findOrMakeNotificationType(): TaxonomyType
    {
        return NotificationTags::findOrMakeType();
    }

    /**
     * Retained for BC
     * @deprecated
     */
    public function getNotificationTags(): \SilverStripe\ORM\DataList
    {
        return NotificationTags::getAvailableTerms();
    }

    /**
     * Do not show the Recipients tab in Taxonomy admin
     */
    #[\Override]
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('Recipients');
    }
}
