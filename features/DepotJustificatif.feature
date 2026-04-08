Feature: Depot Justificatif
  En tant qu’étudiant
  Je veux soumettre mes justificatifs d'absence
  Afin de justifier mes absences.

  Scenario:
    Given je suis connecté à un compte étudiant de numéro étudiant "22400227" sur la page de dépot de justificatif
    And j ai une absence le "04/04/2026" à "8H00" d une durée de "1H30" et "sans" examen
    And j ai une absence le "04/06/2026" à "8H00" d une durée de "1H30" et "sans" examen
    And j ai une absence le "04/07/2026" à "8H00" d une durée de "1H30" et "sans" examen
    When je met en date de départ "2026-04-01" et en date de fin "2026-04-30"
    And j ai entré un commentaire qui dit "Malade"
    And j appuie sur le bouton envoyer le justificatif
    Then le nombre de justificatif doit être égale à "1"
    And l absence du "04/04/2026" à "8H00" d une durée de "1h30" et "sans" examen doit être en "Pending"
    And l absence du "04/06/2026" à "8H00" d une durée de "1h30" et "sans" examen doit être en "NotJustified"
    And l absence du "04/07/2026" à "8H00" d une durée de "1h30" et "sans" examen doit être en "NotJustified"

  Scenario:
    Given je suis connecté à un compte étudiant de numéro étudiant "22400227" sur la page de dépot de justificatif
    When je met en date de départ "2026-05-01" et en date de fin "2026-05-30"
    And j ai entré un commentaire qui dit "Malade"
    And j appuie sur le bouton envoyer le justificatif
    Then le nombre de justificatif doit être égale à "0"

  Scenario Outline:
    Given je suis connecté à un compte étudiant de numéro étudiant <etu> sur la page de dépot de justificatif
    And j ai une absence le <date> à <heure> d une durée de <duree> et <exam> examen
    When je met en date de départ <debut> et en date de fin <fin>
    And j ai entré un commentaire qui dit <com>
    And j appuie sur le bouton envoyer le justificatif
    Then le nombre de justificatif doit être égale à <nbJusti>
    And l absence du <date> à <heure> d une durée de <duree> et <exam> examen doit être en <etat>
    Examples:
      | etu      | date         | heure  | duree  | exam   | debut        | fin          | com      | nbJusti | etat           |
      | 22400227 | "04/01/2026" | "8H00" | "1H30" | "sans" | 20260101     | "2026-01-30" | "Malade" | 0       | "NotJustified" |
      | 22400227 | "04/02/2026" | "8H00" | "1H30" | "sans" | "2026-02-01" | "2026-02-28" | ""       | 0       | "NotJustified" |
      | 22400227 | "04/03/2026" | "8H00" | "1H30" | "sans" | "2026-03-30" | "2026-03-01" | "Malade" | 0       | "NotJustified" |