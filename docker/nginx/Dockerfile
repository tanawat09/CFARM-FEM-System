FROM nginx:alpine

# Copy custom Nginx configuration
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy application files (specifically the public folder)
COPY . /var/www

# Set appropriate permissions
RUN chown -R nginx:nginx /var/www/public
