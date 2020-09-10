FROM nginx:alpine

COPY default.conf /etc/nginx/conf.d/

# Set timezone
RUN ln -s /usr/share/zoneinfo/Europe/Paris /etc/localtime
RUN "date"
