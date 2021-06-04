<?php

return [
    'webApplication'=> env('EPAS_WEB_APPLICATION'),     // construct URL to permit application
    'webPermit'=> env('EPAS_WEB_PERMIT'),               // construct URL to permit
    'webServices' => env('EPAS_WEB_SERVICES'),          // construct URL to integration API
    'userName' => env('EPAS_API_USER_NAME'),            // required for API calls
    'authKey' => env('EPAS_API_AUTH_KEY'),              // required for API calls

    // alternative wsdl for AddApplication with bogus minOccurs="1" removed.
    'applicationWsdl' => env('APPLICATION_WSDL','http://localhost/epas/ApplicationWebService.asmx.xml'),
];
