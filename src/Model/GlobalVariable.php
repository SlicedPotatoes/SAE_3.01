<?php

namespace Uphf\GestionAbsence\Model;

/**
 * Cette classe centralise les variables globales utilisé dans le projet
 * les rendant ainsi facilement accéssible pour les modifiers / en ajouter
 */
class GlobalVariable {
    private static bool $PROD = false;
    private static int $LIMIT_FILE_SIZE_UPLOAD = 5 * 1024 * 1024;
    private static array $ALLOWED_MIME_TYPE = ["image/jpeg", "image/png", "application/pdf"];
    private static array $ALLOWED_EXTENSIONS_FILE = ["jpg", "jpeg", "png", "pdf"];

    /**
     * Savoir si on est en environnement de production ou de développement
     * @return bool
     */
    public static function PROD(): bool {
        return self::$PROD;
    }

    /**
     * Taille maximale d'un fichier upload
     * @return int
     */
    public static function LIMIT_FILE_SIZE_UPLOAD(): int {
        return self::$LIMIT_FILE_SIZE_UPLOAD;
    }

    public static function LIMIT_FILE_SIZE_UPLOAD_MO(): float {
        return round(self::$LIMIT_FILE_SIZE_UPLOAD / (1024 * 1024), 2);
    }

    /**
     * MIME Type des fichiers autorisé à être upload
     * @return string[]
     */
    public static function ALLOWED_MIME_TYPE(): array {
        return self::$ALLOWED_MIME_TYPE;
    }

    /**
     * Extension des fichiers autorisée à être upload
     * @return array|string[]
     */
    public static function ALLOWED_EXTENSIONS_FILE(): array {
        return self::$ALLOWED_EXTENSIONS_FILE;
    }
}