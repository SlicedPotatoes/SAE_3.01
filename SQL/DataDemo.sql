--liquibase formatted sql

--changeset Kevin:1 labels:Données de démo
INSERT INTO Account(idaccount, lastname, firstname, email, password, accounttype)
VALUES (1, 'Responsable', 'Pédagogique', 'rp@uphf.fr', '$2y$12$3croBeav8FjeTWpIz1YtCejL922EGmCl/IkU978LY2zORM7yjghK.', 'EducationalManager'),
       (2, 'Étudiant', '1', 'etu1@uphf.fr', '$2y$12$3croBeav8FjeTWpIz1YtCejL922EGmCl/IkU978LY2zORM7yjghK.', 'Student'),
       (3, 'Étudiant', '2', 'etu2@uphf.fr', '$2y$12$3croBeav8FjeTWpIz1YtCejL922EGmCl/IkU978LY2zORM7yjghK.', 'Student'),
       (4, 'Professeur', '1', 'prof@uphf.fr', '$2y$12$3croBeav8FjeTWpIz1YtCejL922EGmCl/IkU978LY2zORM7yjghK.', 'Teacher'),
       (5, 'Secrétaire', '1', 'secretaire@uphf.fr', '$2y$12$3croBeav8FjeTWpIz1YtCejL922EGmCl/IkU978LY2zORM7yjghK.', 'Secretary');

INSERT INTO GroupStudent(groupid, grouplabel)
VALUES (1, 'BUT INFO 2 Groupe A1'),
       (2, 'BUT INFO 2 Groupe A2');

INSERT INTO Student(idaccount, studentnumber, idgroupstudent)
VALUES (2, 22400227, 1),
       (3, 22400228, 2);

INSERT INTO Teacher (idaccount)
VALUES (1),
       (4);

INSERT INTO Resource(idresource, label)
VALUES (1, 'R4.01'),
       (2, 'R4.02');

INSERT INTO Absence(idstudent, time, duration, examen, allowedjustification, idteacher, coursetype, idresource, dateresit, groupe, currentstate)
VALUES (2, '2026-01-01 08:00:00', '1 hours 30 minutes', false, false, 4, 'TD', 1, null, 'BUT 2 A1A2', 'Pending'),
       (2, '2026-01-01 09:30:00', '3 hours', true, false, 4, 'TP', 2, null, 'BUT 2 A1', 'Pending'),
       (2, '2026-01-07 08:00:00', '1 hours 30 minutes', false, true, 4, 'TD', 1, null, 'BUT 2 A1A2', 'Refused'),
       (2, '2026-01-07 09:30:00', '3 hours', false, false, null, 'TP', 1, null, 'BUT 2 A1', 'Validated');

INSERT INTO Justification(idjustification, cause, currentstate, startdate, enddate, senddate, processeddate, refusalreason)
VALUES (1, 'Malade', 'NotProcessed', '2026-01-01', '2026-01-02', '2026-01-03 08:05:54.1234', null, null),
       (2, 'Malade', 'Processed', '2026-01-07', '2026-01-08', '2026-01-09 09:04:12.1234', '2026-01-09 11:21:56.1234', 'Justification non valide');

INSERT INTO AbsenceJustification(idstudent, time, idjustification)
VALUES (2, '2026-01-01 08:00:00', 1),
       (2, '2026-01-01 09:30:00', 1),
       (2, '2026-01-07 08:00:00', 2),
       (2, '2026-01-07 09:30:00', 2);