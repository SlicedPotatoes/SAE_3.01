<?php
    ini_set('display_startup_errors', 1);
    // Si non authentifier -> LoginPage

    // Sinon, le code ci-dessous

    $route = [
        "dashboard" => "./View/Dashboard/dashboard.php"
    ];
    $title = [
        "dashboard" => "Tableau de bord"
    ];

    $currPage = "dashboard";

    if(isset($_GET['currPage'])) {
        $currPage = $_GET['currPage'];
    }
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>
            <?php
                if(array_key_exists($currPage, $title)) {
                    echo $title[$currPage];
                }
                else {
                    echo "404 Not Found";
                }
            ?>
        </title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="style.css">
    </head>

    <body class="bg-light">
        <!-- Bouton pour avoir le dropdown menu pour modifier les informations profiles, nous pourrons ajouter d'autres fonctionnalité plus tard -->
        <div class="d-flex justify-content-end p-3 position-relative">
            <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-gear"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="mdp.php">Modifier le mot de passe</a></li>
                    <li><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#logoutModal">
                            Déconnexion
                        </button></li>
                </ul>
            </div>
        </div>

        <!-- Contenue de la page -->
        <div class="container mt-4">
            <!-- Message de bienvenu -->
            <h1 class="h3">Bonjour <span class="text-uphf fw-bold">Kévin Masmélove</span> !</h1>
            <div class="header-line-brand-color"></div>

            <!-- Étudiant : Card avec information sur son assiduité -->
            <div class="row row-cols-2 row-cols-md-4 g-3" style="margin-bottom: 15px;">
                <div class="col">
                    <div class="card shadow-sm border-primary text-center h-100 card-compact">
                        <div class="card-body">
                            <div class="card-title small mb-1">Absences totales</div>
                            <div class="fs-4 text-primary mb-0">10</div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card shadow-sm border-warning text-center h-100 card-compact">
                        <div class="card-body">
                            <div class="card-title small mb-1">En attente</div>
                            <div class="fs-4 text-warning mb-0">1</div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card shadow-sm border-success text-center h-100 card-compact">
                        <div class="card-body">
                            <div class="card-title small mb-1">Justifiées</div>
                            <div class="fs-4 text-success mb-0">3</div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card shadow-sm border-danger text-center h-100 card-compact">
                        <div class="card-body">
                            <div class="card-title small mb-1">Refusées</div>
                            <div class="fs-4 text-danger mb-0">6</div>
                            <div class="text-muted small">Malus -0.6</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card p-3">
                <?php
                    if(array_key_exists($currPage, $route)) {
                        require $route[$currPage];
                    }
                    else {
                        require "./View/404.html";
                    }
                ?>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>