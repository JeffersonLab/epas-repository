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

### Create a Permit Request
```php
use \Jlab\EpasRepository\Repository\ApplicationRepository;
$data = new Application([
    'RemoteRef' => 'ATLIS-20201-20220124125523',
    'Title' => 'A title for the permit application',
    'ResponsibilityGroupName' => 'Accelerator',
    'ApplicationTypeName' => 'Permit Request',
    'WorkOrderNumber' => 'ATLIS-20201',
]);
$repo = new ApplicationRepository();
$application = $repo->save($data);
print $application->title;
print $application->url();
```


### Retrieve Permit Applications by Work Order (ATLIS Task ID)
```php
use \Jlab\EpasRepository\Repository\ApplicationRepository;
$repo = new ApplicationRepository();
$applications = $repo->findByWorkOrder('ATLIS-20201');
print $applications->first()->title;
print $applications->first()->url();
```

### Retrieve Specific Permit Application by its RemoteRef

```php
use \Jlab\EpasRepository\Repository\ApplicationRepository;
$repo = new ApplicationRepository();
$applications = $repo->findByWorkOrder('ATLIS-20201');
$remoteRef = $applications->first()->remoteRef()  // ex: ATLIS-20201-20220124125523
$application = $repo->findByRemoteRef($remoteRef);
print $applications->first()->title;
print $applications->first()->url();
```

### Update a Permit Application's Title

```php
use \Jlab\EpasRepository\Repository\ApplicationRepository;
$repo = new ApplicationRepository();
$applications = $repo->findByWorkOrder('ATLIS-20201');
$application = $applications->first()
print $application->title;
$application->title = 'New Title'
$updated = $repo->update($application);
print $updated->title;
```

### Delete a Permit Application by its RemoteRef
```php
use \Jlab\EpasRepository\Repository\ApplicationRepository;
$repo = new ApplicationRepository();
$applications = $repo->findByWorkOrder('ATLIS-20201');
$remoteRef = $applications->first()->remoteRef()  // ex: ATLIS-20201-20220124125523
$repo->delete($remoteRef);  // returns true or throws
```


### Retrieve Permits by Work Order (ATLIS Task ID)
```php
use \Jlab\EpasRepository\Repository\PermitRepository;
$repo = new PermitRepository();
$permits = $repo->findByWorkOrder('ATLIS-20201');
print $permits->first()->title;
print $permits->first()->url();
```



## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

Note that in order to run integration tests, you must place a valid ePAS API token into the EPAS_API_AUTH_KEY environment variable before invoking phpunit.
If it is not set, then integration tests will be skipped and you will see a warning to that effect.
``` bash
$ composer install
$ vendor/bin/phpunit 
PHPUnit 9.5.4 by Sebastian Bergmann and contributors.

Runtime:       PHP 7.4.27
Configuration: /var/www/epas-repository/phpunit.xml

.....W                                                              6 / 6 (100%)

Time: 00:00.178, Memory: 8.00 MB

There was 1 warning:

There was 1 warning:

1) Jlab\EpasRepository\Tests\ApplicationRepositoryTest::test_it_creates_a_remote_permit_application
Integration test skipped because no EPAS_API_AUTH_KEY env was set

WARNINGS!
Tests: 6, Assertions: 13, Warnings: 1.

```

