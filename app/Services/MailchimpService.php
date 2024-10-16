<?php

namespace App\Services;

use MailchimpMarketing\ApiClient;

class MailchimpService
{
    protected $client;

    public function __construct()
    {
        $this->client = new ApiClient();
        $this->client->setConfig([
            'apiKey' => config('mailchimp.api_key'),
            'server' => substr(config('mailchimp.api_key'), strpos(config('mailchimp.api_key'), '-') + 1)
        ]);
    }

    public function addSubscriber($email, $tags = [])
    {
        try {
            $this->client->lists->addListMember(config('mailchimp.audience_id'), [
                'email_address' => $email,
                'status' => 'subscribed',
                'tags' => $tags,
            ]);
        } catch (\Exception $e) {
            // Handle error (log it, notify admin, etc.)
            \Log::error('Mailchimp error: ' . $e->getMessage());
        }
    }
}
