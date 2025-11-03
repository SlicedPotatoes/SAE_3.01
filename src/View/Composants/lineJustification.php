<?php
/**
 * Front d'une ligne d'un justificatif dans le dashboard.
 */

// Récupération des infos de la justification via researchFile.php ou via la BDD
$fileName = isset($fileName) ? $fileName : null;
$info = null;

// Si l'objet $justification est fourni (cas normal dans le dashboard), on récupère les infos depuis la BD
if (isset($justification) && is_object($justification) && method_exists($justification, 'getIdJustification')) {
    // Tentative d'utilisation de la connexion existante
    try {
        // include connection si nécessaire
        if (!isset($connection) || !$connection) {
            // la connection est généralement dans src/Model/Connection.php
            $connPath = __DIR__ . '/../../Model/Connection.php';
            if (file_exists($connPath)) {
                require_once $connPath;
            }
        }
        if (isset($connection) && $connection instanceof PDO) {
            $pdo = $connection;
            $id = $justification->getIdJustification();

            // Récupérer la justification
            $stmt = $pdo->prepare('SELECT cause, startDate, endDate FROM Justification WHERE idJustification = ?');
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                // Récupérer les fichiers liés
                $stmt = $pdo->prepare('SELECT fileName FROM File WHERE idJustification = ?');
                $stmt->execute([$id]);
                $files = $stmt->fetchAll(PDO::FETCH_COLUMN);

                // Gérer les noms de colonnes venant de PostgreSQL (tout en minuscules)
                $causeVal = $row['cause'] ?? $row['Cause'] ?? '';
                $startVal = $row['startDate'] ?? $row['startdate'] ?? $row['start_date'] ?? '';
                $endVal = $row['endDate'] ?? $row['enddate'] ?? $row['end_date'] ?? '';

                $info = [
                        'motif' => $causeVal,
                        'date_debut' => $startVal,
                        'date_fin' => $endVal,
                        'fichiers' => $files
                ];
            }
        }
    } catch (Exception $e) {
        // ne pas afficher d'erreur fatale dans la vue, on laisse $info = null
        $info = null;
    }
}

// Si on n'a toujours pas d'info mais qu'on a le nom de fichier, essayer d'appeler researchFile.php (info=1)
if (!$info && $fileName) {
    // Construire une URL absolute si possible
    $infoJson = null;
    if (!empty($_SERVER['HTTP_HOST'])) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $url = $scheme . '://' . $host . '/aImplémenter/systemeDeFichier/researchFile.php?name=' . urlencode($fileName) . '&info=1';
        $infoJson = @file_get_contents($url);
    }
    // fallback: essayer via path depuis document root (existant dans l'ancien code)
    if (!$infoJson) {
        $localUrl = '/aImplémenter/systemeDeFichier/researchFile.php?name=' . urlencode($fileName) . '&info=1';
        $path = lineJustification . phprtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . $localUrl;
        $infoJson = @file_get_contents($path);
    }
    if ($infoJson) {
        $tmp = json_decode($infoJson, true);
        if (is_array($tmp)) {
            $info = $tmp;
        }
    }
}
?>
<div class="accordion-item border-bottom">
    <div class="accordion-header">
        <?php if (isset($justification) && is_object($justification) && method_exists($justification, 'getIdJustification')): ?>
            <a href="?currPage=detailsJustification&id=<?= $justification->getIdJustification(); ?>"
               class="text-decoration-none">
                <button class="accordion-button collapsed d-flex align-items-center gap-3 p-3"
                        type="button"
                >
                    <div class="d-flex flex-column">
                        <div>Date de début: <?= $justification->getStartDate() instanceof DateTime ? $justification->getStartDate()->format('d/m/Y') : htmlspecialchars((string)$justification->getStartDate()); ?></div>
                        <div>Date de fin: <?= $justification->getEndDate() instanceof DateTime ? $justification->getEndDate()->format('d/m/Y') : htmlspecialchars((string)$justification->getEndDate()); ?></div>
                    </div>
                    <div class="d-flex align-items-center gap-3 flex-grow-1">
                        <span class='badge rounded-pill text-bg-<?= htmlspecialchars($justification->getCurrentState()->colorBadge()) ?>'><?= htmlspecialchars($justification->getCurrentState()->label()) ?></span>
                    </div>
                </button>
            </a>
        <?php else: ?>
            <div class="alert alert-danger">Erreur : justification non définie ou méthode manquante.</div>
        <?php endif; ?>
    </div>
</div>