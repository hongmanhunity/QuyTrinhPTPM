FROM php:8.0-apache

# Cài đặt extension PDO MySQL để PHP có thể kết nối với Database
RUN docker-php-ext-install pdo pdo_mysql

# Bật mod_rewrite của Apache (hữu ích cho việc định tuyến nếu cần)
RUN a2enmod rewrite
