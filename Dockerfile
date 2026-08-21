FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite headers

# Configure Apache listening ports for Render (overwrite to prevent duplicate Listen 80)
RUN printf "Listen 10000\nListen 80\n" > /etc/apache2/ports.conf

# Configure VirtualHost for all ports
RUN printf "<VirtualHost *:80 *:10000 *:8080>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html\n\
    DirectoryIndex index.php index.html\n\
\n\
    <Directory /var/www/html>\n\
        Options -Indexes +FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
\n\
    ErrorLog /var/log/apache2/error.log\n\
    CustomLog /var/log/apache2/access.log combined\n\
</VirtualHost>\n" > /etc/apache2/sites-available/000-default.conf

# Copy project files
COPY . /var/www/html/

# Create case-insensitive aliases dynamically for all subprojects
RUN cd /var/www/html/projects && \
    for d in */; do \
        [ -d "$d" ] || continue; \
        name="${d%/}"; \
        lower="$(echo "$name" | tr '[:upper:]' '[:lower:]')"; \
        if [ "$name" != "$lower" ] && [ ! -e "$lower" ]; then \
            ln -sf "$name" "$lower"; \
        fi; \
    done && \
    ln -sf HieuWeb04 DatCyber 2>/dev/null || true && \
    ln -sf HieuWeb06 HieuMini 2>/dev/null || true


# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80 10000

CMD ["apache2-foreground"]
