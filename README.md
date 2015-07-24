# RepoMan

Tools for managing source code in multiple repositories, provided as a service.

# Composer repository reporting tool

For a set of git repo uris which contain a composer.json and composer.lock file in the root directory, report on the dependencies across all the repositories and the versions installed with the lock files.

Steps to follow
* Add your authentication token to the service, if required to access the repositories

        curl -X POST /tokens "host name" "token string"
* Add each repository's url to the service 
 
        curl -X POST /repositories -d url="repository url"
* Update the local git repository checkouts from the remotes

        curl -X POST /repositories/update

* GET the report on composer dependencies (currently only serves text/csv resport)

        curl -X GET /reports/dependency/composer
or vist with your web browser
* List the repositories being managed as json

        curl -X GET /repositories
