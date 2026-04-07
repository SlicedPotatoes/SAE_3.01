Feature: Depot Justificatif
  En tant qu’étudiant
  Je veux soumettre mes justificatifs d'absence
  Afin de justifier mes absences.

  Scenario:
    Given je suis connecté à un compte étudiant de numéro étudiant "22400227"
    And j ai une absence le "04/04/2026" à "8H00" d une durée de "1h30" et "sans" examen
    And je suis sur la page de dépot de justificatif
    When je met en date de départ "2026-04-01" et en date de fin "2026-04-30"
    And j'appuie sur le bouton envoyer le justificatif
    Then le justificatif est présent dans ma liste justificative

  Scenario:
    Given que je suis étudiant et que je suis sur la page de dépôt d’un justificatif
    When je renseigne correctement les informations mais qu’aucune absence n’est enregistrée entre ces dates
    Then le justificatif n’est pas enregistré et un message d’erreur est affiché

  Scenario:
    Given que je suis étudiant et que je suis sur la page de dépôt d’un justificatif
    When je renseigne correctement les informations mais que la date de début est ultérieure à la date de fin
    Then le justificatif n’est pas enregistré et un message d’erreur est affiché

  Scenario:
    Given que je suis étudiant et que je suis sur la page de dépôt d’un justificatif
    When je renseigne correctement les informations mais que le commentaire est vide
    Then le justificatif n’est pas enregistré et un message d’erreur est affiché

  Scenario:
    Given que je suis étudiant et que je suis sur la page de dépôt d’un justificatif
    When je renseigne des informations incohérentes
    Then le justificatif n’est pas enregistré et un message d’erreur est affiché