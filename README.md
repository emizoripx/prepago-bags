# PREPAGO BAGS PACKAGE v1.2.38

## Library to manage prepago-bags in emizor5

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


## Added exception in shop

- Added exception in invoice emit in shop `App\Http\Controllers\Shop\InvoiceController.php`

```php
        
        <?php


            namespace App\Http\Controllers;



            class InvoiceController extends BaseController
            {
                ...
                    // EMIZOR-INVOICE-INSERT
                    try {
                        $invoice->emit();
                    } catch (Exception $ex) {
                        return response(['message' => $ex->getMessage()]);;
                    }
                ...
                }
            }

```