create database yeticave
    default character set utf8
    default collate utf8_general_ci;

use yeticave;

create table users (
    id int auto_increment primary key,
    date_reg timestamp default current_timestamp,
    email char(128) not null unique,
    name char(64),
    password char(128) not null ,
    avatar_img char(255),
    contact varchar(1024)
);

create table categories (
    id int auto_increment primary key,
    title char(128),
    symbol_code char(128)
);

create table lots (
    id int auto_increment primary key,
    id_category int not null,
    id_author int not null,
    id_winner int default null,
    date_reg timestamp default current_timestamp,
    title char(128),
    description varchar(4096),
    lot_img char(255),
    start_price decimal,
    bet_step decimal,
    stop_date date,
    foreign key (id_category) references categories(id),
    foreign key (id_author) references users(id),
    foreign key (id_winner) references users(id)
);

create table bets (
    id int auto_increment primary key,
    id_bettor int not null,
    id_lot int not null,
    bet_date timestamp default current_timestamp,
    bet_price decimal,
    foreign key (id_bettor) references users(id),
    foreign key (id_lot) references lots(id)
);