# PREPAGO BAGS PACKAGE v1.0.0


## Api routes

- Add prepago-bags routes in `routes/api.php`

    ```php
        
        use EmizorIpx\PrepagoBags\routes\PrepagoBags;
        ...

        PrepagoBags::routes();
    ```
## Append Bags in FelData

- Added method to insert bags in fel_data `App\Http\Controllers\BaseController` 


```php
        
        <?php


            namespace App\Http\Controllers;


            use EmizorIpx\PrepagoBags\Utils\Presenter as UtilsPresenter;

            class BaseController extends Controller
            {
                

                protected function response($response)
                {
                    ...

                    if ($index == 'none') {
                        ...
                    } else {
                        ...

                        
                        if (request()->include_fel_data) {
                            ...
                            $response = UtilsPresenter::appendBagsFelData($response);
                        }
                        
                    }

                    ...
                }
                
            }

```
