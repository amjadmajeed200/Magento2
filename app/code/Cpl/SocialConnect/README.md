# cpl-social-connect

### Installation

Dependencies:
 - cpl-core

With composer:

```sh
#For google library
$ composer require google/apiclient:"^2.0"
```

Manually:

Copy the zip into app/code/Cpl/SocialConnect directory


#### After installation by either means, enable the extension by running following commands:

```sh
$ php bin/magento module:enable Cpl_SocialConnect --clear-static-content
$ php bin/magento setup:upgrade
```

