<?php
require_once __DIR__ . '/src/Authentification/auth.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Ecoride" />
    <meta property="og:description" content="Rejoignez EcoRide pour partager vos trajets et participer à une mobilité plus durable et solidaire." />
    <meta name="description" content="Plateforme de covoiturage nationale a but écologique, trouvez un itinéraire facilement et proche de chez vous" />
    <meta property="og:site_name" content="EcoRide" />
    <meta property="og:title" content="Ecoride : Votre solution de covoiturage écologique" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="http://www.ecoride.com/" />
    <meta property="og:locale" content="fr_FR" />
    <title>Ecoride : Votre solution de covoiturage écologique et pratique</title>
    <link rel="stylesheet" href="/assets/css/home.css">
    <link rel="canonical" href="http://www.ecoride.com/" />
</head>

<body>
    <header>
        <?php include 'includes/header.php'; ?>
        <h1>
            <?php 
            if (isUserConnected() || isAdminConnected() || isEmployeConnected()) {
                echo "Bienvenue " . $_SESSION['firstName'] . " chez Ecoride" . " !";
            } else {
                echo "Bienvenue Chez Ecoride !";
            }
        ?>
        </h1>
        <div aria-label="Presentation de l'entreprise" class="presentation">
            <p>EcoRide est la solution de covoiturage écologique et économique qui simplifie vos trajets.</p>
            <p>Trouvez ou proposez un trajet en quelques clics, partagez vos frais, réduisez vos émissions de CO₂.</p>
            <p>Voyagez en toute sécurité grâce à notre système de réservation fiable. </p>
            <p>Rejoignez une communauté engagée pour une mobilité plus durable et solidaire. </p>
            <p>EcoRide partageons la route, respectons la planète</p>
        </div>
        <section aria-label="Barre de Recherche" class="search-bar">
            <?php include_once 'includes/searchbar.php'; ?>
        </section>
    </header>

    <main>
        <section class="highlights">

            <aside aria-label="Section défilante listant les avantages du covoiturage" class="benefits">
                <h2>Les Avantages</h2>

                <div class="benefits">
                    <article>
                        <h3><img alt="Icone Economie" src="assets/images/accueil/benefits_icon/economy.svg">Economies Réelles</h3>
                        <p>Réduisez vos frais de transport...</p>
                    </article>
                    <article>
                        <h3><img alt="Icone Ecologie" src="assets/images/accueil/benefits_icon/ecology.svg">Un Impact Écologique Positif</h3>
                        <p>Diminuez vos émissions de CO2...</p>
                    </article>
                    <article>
                        <h3><img alt="Icone Temps" src="assets/images/accueil/benefits_icon/easySpeed.svg">Simplicité et Rapidité</h3>
                        <p>Trouvez ou proposez un trajet...</p>
                    </article>
                    <article>
                        <h3><img alt="Icone Convivialité" src="assets/images/accueil/benefits_icon/drink.svg">Flexibilité et Convivialité</h3>
                        <p>Des trajets adaptés à vos besoins...</p>
                    </article>
                    <article>
                        <h3><img alt="Icone de Gestion" src="assets/images/accueil/benefits_icon/gestion.svg">Gestion Transparente</h3>
                        <p>Accédez à votre historique de trajets...</p>
                    </article>
                    <article>
                        <h3><img alt="Icone Assistance" src="assets/images/accueil/benefits_icon/assist.svg">Confiance et Assistance</h3>
                        <p>Grâce à une équipe dédiée...</p>
                    </article>
                    <h4>EcoRide : Roulez moins cher, plus vert, et en toute simplicité !</h4>
                </div>

            </aside>

            <aside aria-label="Informations sur l'aide de L'Etat et promotion Ecoride pour toute inscription gratuite"class="Offer">

                <div class="government">
                    <img alt="Auto" src="assets/images/accueil/auto.png">

                    <div class="subvention-info">
                        <p>Depuis le 23 Janvier 2023, l'État offre 100€ de subvention aux covoitureurs! </p>
                        <p>Pour en savoir plus rendez vous
                            <a target="_blank" rel="noopener noreferrer" title="vers le site de l'Etat"
                                href="https://www.ecologie.gouv.fr/politiques-publiques/covoiturage-france-ses-avantages-reglementation-vigueur">
                                sur le site de l'État. 
                            </a>
                        </p>
                    </div>
                </div>

                <div class="deal-offre">
                    <img alt="Image 20 crédits offert" src="assets/images/accueil/money.png">
                    <p>Ecoride c'est aussi 20 Credit Offerts pour toutes inscriptions!</p>
                </div>

                <div class="invit">
                    <a title="Vers la page d'inscription" href="/Ecoride/pages/inscription.php">Rejoignez Nous !</a>
                </div>
            </aside>
        </section>

        <section aria-label=" Les Engagements Ecoride" class="engagement">
            <h2>Un Engagement Ecologique Fort</h2>
            <section class="ecology-engagement">
                <aside class="ecotext">
                    <h3>Chaque covoiturage sur EcoRide participe à :</h3>
                    <ul>
                        <li>La réduction des emissions de gaz a effet de serre.</li>
                        <li>La diminutions des embouteillages et de la pollution urbaine.</li>
                        <li>Une meilleure utilisation des ressources automobiles.</li>
                    </ul>
                    <h4>Roulons vers un avenir plus propre et plus responsable</h4>
                </aside>

                <aside>
                    <img alt="Main Tendue" src="assets/images/accueil/hand.png">
                </aside>
            </section>
        </section>

    </main>

    <footer>
        <?php include 'includes/footer.php'; ?>
    </footer>

</body>


</html>
