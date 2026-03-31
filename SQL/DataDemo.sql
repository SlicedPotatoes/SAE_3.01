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