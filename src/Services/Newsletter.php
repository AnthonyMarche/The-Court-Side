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

    /**
     *  Ajoute une adresse email à l'audience (liste mailing) créée sur Mailchimp
     * @param string $newEmailAddress
     * @return void
     */
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

    /**
     * Vérifie si un contact est inscrit à la liste de diffusion mail ou non (subscribed/unsuscribed)
     * @param string $emailAddressToCheck
     * @return mixed
     */
    public function checkContactsStatus(string $emailAddressToCheck)
    {
        $apiClient = $this->connectToAPI();
        $listId = 'e8fe276f15';
        $subscriberHash = md5(strtolower($emailAddressToCheck));
        return $apiClient->lists->getListMember($listId, $subscriberHash);
    }

    /**
     * Met à jour le statut d'inscription d'un contact sur la liste de diffusion.
     * Choix possibles = "subscribed" ou "unsubscribed"
     * @param string $emailAddressToRemove
     * @param string $update
     * @return void
     */
    public function updateContactsSubscription(string $emailAddressToRemove, string $update): void
    {
        $apiClient = $this->connectToAPI();
        $subscriberHash = md5(strtolower($emailAddressToRemove));
        $apiClient->lists->updateListMember("e8fe276f15", $subscriberHash, ["status" => $update]);
    }
}
