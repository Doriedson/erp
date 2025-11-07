sudo docker compose exec app composer dump-autoload -o
sudo docker compose exec web sh -lc "nginx -t && nginx -s reload"
