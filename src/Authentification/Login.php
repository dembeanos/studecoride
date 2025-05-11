<?php

declare(strict_types=1);

require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../Authentification/Session.php';
require_once __DIR__ . '/../Traits/Exception.php';

$db = new Database();
$pdo = $db->getPdo();

final class Login
{
    use ExceptionTrait;

    private string $email;
    private string $password;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->email = '';
        $this->password = '';
    }

    //J'ai préféré ici ne pas utiliser de js
    //pour le login, j'ai souhaité que PHP le traite directement. De ce fait, j'ai créé une 'ancre' sur le code login
    //Ici je crée une méthode qui va cibler l'endroit où les messages seront affichés.
    //Pour cela j'utilise la superglobal SESSION

    //Gestion des erreurs en PHP:

    private function sendSessionError(string $message): void
    {
        $_SESSION['error_message'] = $message;
    }


    //Setters
    public function setEmail(string $email): void
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
        } else {
            $this->sendSessionError("Format d'email invalide.");
            return;
        }
    }

    public function setPassword(string $password): void
    {
        if (strlen($password) < 8) {
            $this->sendSessionError("Le mot de passe doit contenir au moins 8 caractères.");
            return;
        }
        $this->password = trim($password);
    }

    //
    public function execute(): void
    {
        if (empty($this->email) || empty($this->password)) {
            $this->sendSessionError("Email ou mot de passe manquant.");
            return;
        }
        $this->authenticate();
    }

    private function authenticate()
    {

        //Vérification d'une concordence dans la base avec l'email renseigné. 
        //Si oui, récupération du mot de passe et du usertype
        $query = " SELECT  loginid,  usertype, password, status
                    FROM logins WHERE email = :email ";

        $statement = $this->pdo->prepare($query);
        $statement->execute(['email' => $this->email]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        //Vérification que le mot de passe correspond
        if ($result && password_verify($this->password, $result['password'])) {

            //Vérification que l'utilisateur n'est pas banni
            if ($result['status'] === 'banned') {
                return $this->sendSessionError('Votre compte a été banni');
            }

            //Appel de la classe Session que je n'ai pas extendu. Chaque classe a sa responsabilité 
            //Je renseigne une valeur globale à la session loginId
            //Elle sera utilisée pour la messagerie
            Session::setLoginId((int) $result['loginid']);
            $role = $result['usertype'];

            //switch selon le type d'user chacune vers les bonnes fonctions.
            switch ($role) {
                case 'admin':
                    $this->redirectAdmin($result['loginid']);
                    break;
                case 'employe':
                    $this->redirectEmployee($result['loginid']);
                    break;
                case 'user':
                    $this->redirectUser($result['loginid']);
                    break;
                default:
                    throw new Exception("Rôle inconnu.");
            }
        } else {
            return $this->sendSessionError("Identifiants incorrects.");
        }
    }

    /* Les fonctions ci-dessous sont celles appelées par le switch:
    -Elles récupèrent l'id de l'utilisateur dans la table où il est hébergé
    -Elles redirigent vers la bonne page
    -Elles donnent l'ordre à Session de donner une valeur SESSION['employeId] ou ['adminId] ou ['userId]
*/
    private function redirectAdmin(int $loginId): void
    {
        $query = "SELECT adminid AS adminId FROM admins WHERE idlogin = :loginId";
        $statement = $this->pdo->prepare($query);
        $statement->execute(['loginId' => $loginId]);
        $admin = $statement->fetchColumn();

        if ($admin !== false) {
            Session::setAdminId((int)$admin);
            header('Location: /Ecoride/pages/admin/admin.php');
            exit();
        } else {
            $this->saveLog("Echec de la connexion erreur Login Admin non trouvé(redirectAdmin).", 'CRITICAL');
        }
    }

    private function redirectEmployee(int $loginId): void
    {
        $query = "SELECT employeid AS employeId FROM employees WHERE idlogin = :loginId";
        $statement = $this->pdo->prepare($query);
        $statement->execute(['loginId' => $loginId]);
        $employee = $statement->fetchColumn();

        if ($employee !== false) {
            Session::setEmployeId((int)$employee);
            header('Location: /Ecoride/pages/employe/employe.php');
            exit();
        } else {
            $this->saveLog("Echec de la connexion erreur Login employé non trouvé(redirectEmployee).", 'CRITICAL');
        }
    }

    private function redirectUser(int $loginId): void
    {
        try {

            $query = "SELECT userid AS userId FROM users WHERE idlogin = :loginId";
            $statement = $this->pdo->prepare($query);
            $statement->execute(['loginId' => $loginId]);

            $userId = $statement->fetchColumn();
            if ($userId !== false) {
                Session::setUserId((int)$userId);
                header('Location: /Ecoride/index.php');
                exit();
            } else {
                $this->saveLog("Echec de la connexion erreur Login user non trouvé(redirectUser).", 'CRITICAL');
            }
        } catch (Exception $e) {
            $this->saveLog("Erreur Login : " . $e->getMessage(), 'CRITICAL');
        }
    }
}
