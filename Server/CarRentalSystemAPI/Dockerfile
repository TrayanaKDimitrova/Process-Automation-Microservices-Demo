FROM php:7.3-apache-stretch

# --------------
# Install some base extensions
RUN apt-get update -y && \
    apt-get install -y libzip-dev zip && \
    apt-get install -y zlib1g-dev libicu-dev g++ && \
    apt-get install -y netcat

# --------------
# Install PHP modules
RUN docker-php-ext-install mysqli && \
    docker-php-ext-install pcntl && \
    docker-php-ext-configure zip --with-libzip && \
    docker-php-ext-install zip && \
    docker-php-ext-install bcmath

# --------------
# Install Postgre/MySQL PDO/SQL PDO
RUN apt-get install -y libpq-dev && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql && \
    apt-get install -y unixodbc-dev && \
	pecl install sqlsrv pdo_sqlsrv

# --------------
# Install PHP GD modules
RUN apt-get update -y && \
    apt-get install -y libpng-dev zlib1g-dev && \
    docker-php-ext-install gd

# --------------
# Install SSL and enable https
RUN apt-get update && \
	apt-get install ssl-cert

RUN a2enmod rewrite
RUN a2enmod ssl

# --------------
# Setup the apache2 sites
RUN a2ensite 000-default.conf
RUN a2ensite default-ssl.conf

# --------------
# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && \
	mv composer.phar /usr/local/bin/composer && \
	chmod +x /usr/local/bin/composer && \
	composer self-update

# --------------
# Change the user ID and the group ID of the `www-data` user
ARG USER_ID
ARG GROUP_ID

RUN if [ ${USER_ID:-0} -ne 0 ] && [ ${GROUP_ID:-0} -ne 0 ]; then \
	userdel -f www-data && \
	if getent group www-data; then groupdel www-data ;fi && \
	groupadd --system -g ${GROUP_ID} www-data && \
	useradd --system -l -u ${USER_ID} -g www-data www-data && \
	install -d -m 0755 -o www-data -g www-data /home/www-data && \
	chown \
		--changes \
		--silent \
		--no-dereference \
		--recursive \
		${USER_ID}:${GROUP_ID} \
		/home/www-data \
		/var/lock/apache2 \
		/var/run/apache2 \
		/usr/local/bin/composer && \
	echo 'www-data ALL=(ALL:ALL) ALL' >> /etc/sudoers \
;fi

# --------------
# Copy the Project to the container

# ADD ./CarRentalSystemAPI/ /var/www/html/
# RUN chown -R www-data:www-data /var/www/html/

# --------------
# Expose the docker ports
EXPOSE 80
EXPOSE 443
