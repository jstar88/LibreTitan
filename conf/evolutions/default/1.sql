# --- Created by Ebean DDL
# To stop Ebean DDL generation, remove this comment and start using Evolutions

# --- !Ups

create table celestial_objects (
  id                        bigint not null,
  user_id                   bigint,
  constraint pk_celestial_objects primary key (id))
;

create table roles (
  id                        integer not null,
  constraint pk_roles primary key (id))
;

create table users (
  id                        bigint not null,
  type                      integer,
  skin                      varchar(255),
  email                     varchar(255),
  name                      varchar(255),
  password                  varchar(255),
  home_planet_id            bigint,
  constraint pk_users primary key (id))
;


create table roles_users (
  roles_id                       integer not null,
  users_id                       bigint not null,
  constraint pk_roles_users primary key (roles_id, users_id))
;

create table users_roles (
  users_id                       bigint not null,
  roles_id                       integer not null,
  constraint pk_users_roles primary key (users_id, roles_id))
;
create sequence celestial_objects_seq;

create sequence roles_seq;

create sequence users_seq;

alter table celestial_objects add constraint fk_celestial_objects_user_1 foreign key (user_id) references users (id) on delete restrict on update restrict;
create index ix_celestial_objects_user_1 on celestial_objects (user_id);
alter table users add constraint fk_users_homePlanet_2 foreign key (home_planet_id) references celestial_objects (id) on delete restrict on update restrict;
create index ix_users_homePlanet_2 on users (home_planet_id);



alter table roles_users add constraint fk_roles_users_roles_01 foreign key (roles_id) references roles (id) on delete restrict on update restrict;

alter table roles_users add constraint fk_roles_users_users_02 foreign key (users_id) references users (id) on delete restrict on update restrict;

alter table users_roles add constraint fk_users_roles_users_01 foreign key (users_id) references users (id) on delete restrict on update restrict;

alter table users_roles add constraint fk_users_roles_roles_02 foreign key (roles_id) references roles (id) on delete restrict on update restrict;

# --- !Downs

SET REFERENTIAL_INTEGRITY FALSE;

drop table if exists celestial_objects;

drop table if exists roles;

drop table if exists roles_users;

drop table if exists users;

drop table if exists users_roles;

SET REFERENTIAL_INTEGRITY TRUE;

drop sequence if exists celestial_objects_seq;

drop sequence if exists roles_seq;

drop sequence if exists users_seq;

