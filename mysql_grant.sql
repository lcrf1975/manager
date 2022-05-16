grant all privileges on *.* to 'root'@'localhost';
grant all privileges on *.* to 'root'@'127.0.0.1';
grant all privileges on *.* to 'root'@'%';
set password for 'root'@'localhost' = password ('itbr366');
set password for 'root'@'127.0.0.1' = password ('itbr366');
set password for 'root'@'%' = password ('itbr366');
flush privileges;
