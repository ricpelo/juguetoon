drop table if exists usuarios cascade;

create table usuarios (
    id          bigserial       constraint pk_usuarios primary key,
    numero      numeric(13)     not null constraint uq_usuarios_numero unique, 
    nick        varchar(100)    not null,
    password    char(32),
    admin       bool            not null default false
);

insert into usuarios(numero, nick, password, admin)
values  (1, 'juan', md5('juan'), true),
        (2, 'maria', md5('maria'), false);