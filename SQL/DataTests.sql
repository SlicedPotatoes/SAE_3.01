--liquibase formatted sql

--changeset Kevin:1 labels:Données de tests (Test d'intégration)
INSERT INTO Account(idaccount, lastname, firstname, email, password, accounttype)
VALUES (1, 'Étudiant', '1', 'etu@uphf.fr', '$2y$12$3croBeav8FjeTWpIz1YtCejL922EGmCl/IkU978LY2zORM7yjghK.', 'Student');

INSERT INTO GroupStudent(groupid, grouplabel)
VALUES (1, 'BUT INFO 2 Groupe A1');

INSERT INTO Student(idaccount, studentnumber, idgroupstudent)
VALUES (1, 22400227, 1);

INSERT INTO Resource(idresource, label)
VALUES (1, 'Ressource 1');