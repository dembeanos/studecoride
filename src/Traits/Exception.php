<?php

declare(strict_types=1);

require_once __DIR__ .'/../Database/DatabaseNoSql.php';
// Trait dédié à la gestion de l'envoi de messages système (notifications, alertes, logs internes, etc.).
// Ces méthodes remplacent les exceptions PHP standards afin de personnaliser les messages d'erreurs.
// Elles améliorent la sécurité en évitant la divulgation d'informations sensibles.


trait ExceptionTrait
{

    private $mongo;

    // Établit une connexion à la base MongoDB si elle n'existe pas déjà
    private function connectMongoException()
    {
        if (!$this->mongo) {
            $db = new DatabaseNoSql();
            $this->mongo = $db->getDatabase();
        }
    }

    // Retourne une erreur utilisateur liée à un champ spécifique (ex. : input invalide)
    public function sendUserError(string $message, ?string $target = null): array
    {
        return [
            'type' => 'user_error',
            'message' => $message,
            'target' => $target
        ];
    }

    // Retourne un message de succès utilisateur (ex. : action validée via un bouton)
    public function sendUserSuccess(string $message, ?string $target = null): array
    {
        return [
            'type' => 'user_success',
            'message' => $message,
            'target' => $target
        ];
    }

    // Envoie un message destiné à la console développeur pour faciliter le debug
    public function sendToDev(string $message): array
    {
        return [
            'type' => 'dev',
            'message' => $message
        ];
    }

    // Envoie une notification de type "popup" à l'utilisateur pour des événements importants
    public function sendPopup(string $message): array
    {
        return [
            'type' => 'popup',
            'message' => $message
        ];
    }

    // Enregistre un message de log dans MongoDB avec un niveau de gravité
    // Utilisé pour tracer les erreurs système critiques, consultables par l’administrateur
    public function saveLog(string $message, string $logLevel = 'INFO'): bool
    {
        try {

            $this->connectMongoException();

            $log = [
                'timestamp' => date('Y-m-d H:i:s'),
                'message' => $message,
                'logLevel' => $logLevel
            ];

            $this->mongo->logs->insertOne($log);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
