-- B.D TIENDA JUGUETES(JUGUETOON) --

drop table if exists usuarios cascade;

create table usuarios (
    id          bigserial       constraint pk_usuarios primary key,
    numero      numeric(13)     not null constraint uq_usuarios_numero unique, 
    nick        varchar(100)    not null,
    password    char(32),
    admin       bool            not null default false
);

drop table if exists articulos cascade;

create table articulos (
    id 		bigserial 	constraint pk_articulos primary key,
    codigo 	char(13) 	not null constraint uq_articulos_codigo unique,
    nombre 	varchar(50),
    descripcion varchar(150),
    precio 	numeric(6,2) 	not null,
    borrado 	bool 		not null default false,
    existencias int 		default 0
);

drop table if exists pedidos cascade; 

create table pedidos(
	id 		bigserial 	constraint pk_pedidos primary key,
	numero		numeric(8,0)	not null constraint uq_pedidos_codigo unique,
	fecha 		date		not null default CURRENT_DATE
);


insert into articulos(codigo, nombre, descripcion, precio,existencias)
values  (1000, 'Barco Pirata Playmobil', 'Piratas en los clics', 40.00,10),
        (1001, 'PlaStation 4', 'Consola de Sony', 350.00,30);


insert into usuarios(numero, nick, password, admin)
values  (1, 'juan', md5('juan'), true),
        (2, 'maria', md5('maria'), false);

drop view if exists v_articulos;

create view v_articulos as
select id, codigo, nombre, descripcion, precio,
       trim(to_char(precio, '9999D99 L')) as precio_format,
       existencias
from articulos
where borrado = false;
