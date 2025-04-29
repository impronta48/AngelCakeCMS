alter table destinations
    add column created timestamp default now(),
    add column modified timestamp default now();
