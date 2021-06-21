--- This is temporary file for training

create table `ohrm_todo`
(
    `id`          int          not null auto_increment,
    `name`        varchar(100) not null,
    `description` varchar(255) default null,
    `date`        datetime     not null,
    primary key (`id`)
) engine=innodb default charset=utf8;
