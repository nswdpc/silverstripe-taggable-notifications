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
    public function testSetNotificationTags(): void
    {
        $headerName = 'X-Tag-Testing';

        ProjectTags::config()->set('tag_limit', 0);// unlimited tags
        ProjectTags::config()->set('tag_email_header_name', $headerName);
        ProjectTags::config()->set('tag_email_header_serialisation', ProjectTags::HEADER_SERIALISATION_MULTIHEADER);

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

        $headers = $email->getHeaders();

        $this->assertTrue($headers->has($headerName));
        $headerSet = $headers->all($headerName);

        $c = 0;
        foreach ($headerSet as $header) {
            $c++;
            $value = $header->getValue();
            $this->assertContains($value, $tags);
        }

        $this->assertEquals(count($tags), $c);
    }

    public function testTagLimit(): void
    {
        $headerName = 'X-Tag-Testing';
        $tagLimit = 2;

        ProjectTags::config()->set('tag_limit', $tagLimit);// with a limit of tags
        ProjectTags::config()->set('tag_email_header_name', $headerName);
        ProjectTags::config()->set('tag_email_header_serialisation', ProjectTags::HEADER_SERIALISATION_MULTIHEADER);

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

        $headers = $email->getHeaders();

        $this->assertTrue($headers->has($headerName));
        $headerSet = $headers->all($headerName);

        $c = 0;
        foreach ($headerSet as $header) {
            $c++;
            $value = $header->getValue();
            $this->assertContains($value, $storedTags);
        }
        $this->assertEquals($tagLimit, $c);
    }

    public function testJsonSerialisedTags(): void
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

        $headers = $email->getHeaders();

        $this->assertTrue($headers->has($headerName));

        /** @var \Symfony\Component\Mime\Header\UnstructuredHeader $header */
        $header = $headers->get($headerName);

        $this->assertEquals(count($tags), count(json_decode((string) $header->getValue())));
    }

    public function testCsvSerialisedTags(): void
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

        $headers = $email->getHeaders();

        $this->assertTrue($headers->has($headerName));

        /** @var \Symfony\Component\Mime\Header\UnstructuredHeader $header */
        $header = $headers->get($headerName);

        $this->assertEquals(count($tags), count(explode(",", (string) $header->getValue())));
    }

    public function testProjectNotificationTags(): void
    {
        $headerName = 'X-Tag-Testing';
        $projectTag = 'test project tag';

        ProjectTags::config()->set('tag_limit', 0);// unlimited tags
        ProjectTags::config()->set('tag_email_header_name', $headerName);
        ProjectTags::config()->set('tag', $projectTag);// set a project tag
        ProjectTags::config()->set('tag_email_header_serialisation', ProjectTags::HEADER_SERIALISATION_MULTIHEADER);

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

        $headers = $email->getHeaders();

        $this->assertTrue($headers->has($headerName));
        $headerSet = $headers->all($headerName);

        $c = 0;
        foreach ($headerSet as $header) {
            $c++;
            $value = $header->getValue();
            $this->assertContains($value, $allExpectedTags);
        }

        $this->assertEquals(count($allExpectedTags), $c);
    }

    public function testCsvSerialisedTagsWithDelimiterStripped(): void
    {
        $headerName = 'X-Tag-Testing';
        $delimiter = ",";

        ProjectTags::config()->set('tag_email_header_serialisation', ProjectTags::HEADER_SERIALISATION_CSV);
        ProjectTags::config()->set('tag_limit', 0);// unlimited tags
        ProjectTags::config()->set('tag_email_header_name', $headerName);
        ProjectTags::config()->set('tag_email_header_value_delimiter', $delimiter);

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
            "tag {$delimiter}one",
            "tag {$delimiter}two",
            "tag {$delimiter}three",
            "tag {$delimiter}four",
        ];

        $email = $email->setNotificationTags($tags);

        $storedTags = $email->getNotificationTags();

        $this->assertEquals($tags, $storedTags);

        $headers = $email->getHeaders();

        $this->assertTrue($headers->has($headerName));
        /** @var \Symfony\Component\Mime\Header\UnstructuredHeader $header */
        $header = $headers->get($headerName);

        $this->assertEquals(count($tags), count(explode(",", (string) $header->getValue())));
    }
}
