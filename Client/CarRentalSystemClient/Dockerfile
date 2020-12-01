FROM php:7.3-apache-stretch

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
# Copy the Project to the container
ADD ./CarRentalSystemClient/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html/

# --------------
# Remove apt cache to reduce the image size
RUN apt-get clean

# --------------
# Expose the docker ports
EXPOSE 80
EXPOSE 443
