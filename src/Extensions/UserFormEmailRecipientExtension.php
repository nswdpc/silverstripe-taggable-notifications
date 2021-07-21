<?php

namespace NSWDPC\Notifications\Taggable;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\Fieldlist;
use SilverStripe\ORM\DataExtension;
use SilverStripe\TagField\TagField;
use SilverStripe\Taxonomy\TaxonomyTerm;
use SilverStripe\Taxonomy\TaxonomyType;

/**
 * Decorate a userdefined form recipient with notification tags
 * Each tag is a link to a {@link SilverStripe\Taxonomy\TaxonomyTerm} record
 * @author James
 */
class UserFormEmailRecipientExtension extends DataExtension {

    /**
     * @var array
     */
    private static $many_many = [
        'EmailTags' => TaxonomyTerm::class,
    ];

    /**
     * Render terms used into a string
     */
    public function EmailTagsNice() : string {
        $tags = $this->owner->EmailTags()->column('Name');
        if(is_array($tags) && !empty($tags)) {
            $terms = implode(", ", $tags);
        } else {
            $terms = "";
        }
        return $terms;
    }

    /**
     * @var array
     */
    public function updateSummaryFields(&$fields) {
        $fields['EmailTagsNice'] = _t('Taggable.EMAIL_TAGS','Email tags');
    }

    /**
     * Add tag field to Email recipient
     * @param Fieldlist
     */
    public function updateCmsFields(Fieldlist $fields) {

        $limit = intval(Config::inst()->get(ProjectTags::class, 'tag_limit'));
        $tag = trim(strip_tags(Config::inst()->get(ProjectTags::class, 'tag')));

        $description = "";
        if($limit > 0) {
            if($tag) {
                $limit--;
                $description = _t('Taggable.TAG_LIMIT_MULTIPLE_PLUS_PROJECT_TAG', '{limit} tag(s) are allowed, in addition to the system tag <code>{tag}</code>', ['limit' => $limit, 'tag' => $tag] );
            } else {
                $description = _t('Taggable.TAG_LIMIT_MULTIPLE_PLUS_PROJECT_TAG', 'The tag limit is {limit}', ['limit' => $limit] );
            }
        }

        // Use a configured type name
        $terms = TaxonomyTerm::create()->getNotificationTags();
        $fields->addFieldToTab(
            'Root.EmailDetails',
            TagField::create(
              'EmailTags',
              _t('Taggable.EMAIL_TAGS','Email tags'),
              $terms->map('ID','Name'), // allowed terms
              $this->owner->EmailTags(), // current terms
              'Name' // title field: TaxonomyTerm.Name
            )->setCanCreate(true)
            ->setShouldLazyLoad(true)
            ->setIsMultiple(true)
            ->setSourceList( $terms ) // limit source list to this
            ->setLazyLoadItemLimit(null)
            ->setDescription( $description )
        );

    }

}
