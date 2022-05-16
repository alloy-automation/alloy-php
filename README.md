This is a basic wrapper package for sending events to the [Alloy](https://runalloy.com/) API. It exposes a single class function, `qm()`, that you can use across your Laravel controllers, models, and views.

## Installation

Run `composer require alloy-sdk/alloy` from your Laravel application root. Once that's finished, you'll need to open up your `.env` file and add the following to the bottom:

```php
ALLOY_API_KEY={your-api-key}
```

*Optionally:* You can publish the config file from the package by running:

```bash
php artisan vendor:publish --provider="Larahawk\Watcher\LarahawkServiceProvider"
```

## Usage
```
<?php
namespace App\Http\Controllers;
use Alloy\Client\Alloy;
use Illuminate\Http\Request;
class AlloyController extends Controller
{
   public function test(){
 
       $alloy = new Alloy("7a887212-692c-4c23-9d38-1f3120a6d043");
       return $alloy->event(workflowId:'625fa0ffc895e30013628d5d',    data:{},returnExecutionData:true);
   }
}
 ```

To send a single event in your application, use `qm()->event(workflowID, data, returnExecutionData)`. Name is a required string, value a required float, and dimension is an optional string that defaults to null.
 
If the returnExecutionData option is set, then runAlloy() will return an array of all block output. Be aware that the returnExecutionData flag can add a significant amount of latency, since the function will have to wait for the workflow to finish running.


