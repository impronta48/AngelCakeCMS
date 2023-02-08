SELECT * FROM cyclomap_bikesquare_angelcake.poi where id=1618;
SELECT * FROM cyclomap_bikesquare_angelcake.i18n where foreign_key=1618;

##Estraggo tutti i poi non tradotti
select id from poi where id not in (select foreign_key from i18n where locale="ita" and model="Poi");

##Traduco tutti i titoli mancanti
insert into i18n (locale, model, foreign_key, field, content)
select "ita","Poi",id,"title",title
from poi 
where id in (select id from poi where id not in (select foreign_key from i18n where locale="ita" and model="Poi" and field="title")); 

insert into i18n (locale, model, foreign_key, field, content)
select "ita","Poi",id,"descr",descr
from poi 
where id in (select id from poi where id not in (select foreign_key from i18n where locale="ita" and model="Poi" and field="descr")); 