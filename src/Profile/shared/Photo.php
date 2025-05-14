<?php
declare(strict_types=1);

require_once __DIR__ .'/../../Database/Database.php';
require_once __DIR__ .'/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class Photo {

    use ExceptionTrait;

    private $pdo;
    private $userId;
    private $adminId;
    private $employeId;
    private $photo;

    public function __construct($pdo, $userId = null, $adminId = null, $employeId = null)
    {
        $this->pdo = $pdo;
        $this->userId = $userId;
        $this->adminId = $adminId;
        $this->employeId = $employeId;
    }

    //Setters
private function setPhoto($photo) {
    // Vérification de l'existence de la photo
    if (isset($photo) && $photo['error'] === UPLOAD_ERR_OK) {
        // Définition des formats autorisés
        $allowedExtension = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
        
        // Vérification du type d'image
        if (!in_array($photo['type'], $allowedExtension)) {
            $this->sendUserError('Le fichier doit être une image au format Jpeg, Png, Gif ou Svg.', 'updatePhoto');
        }
        
        // Vérification du poids de l'image
        $maxSize = 5 * 1024 * 1024;
        if ($photo['size'] > $maxSize) {
            $this->sendUserError('Le fichier est trop volumineux. La taille maximale autorisée est de 5 Mo', 'updatePhoto');
        }
        
        // Vérification du fichier image
        $imageSize = getimagesize($photo['tmp_name']);
        if ($imageSize === false) {
            $this->sendUserError('Le fichier n\'est pas une image valide.', 'updatePhoto');
        }
        
        // Nettoyage du nom du fichier
        $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '', $photo['name']);

        // Définition du chemin pour le scan antivirus
        $scanFilePath = __DIR__ . '../../../node_modules/scanFiles.js';

        // Lancement du scan antivirus
        $clamavCommand = 'node ' . escapeshellarg($scanFilePath) . ' ' . escapeshellarg($photo['tmp_name']);
        $clamavScan = shell_exec($clamavCommand);

        // Vérification si le scan a trouvé un virus
        if ($clamavScan === null) {
            // Si shell_exec échoue, renvoie une erreur
            return $this->sendPopup('Erreur lors du scan antivirus.');
        }

        if (strpos($clamavScan, 'FOUND') !== false) {
            return $this->sendPopup('Le fichier contient un virus et ne peut pas être enregistré.');
        }

        // Si tout est valide, affectation de la photo
        if (file_exists($photo['tmp_name'])) {
            $this->photo = file_get_contents($photo['tmp_name']);
        } else {
            return $this->sendUserError('Le fichier photo est invalide.', 'updatePhoto');
        }
    }
}


    //Getters

    private function getPdo(){ return $this->pdo;}
    private function getPhoto(){ return $this->photo;}

    //Getter qui selectionne la bonne table

    private function getTargetTableAndColumn()
    {
        if ($this->adminId !== null) {
            return ['admins', 'adminid', $this->adminId];
        } elseif ($this->employeId !== null) {
            return ['employees', 'employeid', $this->employeId];
        } elseif ($this->userId !== null) {
            return ['users', 'userid', $this->userId];
        } else {
            $this->sendToDev("Aucun identifiant utilisateur défini");
            return null;
        }
    }

    //Fonctions Principales

    public function getUserPhoto() {
        list($table, $column, $id) = $this->getTargetTableAndColumn();
    
        $query = "SELECT photo FROM $table WHERE $column = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
    
        try {
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
    
            // On récupère la photo et on la retourne en base64
            $photoContent = stream_get_contents($result['photo']);
            //et on envoir encodé 
            return 'data:image/jpeg;base64,' . base64_encode($photoContent);
    
        } catch (PDOException $e) {
            return $this->saveLog('Erreur photo: ' . $e->getMessage(), "ID: $id", 'ERROR');
        }
    }


    public function updatePhoto($photo)
    {
        try {
            //Appel du setter pour verification
            if ($error = $this->setPhoto($photo)) return $error;

            list($table, $column, $id) = $this->getTargetTableAndColumn();
            //Mise a jour dans la base
            $query = "UPDATE $table SET photo = :photo WHERE $column = :id";
            $statement = $this->pdo->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':photo', $this->photo, PDO::PARAM_LOB);
            $statement->execute();

            return $this->sendUserSuccess('Votre photo a été mise à jour avec succès.', 'updatePhoto');
        } catch (PDOException $e) {
            return $this->saveLog("Erreur update photo: " . $e->getMessage(), "ID: $id", 'CRITICAL');
        }
    }
}
