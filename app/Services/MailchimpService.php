<?php

namespace Vanguard\Services;

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

    public function addSubscriber($email, $tags = [], $mergeFields = [])
    {
        try {
            $this->client->lists->addListMember(config('mailchimp.audience_id'), [
                'email_address' => $email,
                'status' => 'subscribed',
                'tags' => $tags,
                'merge_fields'  => $mergeFields
            ]);
        } catch (\Exception $e) {

            dd($e);
            // Handle error (log it, notify admin, etc.)
            \Log::error('Mailchimp error: ' . $e->getMessage());
        }
    }
}
