<?php
require_once __DIR__ . '/../../src/Authentification/auth.php';
require_once __DIR__ . '/../../src/Database/Database.php';
require_once __DIR__ . '/../../src/Authentification/Login.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $login = new Login($pdo);
        $login->setEmail($email);
        $login->setPassword($password);
        $login->execute();
        
    } catch (Exception $e) {
        // Stockage du message dans la session
        $_SESSION['error_message'] = $e->getMessage();
        // Redirection pour affichage
        header('Location: connexion.php');
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/assets/css/connexion.css">
    <title>Espace Connexion</title>
</head>
<body>
    <header>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'; ?>
        <h1>Espace Connexion</h1>
    </header>
    
    <!-- Afficher le message d'erreur s'il y en a un -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div style="color: red; text-align:center">
            <?php 
                echo htmlspecialchars($_SESSION['error_message']); 
                // Supprimer le message après l'affichage pour ne pas le montrer 
                //Cela permet aussi de le supprimer si erreurs consécutives
                unset($_SESSION['error_message']);
                ?>
        </div>
        <?php endif; ?>
        
    <form action="connexion.php" method="POST">
        <div>
            <h2>Vous Connecter</h2>
        </div>
        <section>
            <div>
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" placeholder="E-mail..." required>
            </div>
            <div>
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" aria-label="E-mail (obligatoire)" name="password" placeholder="Mot de passe..." required>
            </div>
        </section>
        <div>
            <button class="btn-primary" type="submit" name="send" id="send">Se Connecter</button>
        </div>
        <div>
            <a href="/pages/subscribe/subscribtion.php"> Vous n'avez pas encore de compte ? Inscrivez-vous ici</a>
        </div>
    </form>
   
    <footer>
        <?php include __DIR__. '/../../includes/footer.php'; ?>
    </footer>
</body>
</html>
