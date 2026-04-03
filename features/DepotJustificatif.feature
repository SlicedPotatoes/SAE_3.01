Feature: Depot Justificatif
  En tant qu’étudiant
  Je veux soumettre mes justificatifs d'absence
  Afin de justifier mes absences.

  Scenario:
    Given que je suis étudiant et que je suis sur la page de dépôt d’un justificatif
    When renseigne correctement les informations
    Then le justificatif est enregistré correctement dans la base de données

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
