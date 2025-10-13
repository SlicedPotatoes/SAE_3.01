php
<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Assure-toi que FPDF est bien installé

class UploadFileTest
{
    public function run()
    {
        // Création d'un vrai PDF temporaire avec FPDF
        $tmpFile = tempnam(sys_get_temp_dir(), "test");
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(40, 10, 'Test PDF');
        $pdf->Output('F', $tmpFile);

        // Fonction alternative pour le type MIME
        function getMimeType($filename) {
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if ($ext === 'pdf') return 'application/pdf';
            return 'application/octet-stream';
        }

        $_FILES = [
            'files' => [
                'name' => ['test.pdf'],
                'type' => ['application/pdf'],
                'tmp_name' => [$tmpFile],
                'error' => [0],
                'size' => [filesize($tmpFile)]
            ]
        ];

        $_POST['absenceReason'] = 'test';
        $_POST['startDate'] = '2024-06-01';
        $_POST['endDate'] = '2024-06-02';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $uploadDir = 'C:/upload';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadPath = __DIR__ . '/../src/Presentation/upload.php';
        ob_start();
        if (file_exists($uploadPath)) {
            include $uploadPath;
            $output = ob_get_clean();
        } else {
            ob_end_clean();
            echo "Erreur : upload.php introuvable à $uploadPath\n";
            return;
        }

        // AFFICHAGE APRÈS l'inclusion
        echo "Type MIME détecté : " . getMimeType($tmpFile) . "\n";
        if (!is_dir($uploadDir)) {
            echo "Dossier $uploadDir créé.\n";
        }
        echo "Sortie upload.php :\n$output\n";
        if (strpos($output, 'Fichier uploadé') !== false) {
            echo "Test upload : OK\n";
        } else {
            echo "Test upload : ECHEC\n";
        }

        if (file_exists('C:/upload/test.pdf')) {
            echo "Fichier présent : OK\n";
        } else {
            echo "Fichier présent : ECHEC\n";
        }

        echo "Chemin du fichier temporaire : $tmpFile\n";
        echo "Contenu du fichier temporaire : " . file_get_contents($tmpFile) . "\n";
        echo "Paramètres envoyés :\n";
        print_r($_FILES);
        print_r($_POST);

        if (file_exists($tmpFile)) {
            unlink($tmpFile);
        }
    }
}

if (php_sapi_name() === 'cli') {
    $test = new UploadFileTest();
    $test->run();
}