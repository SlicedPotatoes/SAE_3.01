Drop table if exists groupe, student, teacher, state, courseType, resource, studentProof, file, absence cascade;

create table groupe
(
    idGroupe serial primary key,
    label   text not null
);

create table student
(
    idStudent  int primary key,
    lastName   text                           not null,
    firstName  text                           not null,
    firstName2 text,
    email      text unique                    not null check (email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+.[A-Za-z]{2,}$'),
    idGroupe    int references groupe (idGroupe) not null
);

create table teacher
(
    idTeacher serial primary key,
    lastName  text        not null,
    firstName text        not null,
    email     text unique not null check ( email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+.[A-Za-z]{2,}$' )
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

create table studentProof
(
    idStudentProof serial primary key,
    reason         text not null
);

create table file
(
    idFile         serial primary key,
    url            text                                         not null,
    idStudentProof int references studentProof (idStudentProof) not null
);

create table absence
(
    idAbsence      serial primary key,
    time           timestamp                                not null,
    examen         boolean                                  not null,
    idTeacher      int references teacher (idTeacher)       not null,
    idStudent      int references student (idStudent)       not null,
    idState        int references state (idState)           not null,
    idCourseType   int references courseType (idCourseType) not null,
    idResource     int references resource (idResource)     not null,
    idStudentProof int references studentProof (idStudentProof)
);
