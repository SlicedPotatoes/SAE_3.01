Feature: Depot Justificatif
  En tant qu’étudiant
  Je veux soumettre mes justificatifs d'absence
  Afin de justifier mes absences.

  Scenario Outline: Déposer un justificatif sur une plage contenant une ou des absence(s) justifiable(s)
    Given je suis connecté à un compte étudiant de numéro étudiant <etu> sur la page de dépot de justificatif
    And j ai une absence le <date1>, <lock1>, avec comme etat <state1>
    And j ai une absence le <date2>, <lock2>, avec comme etat <state2>
    And j ai une absence le <date3>, <lock3>, avec comme etat <state3>
    When je met en date de départ <start> et en date de fin <end>
    And j ai entré un commentaire qui dit <comment>
    And j appuie sur le bouton envoyer le justificatif
    Then le justificatif est présent dans la base de données
    And le motif du justificatif doit être <comment>
    And l absence du <date1> doit être en <result1>
    And l absence du <date2> doit être en <result2>
    And l absence du <date3> doit être en <result3>
    And l absence du <date1> doit <lier1> au justificatif
    And l absence du <date2> doit <lier2> au justificatif
    And l absence du <date3> doit <lier3> au justificatif

    Examples:
    | etu | date1               | lock1       | state1       | result1 | lier1     | date2               | lock2           | state2       | result2      | lier2            | date3               | lock3       | state3       | result3 | lier3     | start      | end        | comment |
    | 1   | 2026-04-04 08:00:00 | justifiable | NotJustified | Pending | etre lier | 2026-04-05 08:00:00 | justifiable     | NotJustified | Pending      | etre lier        | 2026-04-06 08:00:00 | justifiable | NotJustified | Pending | etre lier | 2026-04-04 | 2026-04-06 | Malade  |
    | 1   | 2026-04-08 08:00:00 | justifiable | NotJustified | Pending | etre lier | 2026-04-09 08:00:00 | non justifiable | Refused      | Refused      | ne pas etre lier | 2026-04-10 08:00:00 | justifiable | NotJustified | Pending | etre lier | 2026-04-08 | 2026-04-10 | Malade  |

  Scenario Outline: Déposer un justificatif avec une date de début / fin invalide ou commentaire vide
    Given je suis connecté à un compte étudiant de numéro étudiant 1 sur la page de dépot de justificatif
    When je met en date de départ <debut> et en date de fin <fin>
    And j ai entré un commentaire qui dit <com>
    And j appuie sur le bouton envoyer le justificatif
    Then le justificatif n est pas présent dans la base de données
    And le message d erreur correspond a "<message>"
    Examples:
      | debut      | fin        | com    | message                                                                                                     |
      | 20260101   | 2026-01-30 | Malade | These rules must pass for `{ "absenceReason": "Malade", "startDate": "20260101", "endDate": "2026-01-30" }` |
      | 2026-01-01 | 20260130   | Malade | These rules must pass for `{ "absenceReason": "Malade", "startDate": "2026-01-01", "endDate": "20260130" }` |
      | 2026-02-01 | 2026-02-28 |        | These rules must pass for `{ "absenceReason": "", "startDate": "2026-02-01", "endDate": "2026-02-28" }`     |

  Scenario: Déposer un justificatif avec une date de début supérieur à la date de fin
    Given je suis connecté à un compte étudiant de numéro étudiant 1 sur la page de dépot de justificatif
    When je met en date de départ 2026-03-30 et en date de fin 2026-03-01
    And j ai entré un commentaire qui dit Malade
    And j appuie sur le bouton envoyer le justificatif
    Then le justificatif n est pas présent dans la base de données
    And le message d erreur correspond a "La date de début dois être inférieure ou égal a la date de fin"

  Scenario: Déposer un justificatif sur une plage ne contenant aucune absence justifiable
    Given je suis connecté à un compte étudiant de numéro étudiant 1 sur la page de dépot de justificatif
    And j ai une absence le 2026-06-01 08:00:00, non justifiable, avec comme etat Refused
    When je met en date de départ 2026-06-01 et en date de fin 2026-06-01
    And j ai entré un commentaire qui dit Malade
    And j appuie sur le bouton envoyer le justificatif
    Then le justificatif n est pas présent dans la base de données
    And le message d erreur correspond a "Il n'y a pas d'absence pouvant être justifié dans la période sélectionné"