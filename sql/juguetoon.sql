
create table usuarios (
    id          bigserial       constraint pk_socios primary key,
    numero      numeric(13)     not null constraint uq_socios_numero unique, 
    nick        varchar(100)    not null,
    password    char(32),
    admin       bool           not null default false
);

insert into socios(numero, nick, password, admin)
values  (1000, 'juan', md5('juan'), true),
        (1001, 'maria', md5('maria'), false);

drop table if exists articulos cascade;

create table articulos (
    id bigserial constraint constraint pk_articulos primary key,
    codigo char(13) not null constraint uq_articulos_codigo unique,
    nombre varchar(50),
    descripcion varchar(150),
    precio numeric(6,2) not null
);

insert into articulos(codigo, nombre, descripcion, precio)
values  (1000, 'Barco Pirata Playmobil', 'Piratas en los clics', 40.00),
        (1001, 'PlaStation 4', 'Consola de Sony', 350.00);
