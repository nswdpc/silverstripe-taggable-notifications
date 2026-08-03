<?php

namespace NSWDPC\Messaging\Taggable\Tests;

use NSWDPC\Messaging\Taggable\ProjectTags;
use NSWDPC\Messaging\Taggable\TaggableEmail;
use NSWDPC\Messaging\Taggable\NotificationTags;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\UserForms\Control\UserDefinedFormController;
use SilverStripe\UserForms\Model\Recipient\EmailRecipient;
use SilverStripe\Taxonomy\TaxonomyTerm;
use SilverStripe\Taxonomy\TaxonomyType;

/**
 * Test userforms extensions
 */
class UserFormsTest extends SapphireTest
{

    protected $usesDatabase = true;


    public function testEmailRecipient(): void
    {
        $type = NotificationTags::findOrMakeType();
        $tags = ['tag1','tag2','tag3'];
        $terms = [];
        foreach($tags as $tag) {
            $terms[$tag] = TaxonomyTerm::create([
                'Name' => $tag,
                'TypeID' => $type->ID
            ]);
            $terms[$tag]->write();
        }
        $emailRecipient = EmailRecipient::create([
            'EmailAddress' => 'someone@example.com',
            'EmailSubject' => 'test email',
        ]);
        $emailRecipient->write();
        $emailRecipient->EmailTags()->add($terms['tag1']);
        $emailRecipient->EmailTags()->add($terms['tag3']);

        $this->assertEquals(2, $emailRecipient->EmailTags()->count());

        $genericTag = TaxonomyTerm::create([
            'Name' => 'generic1'
        ]);
        $genericTag->write();
        $emailRecipient->EmailTags()->add($genericTag);

        $tagsString = $emailRecipient->EmailTagsNice();

        $this->assertEquals("tag1, tag3", $tagsString);
    }

    public function testUserFormsTaggableEmail(): void
    {

        $type = NotificationTags::findOrMakeType();
        $tags = ['userform1','userform2','userform3'];
        $terms = [];
        foreach($tags as $tag) {
            $terms[$tag] = TaxonomyTerm::create([
                'Name' => $tag,
                'TypeID' => $type->ID
            ]);
            $terms[$tag]->write();
        }
        $emailRecipient = EmailRecipient::create([
            'EmailAddress' => 'userform@example.com',
            'EmailSubject' => 'test userform email recipient',
        ]);
        $emailRecipient->write();
        $emailRecipient->EmailTags()->add($terms['userform1']);
        $emailRecipient->EmailTags()->add($terms['userform3']);

        // tag should not be present in final email tags
        $genericTag = TaxonomyTerm::create([
            'Name' => 'generic1'
        ]);
        $genericTag->write();
        $emailRecipient->EmailTags()->add($genericTag);


        $email = TaggableEmail::create();
        $controller = UserDefinedFormController::create();

        $emailData = [];

        $controller->invokeWithExtensions('updateEmail', $email, $emailRecipient, $emailData);

        $notificationTags = $email->getNotificationTags();
        $this->assertEquals(['userform1','userform3'], $notificationTags);
    }
}
