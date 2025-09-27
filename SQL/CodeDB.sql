--liquibase formatted sql

--changeset Louis:1 labels:Initialisation context:initialisation

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
    idStudent       int references student (idStudent) not null
);

create table file
(
    idFile          serial primary key,
    url             text                                           not null,
    idJustification int references justification (idJustification) not null
);

create table absence
(
    time                 timestamp                                not null,
    duration             interval                                 not null,
    examen               boolean                                  not null,
    allowedJustification boolean                                  not null,
    idTeacher            int references teacher (idTeacher)       not null,
    idStudent            int references student (idStudent)       not null,
    currentState         stateAbsence                             not null,
    courseType           courseType                               not null,
    idResource           int references resource (idResource)     not null,
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

--changeset Louis:2 labels:ajout-colonnes dans justification
alter table justification add column sendDate timestamp;
alter table justification add column processedDate timestamp;

/* liquibase rollback
    alter table justification drop column sendDate;
    alter table justification drop column processedDate;
 */

--changeset Louis:3 labels:drop colonne dans justification
alter table justification drop column idStudent;

--rollback alter table justification add column idStudent int references student (idStudent) not null;

--changeset Louis:4 labels:ajout-colonne dans absence
alter table absence add column dateResit timestamp;

--rollback alter table absence drop column dateResit;

--changeset Louis:5 labels:drop not null pour idTeacher dans absence
alter table absence alter column idTeacher drop not null;

--rollback alter table absence alter column idTeacher set not null;

--changeset Louis:6 labels:modif table file
alter table file rename column url to fileName;

--rollback alter table file rename column fileName to url;