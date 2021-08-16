FROM debian:buster
LABEL maintainer="Simon<simon.stockhause (at} mni (dot} thm {dot] de>"

ENV DEBIAN_FRONTEND noninteractive
ENV XAMPP_URL=https://www.apachefriends.org/xampp-files/7.4.22/xampp-linux-x64-7.4.22-0-installer.run
ENV NODE_VERSION=16.6.1
# Set root password to root, format is 'user:password'.
RUN echo 'root:root' | chpasswd

# Install XAMPP PHP Database
RUN apt-get update --fix-missing \ 
  && apt-get upgrade -y \
  && apt-get -y install curl net-tools wget \ 
  && apt-get -y install git curl build-essential openssl libssl-dev \
  && apt-get -yq install openssh-server supervisor \ 
  && apt-get -y install nano vim less --no-install-recommends \ 
  &&  apt-get clean

RUN curl -Lo xampp-linux-installer.run ${XAMPP_URL} \
  &&  chmod +x xampp-linux-installer.run \ 
  && bash -c './xampp-linux-installer.run'\ 
  && ln -sf /opt/lampp/lampp /usr/bin/lampp \ 
  && ln -sf /opt/lampp/bin/php /usr/bin/php \ 
  && sed -i.bak s'/Require local/Require all granted/g' /opt/lampp/etc/extra/httpd-xampp.conf \ 
  && sed -i.bak s'/display_errors=Off/display_errors=On/g' /opt/lampp/etc/php.ini \ 
  && mkdir /opt/lampp/apache2/conf.d \ 
  && echo "IncludeOptional /opt/lampp/apache2/conf.d/*.conf" >> /opt/lampp/etc/httpd.conf \ 
  && mkdir /www \ 
  && ln -s /www /opt/lampp/htdocs \ 
  && mkdir -p /var/run/sshd \ 
  && sed -ri 's/#PermitRootLogin prohibit-password/PermitRootLogin yes/g' /etc/ssh/sshd_config 

# Install Composer
RUN wget -O composer-setup.php https://getcomposer.org/installer \ 
  && ./opt/lampp/bin/php composer-setup.php --install-dir=/usr/local/bin --filename=composer \ 
  && composer self-update

# Install NodeJS
RUN  curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.34.0/install.sh | bash \
 && chmod +x ./root/.nvm/nvm.sh \
 && ./root/.nvm/nvm.sh \
 &&  ./root/.nvm/nvm.sh install ${NODE_VERSION} \
 &&  ./root/.nvm/nvm.sh use v${NODE_VERSION} \
 &&  ./root/.nvm/nvm.sh alias default v${NODE_VERSION} 
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"

# Joomla
RUN  git clone https://github.com/joomla/joomla-cms /www/joomla \
 && cd /www/joomla \
 && git checkout 4.1-dev \
 && composer install  \
 && npm ci \
 && ln -sf /projects /opt/lampp/htdocs/projects

ADD bash_alias /bash_alias

RUN touch /root/.bashrc \ 
  && cat /bash_alias >> /root/.bashrc

# copy supervisor config file to start openssh-server
COPY supervisord-openssh-server.conf /etc/supervisor/conf.d/supervisord-openssh-server.conf

# copy a startup script
COPY startup.sh /startup.sh

# own files to webserver in order ot access them properly via xampp
RUN chown daemon:daemon -R /www

VOLUME [ "/var/log/mysql/", "/var/log/apache2/", "/www", "/opt/lampp/apache2/conf.d/" ]

EXPOSE 3306
EXPOSE 22
EXPOSE 80

CMD ["bash", "/startup.sh"]
