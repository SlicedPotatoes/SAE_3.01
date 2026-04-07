--liquibase formatted sql

--changeset Kevin:1 labels:Données de démo
INSERT INTO Account(idaccount, lastname, firstname, email, password, accounttype)
VALUES (-1, 'Durand', 'Mehdi', 'rp@uphf.fr', '$2y$12$3croBeav8FjeTWpIz1YtCejL922EGmCl/IkU978LY2zORM7yjghK.', 'EducationalManager'),
       (-2, 'Martin', 'Yohan', 'etu1@uphf.fr', '$2y$12$3croBeav8FjeTWpIz1YtCejL922EGmCl/IkU978LY2zORM7yjghK.', 'Student'),
       (-3, 'Martin', 'Jules', 'etu2@uphf.fr', '$2y$12$3croBeav8FjeTWpIz1YtCejL922EGmCl/IkU978LY2zORM7yjghK.', 'Student'),
       (-4, 'Simonne', 'Jean-Raphael', 'etu3@uphf.fr', '$2y$12$3croBeav8FjeTWpIz1YtCejL922EGmCl/IkU978LY2zORM7yjghK.', 'Student'),
       (-5, 'Professeur', 'Moustache', 'prof@uphf.fr', '$2y$12$3croBeav8FjeTWpIz1YtCejL922EGmCl/IkU978LY2zORM7yjghK.', 'Teacher'),
       (-6, 'Petit', 'Robert', 'secretaire@uphf.fr', '$2y$12$3croBeav8FjeTWpIz1YtCejL922EGmCl/IkU978LY2zORM7yjghK.', 'Secretary');

INSERT INTO GroupStudent(groupid, grouplabel)
VALUES (-1, 'BUT INFO 2 Groupe A1'),
       (-2, 'BUT INFO 2 Groupe A2'),
       (-3, 'BUT INFO 2 Groupe B');

INSERT INTO Student(idaccount, studentnumber, idgroupstudent)
VALUES (-2, 22400227, -1),
       (-3, 22400228, -2),
       (-4, 22400229, -2);

INSERT INTO Teacher (idaccount)
VALUES (-1),
       (-5);

INSERT INTO Resource(idresource, label)
VALUES (-1, 'R4.01 Architecture Logiciel'),
       (-2, 'R4.02 Optimisation');

INSERT INTO comments(textcomment)
VALUES ('Le document fourni est illisible.'),
       ('Le document ne couvre pas toutes les absences indiquées.'),
       ('Le motif indiqué n''est pas suffisant pour justifier l''absence.'),
       ('Le justificatif ne comporte pas de signature ou cachet officiel.');

INSERT INTO Absence(idstudent, time, duration, examen, allowedjustification, idteacher, coursetype, idresource, dateresit, groupe, currentstate)
VALUES (-3, '2026-01-01 08:00:00', '1 hours 30 minutes', false, false, -5, 'TD', -1, null, 'BUT 2 A1A2', 'Pending'),
       (-3, '2026-01-01 09:30:00', '3 hours', true, false, -5, 'TP', -2, null, 'BUT 2 A1', 'Pending'),
       (-3, '2026-01-07 08:00:00', '1 hours 30 minutes', false, true, -5, 'TD', -1, null, 'BUT 2 A1A2', 'Refused'),
       (-3, '2026-01-07 09:30:00', '3 hours', false, false, -1, 'TP', -1, null, 'BUT 2 A1', 'Validated');

INSERT INTO Justification(idjustification, cause, currentstate, startdate, enddate, senddate, processeddate, refusalreason)
VALUES (-1, 'Malade', 'NotProcessed', '2026-01-01', '2026-01-02', '2026-01-03 08:05:54.1234', null, null),
       (-2, 'Malade', 'Processed', '2026-01-07', '2026-01-08', '2026-01-09 09:04:12.1234', '2026-01-09 11:21:56.1234', 'Justification non valide');

INSERT INTO AbsenceJustification(idstudent, time, idjustification)
VALUES (-3, '2026-01-01 08:00:00', -1),
       (-3, '2026-01-01 09:30:00', -1),
       (-3, '2026-01-07 08:00:00', -2),
       (-3, '2026-01-07 09:30:00', -2);


-- ABSENCES Yohan MARTIN
INSERT INTO Absence
(idstudent, time, duration, examen, allowedjustification, idteacher, coursetype, idresource, dateresit, groupe, currentstate)
VALUES
-- Lundi
(-2, '2026-01-12 08:00:00', '1 hours 30 minutes', false, true, -5, 'TD', -1, null, 'BUT 2 A1', 'NotJustified'),
(-2, '2026-01-12 09:30:00', '3 hours', false, true, -5, 'TP', -2, null, 'BUT 2 A1', 'NotJustified'),
-- Mardi
(-2, '2026-01-13 08:00:00', '1 hours 30 minutes', false, true, -5, 'TD', -1, null, 'BUT 2 A1', 'NotJustified'),
(-2, '2026-01-13 09:30:00', '3 hours', false, true, -5, 'TP', -2, null, 'BUT 2 A1', 'NotJustified'),
-- Mercredi
(-2, '2026-01-14 08:00:00', '3 hours', false, true, -5, 'TP', -2, null, 'BUT 2 A1', 'NotJustified'),
-- Jeudi
(-2, '2026-01-15 08:00:00', '1 hours 30 minutes', false, true, -5, 'TD', -1, null, 'BUT 2 A1', 'NotJustified'),
(-2, '2026-01-15 09:30:00', '3 hours', false, true, -5, 'TP', -2, null, 'BUT 2 A1', 'NotJustified'),
-- Vendredi
(-2, '2026-01-16 08:00:00', '1 hours 30 minutes', false, true, -5, 'TD', -1, null, 'BUT 2 A1', 'NotJustified');

-- Absences déjà validé pour avoir du contenu dans les justificatifs
INSERT INTO Absence(idstudent, time, duration, examen, allowedjustification, idteacher, coursetype, idresource, dateresit, groupe, currentstate)
VALUES (-2, '2026-01-01 08:00:00', '1 hours 30 minutes', false, false, -5, 'TD', -1, null, 'BUT 2 A1A2', 'Validated'),
       (-2, '2026-01-01 09:30:00', '3 hours', true, false, -5, 'TP', -2, null, 'BUT 2 A1', 'Validated'),
       (-2, '2026-01-07 08:00:00', '1 hours 30 minutes', false, true, -5, 'TD', -1, null, 'BUT 2 A1A2', 'Refused'),
       (-2, '2026-01-07 09:30:00', '3 hours', false, false, -1, 'TP', -1, null, 'BUT 2 A1', 'Validated');

INSERT INTO Justification(idjustification, cause, currentstate, startdate, enddate, senddate, processeddate, refusalreason)
VALUES (-3, 'Malade', 'Processed', '2026-01-01', '2026-01-02', '2026-01-03 08:05:54.1234', '2026-01-09 11:21:56.1234', 'Tout est bon'),
       (-4, 'Malade', 'Processed', '2026-01-07', '2026-01-08', '2026-01-09 09:04:12.1234', '2026-01-09 11:21:56.1234', 'Justification non valide');

INSERT INTO AbsenceJustification(idstudent, time, idjustification)
VALUES (-2, '2026-01-01 08:00:00', -3),
       (-2, '2026-01-01 09:30:00', -3),
       (-2, '2026-01-07 08:00:00', -4),
       (-2, '2026-01-07 09:30:00', -4);
