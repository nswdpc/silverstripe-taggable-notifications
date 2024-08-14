<?php

namespace NSWDPC\Messaging\Taggable\Tests;

use NSWDPC\Messaging\Taggable\ProjectTags;
use NSWDPC\Messaging\Taggable\TaggableEmail;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;

/**
 * Test TaggableEmail notification tag setting and processing
 */
class TaggableEmailTest extends SapphireTest
{
    public function testSetNotificationTags()
    {
        $headerName = 'X-Tag-Testing';

        ProjectTags::config()->set('tag_limit', 0);// unlimited tags
        ProjectTags::config()->set('tag_email_header_name', $headerName);

        $from = "from@example.com";
        $to = "to@example.com";
        $subject = "test set notification tags";
        $body = "<p>Email body<p>";

        $email = TaggableEmail::create(
            $from,
            $to,
            $subject,
            $body
        );

        $tags = [
            'tag one',
            'tag two',
            'tag three',
            'tag four'
        ];

        $email = $email->setNotificationTags($tags);

        $storedTags = $email->getNotificationTags();

        $this->assertEquals($tags, $storedTags);

        $headers = $email->getSwiftMessage()->getHeaders();

        $this->assertTrue($headers->has($headerName));
        $headerSet = $headers->getAll($headerName);

        $this->assertEquals(count($tags), count($headerSet));

        foreach ($headerSet as $header) {
            $value = $header->getValue();
            $this->assertContains($value, $tags);
        }
    }

    public function testTagLimit()
    {
        $headerName = 'X-Tag-Testing';
        $tagLimit = 2;

        ProjectTags::config()->set('tag_limit', $tagLimit);// unlimited tags
        ProjectTags::config()->set('tag_email_header_name', $headerName);

        $from = "from@example.com";
        $to = "to@example.com";
        $subject = "test set notification tags";
        $body = "<p>Email body<p>";

        $email = TaggableEmail::create(
            $from,
            $to,
            $subject,
            $body
        );

        $tags = [
            'tag one',
            'tag two',
            'tag three',
            'tag four'
        ];

        $email = $email->setNotificationTags($tags);

        $storedTags = $email->getNotificationTags();

        $this->assertEquals($tagLimit, count($storedTags));

        $headers = $email->getSwiftMessage()->getHeaders();

        $this->assertTrue($headers->has($headerName));
        $headerSet = $headers->getAll($headerName);

        $this->assertEquals(count($storedTags), count($headerSet));

        foreach ($headerSet as $header) {
            $value = $header->getValue();
            $this->assertContains($value, $storedTags);
        }
    }

    public function testJsonSerialisedTags()
    {
        $headerName = 'X-Tag-Testing';


        ProjectTags::config()->set('tag_email_header_serialisation', ProjectTags::HEADER_SERIALISATION_JSON);
        ProjectTags::config()->set('tag_limit', 0);// unlimited tags
        ProjectTags::config()->set('tag_email_header_name', $headerName);

        $from = "from@example.com";
        $to = "to@example.com";
        $subject = "test set notification tags";
        $body = "<p>Email body<p>";

        $email = TaggableEmail::create(
            $from,
            $to,
            $subject,
            $body
        );

        $tags = [
            'tag one',
            'tag two',
            'tag three',
            'tag four'
        ];

        $email = $email->setNotificationTags($tags);

        $storedTags = $email->getNotificationTags();

        $this->assertEquals($tags, $storedTags);

        $headers = $email->getSwiftMessage()->getHeaders();

        $this->assertTrue($headers->has($headerName));
        $header = $headers->get($headerName);

        $this->assertEquals(count($tags), count(json_decode($header->getValue())));
    }

    public function testCsvSerialisedTags()
    {
        $headerName = 'X-Tag-Testing';


        ProjectTags::config()->set('tag_email_header_serialisation', ProjectTags::HEADER_SERIALISATION_CSV);
        ProjectTags::config()->set('tag_limit', 0);// unlimited tags
        ProjectTags::config()->set('tag_email_header_name', $headerName);

        $from = "from@example.com";
        $to = "to@example.com";
        $subject = "test set notification tags";
        $body = "<p>Email body<p>";

        $email = TaggableEmail::create(
            $from,
            $to,
            $subject,
            $body
        );

        $tags = [
            'tag one',
            'tag two',
            'tag three',
            'tag four'
        ];

        $email = $email->setNotificationTags($tags);

        $storedTags = $email->getNotificationTags();

        $this->assertEquals($tags, $storedTags);

        $headers = $email->getSwiftMessage()->getHeaders();

        $this->assertTrue($headers->has($headerName));
        $header = $headers->get($headerName);

        $this->assertEquals(count($tags), count(explode(",", $header->getValue())));
    }

    public function testProjectNotificationTags()
    {
        $headerName = 'X-Tag-Testing';
        $projectTag = 'test project tag';

        ProjectTags::config()->set('tag_limit', 0);// unlimited tags
        ProjectTags::config()->set('tag_email_header_name', $headerName);
        ProjectTags::config()->set('tag', $projectTag);// unlimited tags

        $from = "from@example.com";
        $to = "to@example.com";
        $subject = "test set notification tags";
        $body = "<p>Email body<p>";

        $email = TaggableEmail::create(
            $from,
            $to,
            $subject,
            $body
        );

        $tags = [
            'tag one',
            'tag two',
            'tag three',
            'tag four'
        ];

        $allExpectedTags = array_merge([ $projectTag ], $tags);

        $email = $email->setNotificationTags($tags);

        $storedTags = $email->getNotificationTags();

        $this->assertEquals($allExpectedTags, $storedTags);

        $headers = $email->getSwiftMessage()->getHeaders();

        $this->assertTrue($headers->has($headerName));
        $headerSet = $headers->getAll($headerName);

        // tags + project tag
        $this->assertEquals(count($allExpectedTags), count($headerSet));

        foreach ($headerSet as $header) {
            $value = $header->getValue();
            $this->assertContains($value, $allExpectedTags);
        }
    }
}
