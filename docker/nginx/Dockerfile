# docker/nginx/Dockerfile
FROM nginx:1.25-alpine

# Remove a conf padrão do container para evitar conflitos
RUN rm -f /etc/nginx/conf.d/default.conf

# Copia a sua conf para o local esperado
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Opcional: checagem de sintaxe no build (falha o build se a conf estiver inválida)
RUN nginx -t || (cat /var/log/nginx/error.log && exit 1)
