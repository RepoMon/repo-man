# RepoMan

Tools for managing source code in multiple repositories, provided as a service.

# Composer repository reporting tool

For a set of git repo uris which contain a composer.json and composer.lock file in the root directory, report on the dependencies across all the repositories and the versions installed with the lock files.

Steps to follow

* Add your authentication token for a git repository host to the service (if required to access its repositories)

        curl -X POST /tokens "host name" "token string"
        
* Add each repository's url to the service 
 
        curl -X POST /repositories -d url="repository url"
        
* Update the local git repository checkouts from the remotes

        curl -X POST /repositories/update

* GET the report on composer dependencies (default content-type is application/json)

        curl -X GET /reports/dependency/composer
        
* GET a HTML representation of the report
        
        curl -X GET /reports/dependency/composer -H "Accept: text/html"
        
* GET a CSV representation of the report

        curl -X GET /reports/dependency/composer -H "Accept: text/csv"

* List the repositories being managed (as JSON)

        curl -X GET /repositories

# Composer update dependencies tool

Update 1 or more required libraries in a repositories composer config.

        curl -X POST /dependencies/composer -d repository='url' -d require='{"lib/name":"version"}'