<?php

use PHPUnit\Framework\TestCase;
use Uphf\GestionAbsence\Model\Statistics\DataAdapter;
use Uphf\GestionAbsence\Model\DB\Select\SelectBuilder\ProportionStatisticsType;

/**
 * Classe de test pour la classe statique DataAdapter
 */
final class DataAdapterTest extends TestCase {
    /**
     * Ce test permet de vérifier le comportement de proportionAdapter($rows, $callableLabelFormat, $callableColorPie)
     * quand le type de données est pour un graphique affichant les proportions pour les types de cours.
     *
     * Résultat attendu : true
     * @return void
     */
    public function testAdapteurForTypeCourse(): void {
        $rows = [
            ['label' => 'TD', 'value' => 10],
            ['label' => 'TP', 'value' => 12],
            ['label' => 'CM', 'value' => 8],
            ['label' => 'BEN', 'value' => 5],
            ['label' => 'DS', 'value' => 2]
        ];
        $output = [
            'labels' => ['TD', 'TP', 'CM', 'BEN', 'DS'],
            'data' => [10, 12, 8, 5, 2],
            'backgroundColor' => NULL
        ];

        $type = ProportionStatisticsType::TypeCourse;
        $result = DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());

        $this->assertSame($output, $result);
    }

    /**
     * Ce test permet de vérifier le comportement de proportionAdapter($rows, $callableLabelFormat, $callableColorPie)
     * quand le type de données est pour un graphique affichant les proportions pour les enseignants.
     *
     * Comportement attendu:
     * - Ranger les valeurs dans un dictionnaire, avec 3 clés (labels, data, backgroundColor)
     * - Pas de modification des labels
     * - backgroundColor = NULL
     *
     * Résultat attendu : true
     * @return void
     */
    public function testAdapteurForTeacher(): void {
        $rows = [
            ['label' => 'Prof 1', 'value' => 10],
            ['label' => 'Prof 2', 'value' => 12],
            ['label' => 'Prof 3', 'value' => 8],
        ];
        $output = [
            'labels' => ['Prof 1', 'Prof 2', 'Prof 3'],
            'data' => [10, 12, 8],
            'backgroundColor' => NULL
        ];

        $type = ProportionStatisticsType::Teacher;
        $result = DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());

        $this->assertSame($output, $result);
    }

    /**
     * Ce test permet de vérifier le comportement de proportionAdapter($rows, $callableLabelFormat, $callableColorPie)
     * quand le type de données est pour un graphique affichant les proportions pour les ressources
     *
     *  Comportement attendu:
     *  - Ranger les valeurs dans un dictionnaire, avec 3 clés (labels, data, backgroundColor)
     *  - Pas de modification des labels
     *  - backgroundColor = NULL
     *
     * Résultat attendu : true
     * @return void
     */
    public function testAdapteurForResource(): void {
        $rows = [
            ['label' => 'R3.01', 'value' => 10],
            ['label' => 'R3.02', 'value' => 12],
            ['label' => 'R3.03', 'value' => 8],
        ];
        $output = [
            'labels' => ['R3.01', 'R3.02', 'R3.03'],
            'data' => [10, 12, 8],
            'backgroundColor' => NULL
        ];

        $type = ProportionStatisticsType::Resource;
        $result = DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());

        $this->assertSame($output, $result);
    }

    /**
     * Ce test permet de vérifier le comportement de proportionAdapter($rows, $callableLabelFormat, $callableColorPie)
     * quand le type de données est pour un graphique affichant les proportions pour les groupes.
     *
     *  Comportement attendu:
     *  - Ranger les valeurs dans un dictionnaire, avec 3 clés (labels, data, backgroundColor)
     *  - Pas de modification des labels
     *  - backgroundColor = NULL
     *
     * Résultat attendu : true
     * @return void
     */
    public function testAdapteurForGroup(): void {
        $rows = [
            ['label' => 'BUT INFO 2 A1', 'value' => 10],
            ['label' => 'BUT INFO 2 A2', 'value' => 12],
            ['label' => 'BUT INFO 2 B', 'value' => 8],
        ];
        $output = [
            'labels' => ['BUT INFO 2 A1', 'BUT INFO 2 A2', 'BUT INFO 2 B'],
            'data' => [10, 12, 8],
            'backgroundColor' => NULL
        ];

        $type = ProportionStatisticsType::Group;
        $result = DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());

        $this->assertSame($output, $result);
    }

    /**
     * Ce test permet de vérifier le comportement de proportionAdapter($rows, $callableLabelFormat, $callableColorPie)
     * quand le type de données est pour un graphique affichant les proportions pour les etats des absences.
     *
     *  Comportement attendu:
     *  - Ranger les valeurs dans un dictionnaire, avec 3 clés (labels, data, backgroundColor)
     *  - Modification des labels
     *  - Couleurs en fonction du label
     *
     * Résultat attendu : true
     * @return void
     */
    public function testAdapteurForState(): void {
        $rows = [
            ['label' => 'Validated', 'value' => 10],
            ['label' => 'Refused', 'value' => 12],
            ['label' => 'NotJustified', 'value' => 8],
            ['label' => 'Pending', 'value' => 4]
        ];
        $output = [
            'labels' => ['Validé', 'Refusé', 'Non justifié', 'En attente'],
            'data' => [10, 12, 8, 4],
            'backgroundColor' => ['rgb(25, 135, 84)', 'rgb(220, 53, 69)', 'rgb(255, 38, 53)', 'rgb(108, 117, 125)']
        ];

        $type = ProportionStatisticsType::State;
        $result = DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());

        $this->assertSame($output, $result);
    }

    /**
     * Ce test permet de vérifier le comportement de proportionAdapter($rows, $callableLabelFormat, $callableColorPie)
     * quand le type de données est pour un graphique affichant les proportions pour les examens.
     *
     * Comportement attendu:
     * - Ranger les valeurs dans un dictionnaire, avec 3 clés (labels, data, backgroundColor)
     * - Modification des labels
     * - Couleurs en fonction du label
     *
     * Résultat attendu : true
     * @return void
     */
    public function testAdapteurForExamen(): void {
        $rows = [
            ['label' => true, 'value' => 10],
            ['label' => false, 'value' => 12]
        ];
        $output = [
            'labels' => ['Avec examen', 'Sans examen'],
            'data' => [10, 12],
            'backgroundColor' => ['rgb(255, 205, 86)', 'rgb(54, 162, 235)']
        ];

        $type = ProportionStatisticsType::Examen;
        $result = DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());

        $this->assertSame($output, $result);
    }

    /**
     * Ce test sert à vérifier qu'une exception est bien levé s'il manque la colonne label sur une ligne.
     * Dans le cas où on traite des données pour les types de cours
     *
     * @return void
     * @throws Exception
     */
    public function testNoLabelTypeCourse(): void {
        $rows = [
            ['label' => 'TD', 'value' => 10],
            ['value' => 12]
        ];

        $this->expectException(Exception::class);

        $type = ProportionStatisticsType::TypeCourse;
        DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());
    }

    /**
     * Ce test sert à vérifier qu'une exception est bien levé s'il manque la colonne label sur une ligne.
     * Dans le cas où on traite des données pour les enseignants
     *
     * @return void
     * @throws Exception
     */
    public function testNoLabelTeacher(): void {
        $rows = [
            ['label' => 'P1', 'value' => 10],
            ['value' => 12]
        ];

        $this->expectException(Exception::class);

        $type = ProportionStatisticsType::Teacher;
        DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());
    }

    /**
     * Ce test sert à vérifier qu'une exception est bien levé s'il manque la colonne label sur une ligne.
     * Dans le cas où on traite des données pour les ressources
     *
     * @return void
     * @throws Exception
     */
    public function testNoLabelResource(): void {
        $rows = [
            ['label' => 'R3.01', 'value' => 10],
            ['value' => 12]
        ];

        $this->expectException(Exception::class);

        $type = ProportionStatisticsType::Resource;
        DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());
    }

    /**
     * Ce test sert à vérifier qu'une exception est bien levé s'il manque la colonne label sur une ligne.
     * Dans le cas où on traite des données pour les groupes
     *
     * @return void
     * @throws Exception
     */
    public function testNoLabelGroup(): void {
        $rows = [
            ['label' => 'BUT 1', 'value' => 10],
            ['value' => 12]
        ];

        $this->expectException(Exception::class);

        $type = ProportionStatisticsType::Group;
        DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());
    }

    /**
     * Ce test sert à vérifier qu'une exception est bien levé s'il manque la colonne label sur une ligne.
     * Dans le cas où on traite des données pour les états
     *
     * @return void
     * @throws Exception
     */
    public function testNoLabelState(): void {
        $rows = [
            ['label' => 'Pending', 'value' => 10],
            ['value' => 12]
        ];

        $this->expectException(Exception::class);

        $type = ProportionStatisticsType::State;
        DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());
    }

    /**
     * Ce test sert à vérifier qu'une exception est bien levé s'il manque la colonne label sur une ligne.
     * Dans le cas où on traite des données pour les examens
     *
     * @return void
     * @throws Exception
     */
    public function testNoLabelExam(): void {
        $rows = [
            ['label' => true, 'value' => 10],
            ['value' => 12]
        ];

        $this->expectException(Exception::class);

        $type = ProportionStatisticsType::Examen;
        DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());
    }

    /**
     * Ce test sert à vérifier qu'une exception est bien levé s'il manque la colonne value sur une ligne.
     * Dans le cas où on traite des données pour les types de cours
     *
     * @return void
     * @throws Exception
     */
    public function testNoValueTypeCourse(): void {
        $rows = [
            ['label' => 'TD', 'value' => 1],
            ['label' => 'TP']
        ];

        $this->expectException(Exception::class);

        $type = ProportionStatisticsType::TypeCourse;
        DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());
    }

    /**
     * Ce test sert à vérifier qu'une exception est bien levé s'il manque la colonne value sur une ligne.
     * Dans le cas où on traite des données pour les enseignants
     *
     * @return void
     * @throws Exception
     */
    public function testNoValueTeacher(): void {
        $rows = [
            ['label' => 'Prof 1', 'value' => 1],
            ['label' => 'Prof 2']
        ];

        $this->expectException(Exception::class);

        $type = ProportionStatisticsType::Teacher;
        DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());
    }

    /**
     * Ce test sert à vérifier qu'une exception est bien levé s'il manque la colonne value sur une ligne.
     * Dans le cas où on traite des données pour les ressources
     *
     * @return void
     * @throws Exception
     */
    public function testNoValueResource(): void {
        $rows = [
            ['label' => 'R3.01', 'value' => 10],
            ['label' => 'R3.02']
        ];

        $this->expectException(Exception::class);

        $type = ProportionStatisticsType::Resource;
        DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());
    }

    /**
     * Ce test sert à vérifier qu'une exception est bien levé s'il manque la colonne value sur une ligne.
     * Dans le cas où on traite des données pour les groupes
     *
     * @return void
     * @throws Exception
     */
    public function testNoValueGroup(): void {
        $rows = [
            ['label' => 'BUT 1', 'value' => 1],
            ['label' => 'BUT 2']
        ];

        $this->expectException(Exception::class);

        $type = ProportionStatisticsType::Group;
        DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());
    }

    /**
     * Ce test sert à vérifier qu'une exception est bien levé s'il manque la colonne value sur une ligne.
     * Dans le cas où on traite des données pour les états
     *
     * @return void
     * @throws Exception
     */
    public function testNoValueState(): void {
        $rows = [
            ['label' => 'Pending', 'value' => 1],
            ['label' => 'Validated']
        ];

        $this->expectException(Exception::class);

        $type = ProportionStatisticsType::State;
        DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());
    }

    /**
     * Ce test sert à vérifier qu'une exception est bien levé s'il manque la colonne value sur une ligne.
     * Dans le cas où on traite des données pour les examens
     *
     * @return void
     * @throws Exception
     */
    public function testNoValuelExam(): void {
        $rows = [
            ['label' => true, 'value' => 1],
            ['label' => false]
        ];

        $this->expectException(Exception::class);

        $type = ProportionStatisticsType::Examen;
        DataAdapter::proportionAdapter($rows, $type->callableLabelFormat(), $type->callableColorPie());
    }
}