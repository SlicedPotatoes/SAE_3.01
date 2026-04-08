Feature: Depot Justificatif
  En tant qu’étudiant
  Je veux soumettre mes justificatifs d'absence
  Afin de justifier mes absences.

  Scenario:
    Given je suis connecté à un compte étudiant de numéro étudiant "22400227" sur la page de dépot de justificatif
    And j ai une absence le "04/04/2026" à "8H00" d une durée de "1h30" et "sans" examen
    And j ai une absence le "04/04/2026" à "8H00" d une durée de "1h30" et "sans" examen
    And j ai une absence le "04/04/2026" à "8H00" d une durée de "1h30" et "sans" examen
    When je met en date de départ "2026-04-01" et en date de fin "2026-04-30"
    And j ai entré un commentaire qui dit "Malade"
    And j appuie sur le bouton envoyer le justificatif
    Then le justificatif est présent dans ma liste justificative
    And l'absence est mis en pending

  Scenario:
    Given je suis connecté à un compte étudiant de numéro étudiant "22400227" sur la page de dépot de justificatif
    When je met en date de départ "2026-05-01" et en date de fin "2026-05-30"
    And j ai entré un commentaire qui dit "Malade"
    And j'appuie sur le bouton envoyer le justificatif
    Then le justificatif est absent dans ma liste justificative
    And l'absence est mis en attente de traitement

  Scenario:
    Given je suis connecté à un compte étudiant de numéro étudiant "22400227"
    And j ai une absence le "04/03/2026" à "8H00" d une durée de "1h30" et "sans" examen
    And je suis sur la page de dépot de justificatif
    When je met en date de départ "2026-03-30" et en date de fin "2026-03-1"
    And j ai entré un commentaire qui dit "Malade"
    And j appuie sur le bouton envoyer le justificatif
    Then le justificatif est absent dans ma liste justificative

  Scenario:
    Given je suis connecté à un compte étudiant de numéro étudiant "22400227"
    And j ai une absence le "04/02/2026" à "8H00" d une durée de "1h30" et "sans" examen
    And je suis sur la page de dépot de justificatif
    When je met en date de départ "2026-02-01" et en date de fin "2026-02-30"
    And j ai entré un commentaire qui dit ""
    And j appuie sur le bouton envoyer le justificatif
    Then le justificatif est absent dans ma liste justificative

  Scenario:
    Given je suis connecté à un compte étudiant de numéro étudiant "22400227"
    And j ai une absence le "04/04/2026" à "8H00" d une durée de "1h30" et "sans" examen
    And je suis sur la page de dépot de justificatif
    When je met en date de départ "20260401" et en date de fin "2026-04-30"
    And j ai entré un commentaire qui dit "Malade"
    And j appuie sur le bouton envoyer le justificatif
    Then le justificatif est absent dans ma liste justificative