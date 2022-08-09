# Karma8
# Installation

## Set Up Virtual Environment
1. Install Docker
2. Install Make
3. Create Application:
   * `make install`
4. Add hosts on your Local Machine:
   * `make add-hosts`
5. Open url http://karma8.dev

# Maintenance

## Docker

### Run Docker Containers
* `make up`

### Stop Docker Containers
* `make down`

### Stop Docker Containers
* `make down`

### Connect to docker server container
* `make sh-server`

### Connect to docker db container
* `make sh-db`

## Logs

### View Docker server Container Log
* `make log-server`

### View Docker db Container Log
* `make log-db`

### View PHP Log
* `make log-php`

### View MySQL All Queries Log
* `make log-mysql`

## Other

### Clean Application Files
* `make clean`