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
        
        
# Format of report
        
        Library	  Version	Used By                                Configured Version	Last Updated
        SCEE/ABC  v1.0.0	https://github.com/SCEE/DEF:v1.2.2     1.*	                20/04/2015 11:40

1. **Library** : name of the library
2. **Version** : installed version of that library
3. **Used By** : name and version of the service or bundle depending on this library 
4. **Configured Version** : version specified in composer.json
5. **Last Updated** : installation date from the lock file.


* List the repositories being managed (as JSON)

        curl -X GET /repositories
