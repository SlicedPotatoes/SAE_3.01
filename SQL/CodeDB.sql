--liquibase formatted sql

--changeset Isaac:1 labels:Initialisation context:initialisation

create table groupStudent
(
    idGroupStudent serial primary key,
    label          text not null
);

create table student
(
    idStudent  int primary key,
    lastName   text        not null,
    firstName  text        not null,
    firstName2 text,
    email      text unique not null check (email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+.[A-Za-z]{2,}$') ,
    idGroupStudent   int references groupStudent (idGroupStudent) not null
);

create table teacher
(
    idTeacher serial primary key,
    lastName  text        not null,
    firstName text        not null,
    email     text unique not null check ( email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+.[A-Za-z]{2,}$')
);

create type stateAbsence as enum
    (
        'Validated',
        'Refused',
        'NotJustified',
        'Pending'
        );

create type courseType as enum
    (
        'TP',
        'CM',
        'TD',
        'BEN'
        );

create type stateJustif as enum
    (
        'Processed',
        'NotProcessed'
        );

create table resource
(
    idResource serial primary key,
    label      text not null
);
create table justification
(
    idJustification serial primary key,
    cause           text                               not null,
    currentState    stateJustif                        not null,
    startDate       timestamp                          not null,
    endDate         timestamp                          not null,
    sendDate timestamp not null,
    processedDate timestamp
);

create table file
(
    idFile          serial primary key,
    fileName             text                                           not null,
    idJustification int references justification (idJustification) not null
);

create table absence
(
    time                 timestamp                                not null,
    duration             interval                                 not null,
    examen               boolean                                  not null,
    allowedJustification boolean                                  not null,
    idTeacher            int references teacher (idTeacher),
    idStudent            int references student (idStudent)       not null,
    currentState         stateAbsence                             not null,
    courseType           courseType                               not null,
    idResource           int references resource (idResource)     not null,
    dateResit timestamp,
    primary key (idStudent, time)
);

create table absenceJustification
(
    idStudent       int,
    time           timestamp,
    idjustification int references justification (idJustification),
    constraint fk_absence
        foreign key (idStudent, time) references absence (idStudent, time) on delete cascade,
    primary key (idStudent, time, idJustification)
);

--rollback drop table absence, file, absenceJustification, resource, courseType, state, teacher, student, groupStudent, justification cascade;
--rollback drop type stateAbsence, stateJustif, courseType cascade;

--changeset Kevin:2 labels:sprint2 context:sprint2

-- Création d'un type "accountType"
CREATE TYPE accountType AS enum
    (
    'Student',
    'Teacher',
    'EducationalManager',
    'Secretary'
);

-- Création de la table Account
CREATE TABLE Account
(
    idAccount SERIAL PRIMARY KEY,
    lastName TEXT NOT NULL,
    firstName TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL CHECK ( email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+.[A-Za-z]{2,}$'),
    password TEXT NOT NULL,
    accountType accountType NOT NULL
);

-- Création de la nouvelle table Student
CREATE TABLE Student_
(
    idAccount INT PRIMARY KEY REFERENCES Account ON DELETE CASCADE,
    studentNumber INT NOT NULL UNIQUE,
    idGroupStudent INT NOT NULL REFERENCES groupStudent
);

-- Création de la nouvelle table Teacher
CREATE TABLE Teacher_
(
    idAccount INT PRIMARY KEY REFERENCES Account ON DELETE CASCADE
);

-- Insérer les étudiants, avec "password" comme mot de passe
INSERT INTO Account (lastName, firstName, email, password, accountType)
(
 SELECT lastName, firstName, email, '$2y$12$pZttVgqi/eCaSh2pz.Kj7.L95sFqMm0.Xl2YbqqSiY4fE/MoHYq9G', 'Student'
 FROM Student
);

-- Insérer les données relatives a un étudiant
INSERT INTO Student_
(
 SELECT idAccount, idStudent, idGroupStudent
 FROM Student
 JOIN Account USING(email)
);

-- Insérer les professeurs, avec "password" comme mot de passe
INSERT INTO Account (lastName, firstName, email, password, accountType)
(
 SELECT lastName, firstName, email, '$2y$12$pZttVgqi/eCaSh2pz.Kj7.L95sFqMm0.Xl2YbqqSiY4fE/MoHYq9G', 'Teacher'
 FROM Teacher
);

-- Créer les entités professeur
INSERT INTO Teacher_
(
    SELECT idAccount
    FROM Account
    WHERE accountType = 'Teacher'
);

-- Enlever la contrainte de la table absence avec teacher
ALTER TABLE Absence DROP CONSTRAINT absence_idteacher_fkey;

-- Mettre à jour les absences avec les nouveaux ids.
UPDATE Absence abs
SET idTeacher = newT.idAccount
FROM Teacher t
JOIN Account a USING(email)
JOIN Teacher_ newT USING(idAccount)
WHERE abs.idTeacher = t.idTeacher;

-- Remettre la contrainte sur la table Absence
ALTER TABLE Absence ADD CONSTRAINT absence_idteacher_fkey FOREIGN KEY(idTeacher) REFERENCES Teacher_;

-- Supprimer la table Teacher
DROP TABLE Teacher;

-- Rename la table Teacher_
ALTER TABLE Teacher_ RENAME TO Teacher;

-- Enlever la contrainte de la table absence avec student
ALTER TABLE absence DROP CONSTRAINT absence_idstudent_fkey;

-- Enlever la contrainte de la table absenceJustification avec absence
ALTER TABLE absenceJustification DROP CONSTRAINT fk_absence;

-- Mettre à jour les absences avec les nouveaux ids.
UPDATE Absence abs
SET idStudent = newS.idAccount
FROM Student s
JOIN Student_ newS ON newS.studentNumber = s.idStudent
WHERE abs.idStudent = s.idStudent;

-- Mettre à jour la table absenceJustification
UPDATE AbsenceJustification aj
SET idStudent = newS.idAccount
FROM Student s
JOIN Student_ newS ON newS.studentNumber = s.idStudent
WHERE aj.idStudent = s.idStudent;

-- Remettre la contrainte sur la table Absence
ALTER TABLE Absence ADD CONSTRAINT absence_idstudent_fkey FOREIGN KEY(idStudent) REFERENCES Student_;

-- Remettre la contrainte sur la table AbsenceJustification
ALTER TABLE AbsenceJustification ADD CONSTRAINT fk_absence FOREIGN KEY (idStudent, time) REFERENCES Absence ON DELETE CASCADE;

-- Supprimer la table Student
DROP TABLE Student;

-- Rename la table Student_
ALTER TABLE Student_ RENAME TO Student;

/* liquibase rollback
    ALTER TABLE Student RENAME TO Student_;

    create table student
    (
        idStudent  int primary key,
        lastName   text        not null,
        firstName  text        not null,
        firstName2 text,
        email      text unique not null check (email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+.[A-Za-z]{2,}$') ,
        idGroupStudent   int references groupStudent (idGroupStudent) not null
    );

    INSERT INTO Student
    (
        SELECT studentNumber, lastName, firstName, '', email, idGroupStudent
        FROM Account
        JOIN Student_ USING(idAccount)
    );

    ALTER TABLE Teacher RENAME TO Teacher_;

    create table teacher
    (
        idTeacher serial primary key,
        lastName  text        not null,
        firstName text        not null,
        email     text unique not null check ( email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+.[A-Za-z]{2,}$')
    );

    INSERT INTO Teacher (lastName, firstName, email)
    (
        SELECT lastName, firstName, email
        FROM Account
        WHERE accountType = 'Teacher'
    );

    ALTER TABLE Absence DROP CONSTRAINT absence_idteacher_fkey;

    UPDATE Absence abs
    SET idTeacher = old.idTeacher
    FROM Teacher old
    JOIN Account a using(email)
    WHERE a.idAccount = abs.idTeacher;

    ALTER TABLE Absence ADD CONSTRAINT absence_idteacher_fkey FOREIGN KEY(idTeacher) REFERENCES Teacher;

    ALTER TABLE Absence DROP CONSTRAINT absence_idstudent_fkey;
    ALTER TABLE AbsenceJustification DROP CONSTRAINT fk_absence;

    UPDATE Absence abs
    SET idStudent = old.idStudent
    FROM Student old
    JOIN Account a using(email)
    WHERE a.idAccount = abs.idStudent;

    UPDATE AbsenceJustification aj
    SET idStudent = old.idStudent
    FROM Student old
    JOIN Account a using(email)
    WHERE a.idAccount = aj.idStudent;

    ALTER TABLE Absence ADD CONSTRAINT absence_idstudent_fkey FOREIGN KEY(idStudent) REFERENCES Student;
    ALTER TABLE AbsenceJustification ADD CONSTRAINT fk_absence FOREIGN KEY(idStudent, time) REFERENCES Absence ON DELETE CASCADE;

    DROP TYPE accountType CASCADE;
    DROP TABLE account CASCADE;
    DROP TABLE Student_;
    DROP TABLE Teacher_;
*/

--changeset Kevin:3 labels:bugfix context:correction email regex
UPDATE Account
SET email = email || '.error' -- Pour facilement identifier les emails problématique
WHERE email !~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$';

ALTER TABLE Account DROP CONSTRAINT account_email_check;
ALTER TABLE Account ADD CONSTRAINT account_email_check CHECK (email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$');

/* liquibase rollback
   ALTER TABLE Account DROP CONSTRAINT account_email_check;
   ALTER TABLE Account ADD CONSTRAINT account_email_check CHECK (email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+.[A-Za-z]{2,}$');

   UPDATE Account
   SET email = regexp_replace(email, '\.error$', '')
   WHERE email ~* '\.error$';
*/


--changeset Yann:4 labels:addColumn context:ajout motif refus

-- Ajout de la colonne motifRefus dans la table justification
ALTER TABLE justification
ADD COLUMN refusalreason TEXT;

/* liquibase rollback
   ALTER TABLE justification DROP COLUMN refusalreason;
 */

--changeset Louis:5 labels:addView context:ajout d'une vue pour avoir toutes les informations d'un étudiant

CREATE VIEW StudentAccount AS
SELECT a.idaccount      as StudentID,
       a.lastname       as LastName,
       a.firstname      as FirstName,
       a.email          as Email,
       a.accounttype    as AccountType,
       s.studentnumber  as StudentNumber,
       g.idgroupstudent as GroupID,
       g.label          as GroupLabel
FROM Account a
         JOIN Student s ON a.idaccount = s.idaccount
         JOIN groupstudent g ON s.idgroupstudent = g.idgroupstudent;

/* liquibase rollback
   DROP VIEW StudentAccount;
*/

--changeset Kevin:6 labels:systeme de recherche performant context:searchStudent

-- Ajout de l'extension unaccent, elle ajoute la fonction unaccent(string).
-- Cette fonction permet de remplacer les caractères accentués d'une chaine passée en paramètre par leurs correspondances non accentués
CREATE EXTENSION IF NOT EXISTS unaccent;

-- Ajout de l'extension pg_trgm, cette extension ajoute l'opérateur %
-- Celui-ci evalue la condition "la similarité entre la chaine de gauche et de droite est supérieur à threshold"
CREATE EXTENSION IF NOT EXISTS pg_trgm;

-- Création de deux champs dans Account pour stocker la version "normaliser" pour nom et prénom
-- Le fais de les stockés évite de les recalculer à chaque fois.
ALTER TABLE Account ADD COLUMN search_firstname text;
ALTER TABLE Account ADD COLUMN search_lastname text;

-- Mettre à jour les champs search_firstname et search_lastname pour les comptes existants
UPDATE Account
SET search_lastname = unaccent(lower(lastName)),
    search_firstname = unaccent(lower(firstName));

-- Ajout d'une trigger function pour le trigger de mise à jour d'un Account
-- Génère la valeur des champs search_lastname et search_firstname
CREATE OR REPLACE FUNCTION updateAccountBeforeInsert() RETURNS TRIGGER AS $$
BEGIN
    NEW.search_lastname := unaccent(lower(NEW.lastName));
    NEW.search_firstname := unaccent(lower(NEW.firstname));
    RETURN NEW;
END
$$ LANGUAGE plpgsql;

-- Ajout d'un trigger exécutant la fonction updateAccountBeforeInsert() pour chaque
-- INSERT ou UPDATE sur la table Account
CREATE OR REPLACE TRIGGER updateAccountBeforeInsert
BEFORE INSERT OR UPDATE ON Account
FOR EACH ROW EXECUTE FUNCTION updateAccountBeforeInsert();

-- Création des index pour optimiser la recherche, pour faire cours les index sont des structures de données utilisées en back de postgres
-- Ils permettent à postgres de ne pas vérifier les valeurs de chaque ligne pour trouver la correspondance par exemple dans le cas d'une restriction.
-- GIN est un type d'index ajouté par pg_trgm, il optimise la recherche de similarité
CREATE INDEX idx_account_search_lastname_trgm ON Account USING GIN (search_lastname gin_trgm_ops);
CREATE INDEX idx_account_search_firstname_trgm ON Account USING GIN (search_firstname gin_trgm_ops);

/* liquibase rollback
 DROP INDEX idx_account_search_firstname_trgm;
 DROP INDEX idx_account_search_lastname_trgm;

 DROP TRIGGER IF EXISTS updateAccountBeforeInsert ON Account;
 DROP FUNCTION IF EXISTS updateAccountBeforeInsert();

 ALTER TABLE Account DROP COLUMN search_lastname;
 ALTER TABLE Account DROP COLUMN search_firstname;

 DROP EXTENSION IF EXISTS unaccent;
 DROP EXTENSION IF EXISTS pg_trgm;
 */

--changeset Kevin:7 labels:Mise à jour de la view StudentAccount et changement de nom de colonnes context:MàJ View => utilisé par BuilderStudent, MàJ Colonne => Coherence pour les hydrators

DROP VIEW StudentAccount;

ALTER TABLE GroupStudent RENAME COLUMN idGroupStudent TO groupId;
ALTER TABLE groupStudent RENAME COLUMN label TO groupLabel;

CREATE VIEW StudentAccount AS
SELECT a.idaccount as studentid,
       a.lastname,
       a.firstname,
       a.email,
       a.accounttype,
       a.search_firstname,
       a.search_lastname,
       s.studentnumber,
       g.*
FROM Account a
         JOIN Student s ON a.idaccount = s.idaccount
         JOIN groupstudent g ON s.idgroupstudent = g.groupId;

/* liquibase rollback
    DROP VIEW StudentAccount;

    ALTER TABLE GroupStudent RENAME COLUMN groupId TO idGroupStudent;
    ALTER TABLE groupStudent RENAME COLUMN groupLabel TO label;

    CREATE VIEW StudentAccount AS
    SELECT a.idaccount      as StudentID,
           a.lastname       as LastName,
           a.firstname      as FirstName,
           a.email          as Email,
           a.accounttype    as AccountType,
           s.studentnumber  as StudentNumber,
           g.idgroupstudent as GroupID,
           g.label          as GroupLabel
    FROM Account a
             JOIN Student s ON a.idaccount = s.idaccount
             JOIN groupstudent g ON s.idgroupstudent = g.idgroupstudent;
 */

--changeset Kevin:8 labels:add table tokenPassword context:Fonctionnalité mot de passe oublié
CREATE TABLE tokenPassword (
    idToken SERIAL PRIMARY KEY,
    token TEXT UNIQUE NOT NULL,
    expire TIMESTAMP DEFAULT now() + '1 hour',
    idAccount INT NOT NULL REFERENCES Account
);

-- rollback DROP TABLE tokenPassword;

--changeset Yann:9 labels:addComments context:ajout de la table commentaire

-- Ajout de la table comments
CREATE TABLE comments (
    idComment SERIAL PRIMARY KEY,
    textComment TEXT NOT NULL
);

/* liquibase rollback
   DROP TABLE comments;
 */

--changeset Isaac:10 labels:Rajout de la colonne groupe dans absence context:
alter table absence add column groupe text

/* liquibase rollback
alter table absence drop column groupe
*/

--changeset Yann:11 labels:AbsenceDays context:ajout tracking du nombre d'absence consécutives

-- Ajout d'une table pour les périodes de "congé"
CREATE TABLE offPeriod (
    id SERIAL PRIMARY KEY,
    startDate TIMESTAMP NOT NULL,
    endDate TIMESTAMP NOT NULL,
    label TEXT NOT NULL
);

-- Ajout d'une table pour la gestion des périodes d'absence des étudiants
CREATE TABLE studentPeriodAbs (
    idStudent INT REFERENCES student(idAccount),
    startDate TIMESTAMP,
    endDate TIMESTAMP,
    consecutiveDays INT NOT NULL DEFAULT 0,
    PRIMARY KEY (idStudent, startDate)
);

/* liquibase rollback
   DROP TABLE offPeriod;
   DROP TABLE studentPeriodAbs;
 */

--changeset Dimitri:12 labels:enumDS context:ajoute le DS
ALTER TYPE courseType ADD VALUE IF NOT EXISTS 'DS';

/* liquibase rollback
ALTER TABLE courseType DROP COLUMN DS
*/

--changeset Dimitri:13 labels:triggerDS context:met le DS en examen

CREATE OR REPLACE FUNCTION force_ds_exam()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.courseType = 'DS' THEN
        NEW.examen := TRUE;
END IF;

RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_ds_is_exam
    BEFORE INSERT OR UPDATE ON absence
                         FOR EACH ROW
                         EXECUTE FUNCTION force_ds_exam();

/* liquibase rollback
DROP TRIGGER IF EXISTS trigger_ds_is_exam ON absence;
DROP FUNCTION IF EXISTS force_ds_exam();
*/

--changeset Isaac:14 labels:mail context:stocke la volonté de recevoir des mails
create table mailAlertEducationManager(
    idMailAlert serial,
    activated boolean not null,
    idAccount int references Account(idAccount)
);

create table mailAlertTeacher(
    idMailAlert serial,
    activated boolean not null,
    idAccount int references Account(idAccount)
);

/* liquibase rollback
   drop table mailAlertEducationManager
   drop table mailAlertTeacher;
 */