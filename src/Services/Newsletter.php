<?php

namespace App\Services;

use MailchimpMarketing\ApiClient;

class Newsletter
{
    private ApiClient $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Connexion à l'API Mailchimp
     * @return ApiClient
     */
    public function connectToAPI(): ApiClient
    {
        $mailchimp = $this->apiClient;

        // définit la config par défaut
        $mailchimp->setConfig([
            'apiKey' => '88f69b9576b7bd4c3055c01f1b504600-us13',
            'server' => 'us13'
        ]);

        // renvoie le statut de la connexion (pour checker si elle est OK ou DOWN)
        // $response = $response->ping->get();

        return $mailchimp;
    }

    public function addEmailToAudience(string $newEmailAddress): void
    {
        $apiClient = $this->connectToAPI();
        // ID de l'audience créée sur Mailchimp
        $listId = 'e8fe276f15';
        $apiClient->lists->addListMember($listId, [
            "email_address" => $newEmailAddress,
            "status" => "subscribed",
        ]);
    }
}
