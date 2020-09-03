FROM php:7.4-alpine

# Set working directory
WORKDIR /app

# Add user for php application
RUN set -x ; \
  addgroup -g 83 -S ahd; \
  adduser -u 83 -D -S -G ahd ahd && exit 0 ; exit 1

# Copy existing application directory contents
COPY . /app/Ui

# Copy existing application directory permissions
COPY --chown=ahd:ahd . /app

# Change current user to www
USER ahd



EXPOSE 8080