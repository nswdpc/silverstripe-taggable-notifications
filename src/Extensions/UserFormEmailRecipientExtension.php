<?php

namespace NSWDPC\Messaging\Taggable;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\TagField\TagField;
use SilverStripe\Taxonomy\TaxonomyTerm;
use SilverStripe\Taxonomy\TaxonomyType;

/**
 * Decorate a userdefined form recipient with notification tags
 * Each tag is a link to a {@link SilverStripe\Taxonomy\TaxonomyTerm} record
 * @author James
 * @method \SilverStripe\ORM\ManyManyList<\SilverStripe\Taxonomy\TaxonomyTerm> EmailTags()
 * @extends \SilverStripe\ORM\DataExtension<static>
 */
class UserFormEmailRecipientExtension extends DataExtension
{
    private static array $many_many = [
        'EmailTags' => TaxonomyTerm::class,
    ];

    /**
     * Render terms used into a string
     */
    public function EmailTagsNice(): string
    {
        $tags = $this->getOwner()->EmailTags()->sort('Name');
        $availableTags = NotificationTags::filterTermsByAvailable($tags);

        return $availableTags === [] ? "" : implode(", ", $availableTags);
    }

    /**
     * @inheritdoc
     */
    #[\Override]
    public function updateSummaryFields(&$fields)
    {
        $fields['EmailTagsNice'] = _t('Taggable.EMAIL_TAGS', 'Email tags');
    }

    /**
     * EmailRecipient post-write operations
     */
    #[\Override]
    public function onAfterWrite()
    {
        parent::onAfterWrite();
        // After write, ensure terms associated are linked to the correct type
        if ($terms = $this->getOwner()->EmailTags()) {
            $type = NotificationTags::findOrMakeType();
            foreach ($terms as $term) {
                $term->TypeID = $type->ID;
                $term->write();
            }
        }
    }

    /**
     * Add tag field to Email recipient
     */
    #[\Override]
    public function updateCmsFields(FieldList $fields)
    {
        $limit = intval(Config::inst()->get(ProjectTags::class, 'tag_limit'));
        $tag = trim(strip_tags((string) Config::inst()->get(ProjectTags::class, 'tag')));
        $description = "";

        if ($limit > 0) {
            if ($tag !== '') {
                $limit--;
                $description = _t('Taggable.TAG_LIMIT_MULTIPLE_PLUS_PROJECT_TAG', '{limit} tag(s) are allowed, in addition to the system tag <code>{tag}</code>.', ['limit' => $limit, 'tag' => $tag]);
            } else {
                $description = _t('Taggable.TAG_LIMIT_MULTIPLE_PLUS_PROJECT_TAG', 'The tag limit is {limit}.', ['limit' => $limit]);
            }
        }

        // TagField
        $availableTerms = NotificationTags::getAvailableTerms();
        // check if member can create tags
        $canCreate = TaxonomyTerm::create()->canCreate();
        $fields->addFieldToTab(
            'Root.EmailDetails',
            TagField::create(
                'EmailTags',
                _t('Taggable.EMAIL_TAGS', 'Email tags'),
                $availableTerms, // available terms
                $this->getOwner()->EmailTags()->sort('Name'), // current terms
                'Name' // title field: TaxonomyTerm.Name
            )->setCanCreate($canCreate)
            ->setShouldLazyLoad(true)
            ->setIsMultiple(true)
            ->setDescription($description)
        );
    }
}
