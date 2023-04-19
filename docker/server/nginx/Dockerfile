FROM nginx:1.20

COPY config/orangehrm.conf /etc/nginx/conf.d/default.conf
COPY config/server.conf /etc/nginx/orangehrm-server.conf
COPY config/ssl.conf /etc/nginx/conf.d/ssl.conf
COPY certs/orangehrmdev.priv.cert /etc/ssl/certs/orangehrmdev.priv.cert
COPY certs/orangehrmdev.priv.key /etc/ssl/private/orangehrmdev.priv.key
COPY entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

CMD /entrypoint.sh
