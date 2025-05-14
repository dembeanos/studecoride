<?php
// Contrôle strict du mode
declare(strict_types=1);

// Définition d'une constante pour la durée maximale d'inactivité (en secondes)
const MAX_IDLE_TIME = 3600; // 1 heure d'inactivité

final class Session {
    
    
    public static function start(): void {
        // Vérifie si la session est déjà active
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        //Variable qui enregistre la derniere activité
        $_SESSION['last_activity'] = time();

        //si la durée d'inactivité est superieur au temps défini on déconnecte
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > MAX_IDLE_TIME) {
            
            self::destroy();
        }
    }

    private static function set(string $key, mixed $value): void {
        self::start();
        $_SESSION[$key] = $value; //ici on assigne notre valeur determiné par Login
    }

    private static function get(string $key): mixed {
        self::start();
        $value = $_SESSION[$key] ?? null;//ici on la récupère
        return $value;
    }


    // Definition des valeurs a Session
    public static function setLoginId(int $loginId): void {  self::set('loginId', $loginId); }
    public static function setRole(string $role): void { self::set('role', $role); }
    public static function setAdminId(int $adminId): void { self::set('adminId', $adminId); }
    public static function setEmployeId(int $employeId): void { self::set('employeId', $employeId); }
    public static function setUserId(int $userId): void { self::set('userId', $userId); }
    public static function setFirstName($userId): void { self::set('firstName', $userId); }

   //Recupération des valeurs dans Session
    public static function getLoginId(): ?int { return self::get('loginId'); }
    public static function getRole(): ?string { return self::get('role'); }
    public static function getAdminId(): ?int { return self::get('adminId'); }
    public static function getEmployeId(): ?int { return self::get('employeId'); }
    public static function getUserId(): ?int { return self::get('userId'); }
    public static function getFirstName($userId): void { self::get('firstName', $userId); }
   
   
    public static function destroy(): void
    {
        self::start(); // Démarre la session
    
        // Puis supprime toutes les variables de session
        session_unset();
    
        // Supression des cookies de connexion
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000, // Définie un délai d'expiration dans le passé pour supprimer le cookie
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
    
}
?>
