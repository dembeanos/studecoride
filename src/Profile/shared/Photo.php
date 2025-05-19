<?php

declare(strict_types=1);

require_once __DIR__ . '/../../Database/Database.php';
require_once __DIR__ . '/../../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class Photo
{

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
    private function setPhoto($photo)
    {
        //Verifictaion de l'existance de la photo
        if (isset($photo) && $photo['error'] === UPLOAD_ERR_OK) {
            //Definition d'une variable des format autorisé facilement étendable ici
            $allowedExtension = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
            //Comparaison du type de l'image avec le tableau d'extensions autorisées
            if (!in_array($photo['type'], $allowedExtension)) {
                $this->sendUserError('Le fichier doit être une image au format Jpeg, Png, Gif ou Svg.', 'updatePhoto');
            }
            //Détermination du poid de l'image par calcul de sa taille.
            $maxSize = 5 * 1024 * 1024;
            //Comparaison du poid de l'image recu avec le poid maximum autorisé
            if ($photo['size'] > $maxSize) {
                $this->sendUserError('Le fichier est trop volumineux. La taille maximale autorisée est de 5 Mo', 'updatePhoto');
            }
            $imageSize = getimagesize($photo['tmp_name']);
            if ($imageSize === false) {
                $this->sendUserError('Le fichier n\'est pas une image valide.', 'updatePhoto');
            }
            //Verification du nom de fichier et remplacement des caractères non autorisés
            $fileName = $photo['name'];
            $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);

            //Affectation du fichier Node.js pour chargement de clamav
            $scanFilePath = __DIR__ . '/../../../assets/js/api/clamav/scanFiles.js';

            //Lancement du scan antivirus de l'image recu
            $command = 'node ' . escapeshellarg($scanFilePath) . ' ' . escapeshellarg($photo['tmp_name']);
            error_log("CLAMAV CMD: $command");
            $clamavScan = shell_exec($command . ' 2>&1');   // redirige stderr vers stdout
            error_log("CLAMAV OUT: " . var_export($clamavScan, true));
            //Verification du résultat si virus trouvé information utilisateurs fichier rejeté
            if (preg_match('/FOUND$/m', $clamavScan)) {
                return $this->sendPopup('Le fichier contient un virus et ne peut pas être enregistré.');
            }

            //Si tout c'est bien passé affectation de la photo à la variable definitive
            $this->photo = $fileName = file_get_contents($photo['tmp_name']);
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

    public function getUserPhoto()
    {
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
            $statement->bindValue(':photo', $this->getPhoto(), PDO::PARAM_LOB);
            $statement->execute();

            return $this->sendUserSuccess('Votre photo a été mise à jour avec succès.', 'updatePhoto');
        } catch (PDOException $e) {
            return $this->saveLog("Erreur update photo: " . $e->getMessage(), "ID: $id", 'CRITICAL');
        }
    }
}
