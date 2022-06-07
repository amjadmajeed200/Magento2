# README #

This README would normally document whatever steps are necessary to get your application up and running.

### What is this repository for? ###

* Quick summary
* Version
* [Learn Markdown](https://bitbucket.org/tutorials/markdowndemo)

### How do I get set up? ###

* Summary of set up
* Clone setup using git repo:https://bitbucket.org/accunityllc/cm-trifecta-magento/src
* Configure core_config_data database table w.r.t environment 
* Run Composer install for dependencies  
* Update env.php w.r.t environment
* Run commands for deployment instructions:
  
1. php bin/magento setup:upgrade (Update the database schema)
2. php bin/magento setup:di:compile (Application code generation)
3. php bin/magento setup:static-content:deploy (This will generate the static assets under pub/static)
4. php bin/magento indexer:reindex (Use this command to reindex all or selected indexers one time only)
5. php bin/magento cache:flush (Flush cache storage)


### Who do I talk to? ###

* Repo owner or admin
* Other community or team contact
