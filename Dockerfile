FROM registry.access.redhat.com/ubi9/php-81

MAINTAINER sgi
LABEL maintainer="sgi"

USER 0
RUN echo "TLS_REQCERT never" >> /etc/openldap/ldap.conf

COPY /conf/www.conf /etc/php-fpm.d/

USER 1001

ADD src .
ADD media ./media

CMD /usr/libexec/s2i/run