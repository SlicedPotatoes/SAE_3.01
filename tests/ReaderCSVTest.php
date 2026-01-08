<?php

use PHPUnit\Framework\TestCase;
use Uphf\GestionAbsence\Model\ReaderCSV;

final class ReaderCSVTest extends TestCase
{
    /**
     * Cette méthode est utilisé pour créer un fichier temporaire sur le disque, étant donné
     * que la classe ReaderCSV utilise souvent un chemin de fichier il faut un véritable fichier
     * pour pouvoir le tester. C'est donc simplement un outils pour la suite des tests
     *
     * @param string $content
     * @return string
     */
    private function makeTempFile(string $content): string
    {
        $file = tempnam(sys_get_temp_dir(), 'reader_csv_');

        file_put_contents($file, $content);
        return $file;
    }

    // TEST POUR LA METHODE isCSV()

    /**
     * Ce test permet de vérifié le comportement de isCSV() lorsque l'extetion est en minuscule
     * (exemple : .csv).
     *
     * Le résultat attendu est true
     * @return void
     */
    public function testIsCSVWithLowercaseExtension(): void
    {
        $this->assertTrue(ReaderCSV::isCSV('file.csv'));
    }

    /**
     * Ce test permet de vérifié le comportement de isCSV() lorsque l'extension est en majuscule
     * (exemple : .CSV).
     *
     * Le résultat attendu est true
     * @return void
     */
    public function testIsCSVWithUppercaseExtension(): void
    {
        $this->assertTrue(ReaderCSV::isCSV('file.CSV'));
    }

    /**
     * Ce test permet de vérifier le comportement de isCSV() lorsque l'extension est différente
     * de .csv.
     *
     * Le résultat attendu est false
     * @return void
     */
    public function testIsCSVWithWrongExtension(): void
    {
        $this->assertFalse(ReaderCSV::isCSV('file.txt'));
    }

    /**
     * Ce test permet de vérifier le comportement de isCSV() lorsque l'extension est absente.
     *
     * Le résultat attendu est false
     * @return void
     */
    public function testIsCSVWithoutAnyExtension(): void
    {
        $this->assertFalse(ReaderCSV::isCSV('file'));
    }

    // TEST POUR LA METHODE readCSV()

    /**
     * Ce test permet de vérifier que la méthode renvoie bien une liste avec la bonne association
     * des clefs et des données.
     *
     * Les résultats attendus sont que la liste contient deux éléments, parmis lesquelles une
     * liste avec un id de 1 et le 'name' 'Horus', et une liste avec un id de 2 et le 'name'
     * 'Lupercal'.
     *
     * @return void
     */
    public function testReadCSVValidFile(): void
    {
        $file = $this->makeTempFile("id;name\n1;Horus\n2;Lupercal\n");

        $data = ReaderCSV::readCSV($file);

        unlink($file);

        $this->assertCount(2, $data);
        $this->assertSame(['id' => '1', 'name' => 'Horus'], $data[0]);
        $this->assertSame(['id' => '2', 'name' => 'Lupercal'], $data[1]);
    }

    /**
     * Ce test permet de vérifier le comportement de la méthode readCSV() lorsque le fichier fourni est vide.
     *
     * Dans ce cas précis, la lecture des en-têtes échoue, ce qui doit provoquer l'arrêt de la méthode
     * et le retour d'un tableau vide.
     *
     * Le résultat attendu est donc un tableau vide.
     *
     * @return void
     */
    public function testReadCSVEmptyFile(): void
    {
        $file = $this->makeTempFile("");

        $data = ReaderCSV::readCSV($file);

        unlink($file);

        $this->assertSame([], $data);
    }

    /**
     * Ce test permet de vérifier le comportement de la méthode readCSV() lorsque le fichier ne
     * contient que la ligne d'en-têtes, sans aucune ligne de données.
     *
     * Dans ce cas, la lecture des en-têtes réussit, mais la boucle de lecture des données ne
     * récupère aucune ligne exploitable. La méthode doit donc retourner un tableau vide.
     *
     * Le résultat attendu est un tableau vide.
     *
     * @return void
     */
    public function testReadCSVOnlyHeadersReturnsEmptyArray(): void
    {
        $file = $this->makeTempFile("id;name\n");

        $data = ReaderCSV::readCSV($file);

        unlink($file);

        $this->assertSame([], $data);
    }

