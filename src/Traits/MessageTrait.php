<?php

declare(strict_types=1);

require_once __DIR__ .'/../Database/DatabaseNoSql.php';

// Trait dédié à la gestion de l'envoi de mail système

trait MessageTrait {

    private $mongo;
    private $date;


    private function connectMongoTrait() {
        if (!$this->mongo) {
            $db = new DatabaseNoSql();
            $this->mongo = $db->getDatabase();
        }
    }

    public function systemMessage($receiverId, $object, $messageText){

        $this->connectMongoTrait();

        $message = [
            'senderId' => 'System',
            'object'=> $object,
            'receiverId' => $receiverId,
            'messageText' => $messageText,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        try {
            $this->mongo->messages->insertOne($message);
            echo "Message envoyé avec succès!";
        } catch (Exception $e) {
            echo "Erreur MongoDB: " . $e->getMessage();
        }

    }

}
?>