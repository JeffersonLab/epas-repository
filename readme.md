# EpasRepository

A package for retrieving and creating ePAS work permits and applications.

## Installation

Via Composer

``` bash
$ composer require jlab/epas-repository
```

## Environment/Config

Define the following environment variables in your laravel .env file

```
# Top Level URL where ePAS is hosted 
EPAS_WEB="https://epas-rk95-dev.staging.prometheusgroup.app"
# General Path to access a permit
EPAS_WEB_PERMIT="${EPAS_WEB}/Permit/General/Default.aspx"
# General Path to access a permit application
EPAS_WEB_APPLICATION="${EPAS_WEB}/Application/General/Default.aspx"
# Web Services API 
EPAS_WEB_SERVICES="${EPAS_WEB}/webservices"

# API Credentials obtained from ePAS server admin
EPAS_API_USER_NAME=####
EPAS_API_AUTH_KEY=###-####-####
```

## Usage

When users create a Permit Application in ePAS they are requested to enter a "Task Number" (AKA Work Order Number) in order to associate the application with a task or work order in an external system.  If the user has done so, the permit application or permit can be retrieved as follows:

### Retrieve Permit Applications by Work Order (ATLIS Task ID)
```php
use \Jlab\EpasRepository\Repository\ApplicationRepository;
$repo = ApplicationRepository();
$applications = $repo->findByWorkOrder('ATLIS-20201');
print $applications->first()->title;
print $applications->first()->url();
```

### Retrieve Permits by Work Order (ATLIS Task ID)
```php
use \Jlab\EpasRepository\Repository\PermitRepository;
$repo = PermitRepository();
$permits = $repo->findByWorkOrder('ATLIS-20201');
print $permits->first()->title;
print $permits->first()->url();
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer install
$ vendor/bin/phpunit 
```

