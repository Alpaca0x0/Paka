mysql -u user -ppassword -e "DROP DATABASE AlpacaTech"
mysql -u user -ppassword -e "CREATE DATABASE AlpacaTech"
mysql -u user -ppassword AlpacaTech < account.sql
mysql -u user -ppassword AlpacaTech < post.sql
mysql -u user -ppassword AlpacaTech < post_event.sql
mysql -u user -ppassword AlpacaTech < reply.sql
pause('')