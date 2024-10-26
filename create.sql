
drop schema if exists sdp cascade;

create schema if not exists sdp;

create table if not exists sdp.rooms (
    room_id uuid primary key,
    type text,
    price numeric
);

insert into sdp.rooms (room_id, type, price) values
('aa354842-59bf-42e6-be3a-6188dbb5fff8', 'day', 1000),
('d5f5c6cb-bf69-4743-a288-dafed2517e38', 'hour', 100);

create table if not exists sdp.reservations (
    reservation_id uuid primary key,
    room_id uuid,
    email text,
    checkin_date timestamp,
    checkout_date timestamp,
    price numeric,
    status text
);
