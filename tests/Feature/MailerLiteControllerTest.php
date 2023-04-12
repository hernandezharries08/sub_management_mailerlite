<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use MailerLite\MailerLite;

class MailerLiteControllerTest extends TestCase
{
    public function testGetSubscribers()
    {
        // Initialize MailerLite API with API key
        $mailerLite = new MailerLite(['api_key' => env('MAILERLITE_API_KEY')]);

        // Call MailerLite API to get subscribers
        $response = $mailerLite->subscribers->get();

        $this->assertNotEmpty($response['body']['data']);
    }

    public function testSubscriberCRUD()
    {
        // Initialize MailerLite API with API key
        $mailerLite = new MailerLite(['api_key' => env('MAILERLITE_API_KEY')]);

        // Create a new subscriber
        $subscriber = $mailerLite->subscribers->create([
            'email' => 'harries@exampletesting.com',
            'fields' => [
                'name' => 'Harries',
                'last_name' => 'Hernandez',
                'country' => 'Philippines',
            ],
        ]);

        $subscriber = $mailerLite->subscribers->find('harries@exampletesting.com');

        $this->assertNotEmpty($subscriber["body"]["data"]);

        // Update the subscriber
        $updatedSubscriber = $mailerLite->subscribers->update($subscriber["body"]["data"]['id'], [
            'fields' => [
                'country' => 'Canada',
            ],
        ]);
        $this->assertEquals('Canada', $updatedSubscriber["body"]["data"]['fields']['country']);

        // Delete the subscriber
        $response = $mailerLite->subscribers->delete($subscriber["body"]["data"]['id']);
        
    }

}