    /**
     * Ce test permet de vérifier que la méthode readCSV() ignore correctement les lignes vides
     * présentes dans le fichier CSV.
     *
     * Dans ce scénario, le fichier contient une ligne vide entre les en-têtes et une ligne de
     * données valide. La ligne vide ne doit pas être interprétée comme une donnée et doit être
     * ignorée par la méthode.
     *
     * Le résultat attendu est que seule la ligne contenant des données valides soit prise
     * en compte.
     *
     * @return void
     */
    public function testReadCSVIgnoresEmptyLines(): void
    {
        $file = $this->makeTempFile("id;name\n\n1;Alice\n");

        $data = ReaderCSV::readCSV($file);

        unlink($file);

        $this->assertCount(1, $data);
        $this->assertSame(['id' => '1', 'name' => 'Alice'], $data[0]);
    }

    /**
     * Ce test permet de vérifier que la méthode readCSV() ignore correctement les lignes dont
     * le nombre de colonnes ne correspond pas aux en-têtes définis.
     *
     * Dans ce cas, le fichier contient une ligne invalide ne comportant qu'une seule colonne,
     * alors que deux colonnes sont attendues. Cette ligne ne doit pas être prise en compte
     * lors de la construction du tableau de données.
     *
     * Les lignes valides avant et après cette ligne incorrecte doivent en revanche être
     * correctement lues.
     *
     * Le résultat attendu est un tableau contenant uniquement les lignes valides.
     *
     * @return void
     */
    public function testReadCSVIgnoresRowsWithWrongColumnCount(): void
    {
        $file = $this->makeTempFile("id;name\n1;Alice\n2\n3;Charlie\n");

        $data = ReaderCSV::readCSV($file);

        unlink($file);

        $this->assertCount(2, $data);
        $this->assertSame(['id' => '1', 'name' => 'Alice'], $data[0]);
        $this->assertSame(['id' => '3', 'name' => 'Charlie'], $data[1]);
    }

    // TEST POUR LA METHODE haveCollum()

    /**
     * Ce test permet de vérifier que la méthode haveCollum() retourne true lorsque les données
     * fournies contiennent exactement les colonnes attendues.
     *
     * Dans ce scénario, la première ligne de données possède les mêmes clefs que celles définies
     * dans le tableau des colonnes attendues, et leur nombre correspond également.
     *
     * Le résultat attendu est donc true.
     *
     * @return void
     */
    public function testHaveCollumValid(): void
    {
        $data = [['id' => 1, 'name' => 'Alice']];
        $columns = ['id', 'name'];

        $this->assertTrue(ReaderCSV::haveCollum($data, $columns));
    }

    /**
     * Ce test permet de vérifier que la méthode haveCollum() retourne false lorsqu'une ou
     * plusieurs colonnes attendues sont absentes des données fournies.
     *
     * Dans ce scénario, les données ne contiennent que la colonne "id", alors que la colonne
     * "name" est également attendue. La méthode doit donc détecter cette incohérence et
     * retourner false.
     *
     * Le résultat attendu est false.
     *
     * @return void
     */
    public function testHaveCollumMissingColumn(): void
    {
        $data = [['id' => 1]];
        $columns = ['id', 'name'];

        $this->assertFalse(ReaderCSV::haveCollum($data, $columns));
    }

    /**
     * Ce test permet de vérifier que la méthode haveCollum() retourne false lorsque le nombre
     * de colonnes présentes dans les données ne correspond pas au nombre de colonnes attendues.
     *
     * Dans ce scénario, les données contiennent une colonne supplémentaire qui n'est pas définie
     * dans la liste des colonnes attendues. La méthode doit donc considérer ces données comme
     * invalides et retourner false.
     *
     * Le résultat attendu est false.
     *
     * @return void
     */
    public function testHaveCollumWrongNumberOfColumns(): void
    {
        $data = [['id' => 1, 'name' => 'Alice', 'extra' => 'x']];
        $columns = ['id', 'name'];

        $this->assertFalse(ReaderCSV::haveCollum($data, $columns));
    }
}