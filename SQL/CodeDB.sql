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

create table state
(
    idState serial primary key,
    label   text not null
);

create table courseType
(
    idCourseType serial primary key,
    label        text not null
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
    processed       boolean                            not null,
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
    duration interval not null,
    examen               boolean                                  not null,
    allowedJustification boolean                                  not null,
    idTeacher            int references teacher (idTeacher)       not null,
    idStudent            int references student (idStudent)       not null,
    idState              int references state (idState)           not null,
    idCourseType         int references courseType (idCourseType) not null,
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

--rollback drop table absence, file, absenceGroup, resource, courseType, state, teacher, student, groupStudent cascade;

--changelog Isaac:2 label:InsertionDansStates
insert into state(label)
values ('Validé'),
       ('Refusé'),
       ('Non-justifié'),
       ('En attente');

--rollback delete from state where(idState) between 1 and 5;

--changelog Isaac:3 label:InsertionDansCourseType
insert into courseType(label)
values ('TP'),
       ('CM'),
       ('TD'),
       ('BEN')

--rollback delete from coursesType where(idCourseType) between 1 and 4;

