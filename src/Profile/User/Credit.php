<?php

declare(strict_types=1);

require_once __DIR__ .'/../../Database/Database.php';
require_once __DIR__ .'/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();



final class Credit {

    use ExceptionTrait;

    private $pdo;
    private $userId;
    private $date;
    private $label;
    private $credit;
    private $debit;
    private $totalCredit;

    public function __construct($pdo, $userId)
    {
        $this->userId = $userId;
        $this->pdo = $pdo;
    }

    //Getter

    private function getPdo(){ return $this->pdo; }
    private function getUserId(){ return $this->userId; }

    //Fonctions principales

    public function getUserCredit()
    {
        try {
            $query = 'SELECT DATE(creationdate) as creationdate, label, credit, debit FROM credits WHERE iduser = :userid';
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':userid', $this->getUserId(), PDO::PARAM_INT);
            $statement->execute();

            $creditInfo = $statement->fetchAll(PDO::FETCH_ASSOC);

            if ($creditInfo) {
                foreach ($creditInfo as &$credit) {
                    $credit = array_map('trim', $credit);
                }

                return [
                    'status' => 'success',
                    'data' => $creditInfo
                ];
            }
            return  $this->sendToDev('Utilisateur non trouvé dans credits');
        } catch (PDOException $e) {
            return $this->saveLog('Erreur lors de la récupération des crédits, Pour user:'.$this->getUserId() . $e->getMessage(),'ERROR');
        }
    }



    public function getUserTotalCredit()
    {
        try {
            $query = 'SELECT credit FROM users WHERE userid = :userid';

            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':userid', $this->getUserId(), PDO::PARAM_INT);

            $statement->execute();
            $creditInfo = $statement->fetch(PDO::FETCH_ASSOC);

            if ($creditInfo) {
                $creditInfo = array_map('trim', $creditInfo);

                return json_encode([
                    'status' => 'success',
                    'data' => $creditInfo
                ]);
            }
            return  $this->sendToDev('Utilisateur non trouvé ou aucun crédit enregistré');
        } catch (PDOException $e) {
            return $this->saveLog('Erreur lors de la récupération des crédits, Pour l\'user: '.$this->getUserId() . $e->getMessage(), 'ERROR');
        }
    }
}
