<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
|--------------------------------------------------------------------------
| Recurring invoices cron route
|--------------------------------------------------------------------------
*/

Route::get('qQdyjfNPOwBDj8L', ['uses' => 'InvoiceController@createRecurringInvoices']);

/*
|--------------------------------------------------------------------------
| Authentication routes
|--------------------------------------------------------------------------
*/

Route::get('/', ['as' => 'LoginPage', 'middleware' => 'login', 'uses' => 'AuthController@getLoginPage']);

Route::group(['prefix' => 'auth'], function() {

    Route::group(['middleware' => 'login'], function () {

        Route::post('login/user', ['as' => 'LoginUser', 'uses' => 'AuthController@loginUser']);

        Route::get('register', ['as' => 'RegisterPage', 'uses' => 'AuthController@getRegisterPage']);

        Route::post('register/user', ['as' => 'RegisterUser', 'uses' => 'AuthController@registerUser']);

        Route::get('confirm/{token}', ['uses' => 'AuthController@confirmAccount']);
    });

    Route::get('logout/user', ['as' => 'LogoutUser', 'uses' => 'AuthController@logoutUser']);
});

Route::group(['middleware' => 'authentication'], function() {

    /*
    |--------------------------------------------------------------------------
    | Super admin routes
    |--------------------------------------------------------------------------
    */

    Route::group(['prefix' => 'superadmin', 'middleware' => 'superAdmin'], function() {

        Route::get('statistics', ['as' => 'SuperAdminStatistics', 'uses' => 'SuperAdminController@getStatistics']);

        Route::get('companies', ['as' => 'SuperAdminGetCompanies', 'uses' => 'SuperAdminController@getCompanies']);

        //ajax route
        Route::post('updateLicence', ['uses' => 'SuperAdminController@updateLicence']);

        Route::get('users', ['as' => 'SuperAdminGetUsers', 'uses' => 'SuperAdminController@getUsers']);

        Route::group(['prefix' => 'user'], function() {

            Route::get('login/{id}', ['as' => 'SuperAdminLoginAsUser', 'uses' => 'SuperAdminController@loginAsUser']);

            Route::get('deactivate/{id}', ['as' => 'DeactivateUser', 'uses' => 'SuperAdminController@deactivateUser']);

            Route::get('activate/{id}', ['as' => 'ActivateUser', 'uses' => 'SuperAdminController@activateUser']);
        });

        Route::group(['prefix' => 'subscribers'], function() {

            Route::get('monthly', ['as' => 'GetMonthlySubscribers', 'uses' => 'SuperAdminController@getMonthlySubscribers']);

            Route::get('annual', ['as' => 'GetAnnualSubscribers', 'uses' => 'SuperAdminController@getAnnualSubscribers']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Admin routes
    |--------------------------------------------------------------------------
    */

    Route::group(['middleware' => 'admin'], function() {

        Route::get('statistics', ['as' => 'AdminStatistics', 'uses' => 'AdminController@getStatistics']);

        Route::get('company/info', ['as' => 'CompanyInfo', 'uses' => 'AdminController@getCompanyInfo']);

        Route::post('company/update', ['as' => 'UpdateCompanyInfo', 'uses' => 'AdminController@updateCompanyInfo']);

        Route::post('uploadLogo', ['as' => 'UploadLogo', 'uses' => 'AdminController@uploadLogo']);
    });

    /*
    |--------------------------------------------------------------------------
    |--------------------------------------------------------------------------
    */

    Route::get('returnToSuperAdmin', ['as' => 'ReturnToSuperAdmin', 'uses' => 'SuperAdminController@returnToSuperAdmin']);

    Route::get('licence/info', ['as' => 'GetLicenceInfo', 'uses' => 'AuthController@getLicenceInfo']);

    /*
    |--------------------------------------------------------------------------
    | Documents routes
    |--------------------------------------------------------------------------
    */

    Route::group(['prefix' => 'docs'], function() {

        /*
        |--------------------------------------------------------------------------
        | Offers routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'offers'], function() {

            Route::get('list', ['as' => 'GetOffers', 'uses' => 'OfferController@getOffers']);

            Route::get('add', ['as' => 'AddOffer', 'uses' => 'OfferController@addOffer']);

            //ajax route
            Route::post('insert', ['uses' => 'OfferController@insertOffer']);

            Route::get('edit/{id}', ['as' => 'EditOffer', 'uses' => 'OfferController@editOffer']);

            //ajax route
            Route::post('update', ['uses' => 'OfferController@updateOffer']);

            Route::get('delete/{id}', ['as' => 'DeleteOffer', 'uses' => 'OfferController@deleteOffer']);

            Route::get('copy/{id}', ['as' => 'CopyOffer', 'uses' => 'OfferController@copyOffer']);

            Route::get('pdf/{type}/{id}', ['as' => 'PDFOffer', 'uses' => 'OfferController@pdfOffer']);

            //ajax route
            Route::post('getProducts', ['uses' => 'OfferController@getProducts']);

            //ajax route
            Route::post('sendEmail', ['uses' => 'OfferController@sendEmail']);
        });

        /*
        |--------------------------------------------------------------------------
        | Invoices routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'invoices'], function() {

            Route::get('{type}/list', ['as' => 'GetInvoices', 'uses' => 'InvoiceController@getInvoices']);

            Route::get('{type}/add', ['as' => 'AddInvoice', 'uses' => 'InvoiceController@addInvoice']);

            //ajax route
            Route::post('insert', ['uses' => 'InvoiceController@insertInvoice']);

            Route::get('edit/{id}', ['as' => 'EditInvoice', 'uses' => 'InvoiceController@editInvoice']);

            //ajax route
            Route::post('update', ['uses' => 'InvoiceController@updateInvoice']);

            Route::get('delete/{id}', ['as' => 'DeleteInvoice', 'uses' => 'InvoiceController@deleteInvoice']);

            Route::get('copy/{id}', ['as' => 'CopyInvoice', 'uses' => 'InvoiceController@copyInvoice']);

            Route::get('reverse/{type}/{id}', ['as' => 'ReverseInvoice', 'uses' => 'InvoiceController@reverseInvoice']);

            Route::get('pdf/{type}/{id}', ['as' => 'PDFInvoice', 'uses' => 'InvoiceController@pdfInvoice']);

            //ajax route
            Route::post('getProducts', ['uses' => 'InvoiceController@getProducts']);

            //ajax route
            Route::post('fiscalization', ['uses' => 'InvoiceController@fiscalization']);

            //ajax route
            Route::post('sendEmail', ['uses' => 'InvoiceController@sendEmail']);
        });

        /*
        |--------------------------------------------------------------------------
        | Dispatches routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'dispatches'], function() {

            Route::get('list', ['as' => 'GetDispatches', 'uses' => 'DispatchController@getDispatches']);

            Route::get('add', ['as' => 'AddDispatch', 'uses' => 'DispatchController@addDispatch']);

            //ajax route
            Route::post('insert', ['uses' => 'DispatchController@insertDispatch']);

            Route::get('edit/{id}', ['as' => 'EditDispatch', 'uses' => 'DispatchController@editDispatch']);

            //ajax route
            Route::post('update', ['uses' => 'DispatchController@updateDispatch']);

            Route::get('delete/{id}', ['as' => 'DeleteDispatch', 'uses' => 'DispatchController@deleteDispatch']);

            Route::get('pdf/{id}', ['as' => 'PDFDispatch', 'uses' => 'DispatchController@pdfDispatch']);

            //ajax route
            Route::post('getProducts', ['uses' => 'DispatchController@getProducts']);
        });

        /*
        |--------------------------------------------------------------------------
        | Contracts routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'contracts'], function() {

            Route::get('list', ['as' => 'GetContracts', 'uses' => 'ContractController@getContracts']);

            Route::get('add', ['as' => 'AddContract', 'uses' => 'ContractController@addContract']);

            //ajax route
            Route::post('insert', ['uses' => 'ContractController@insertContract']);

            Route::get('edit/{id}', ['as' => 'EditContract', 'uses' => 'ContractController@editContract']);

            //ajax route
            Route::post('update', ['uses' => 'ContractController@updateContract']);

            Route::get('delete/{id}', ['as' => 'DeleteContract', 'uses' => 'ContractController@deleteContract']);

            Route::get('copy/{id}', ['as' => 'CopyContract', 'uses' => 'ContractController@copyContract']);

            //ajax route
            Route::post('getProducts', ['uses' => 'ContractController@getProducts']);
        });

        /*
        |--------------------------------------------------------------------------
        | Order forms routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'orderForms'], function() {

            Route::get('list', ['as' => 'GetOrderForms', 'uses' => 'OrderFormController@getOrderForms']);

            Route::get('add', ['as' => 'AddOrderForm', 'uses' => 'OrderFormController@addOrderForm']);

            //ajax route
            Route::post('insert', ['uses' => 'OrderFormController@insertOrderForm']);

            Route::get('edit/{id}', ['as' => 'EditOrderForm', 'uses' => 'OrderFormController@editOrderForm']);

            //ajax route
            Route::post('update', ['uses' => 'OrderFormController@updateOrderForm']);

            Route::get('delete/{id}', ['as' => 'DeleteOrderForm', 'uses' => 'OrderFormController@deleteOrderForm']);

            Route::get('pdf/{id}', ['as' => 'PDFOrderForm', 'uses' => 'OrderFormController@pdfOrderForm']);

            //ajax route
            Route::post('getProducts', ['uses' => 'OrderFormController@getProducts']);
        });

        /*
        |--------------------------------------------------------------------------
        | Notes routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'notes'], function() {

            Route::get('list', ['as' => 'GetNotes', 'uses' => 'NoteController@getNotes']);

            Route::get('add', ['as' => 'AddNote', 'uses' => 'NoteController@addNote']);

            Route::post('insert', ['as' => 'InsertNote', 'uses' => 'NoteController@insertNote']);

            Route::get('edit/{id}', ['as' => 'EditNote', 'uses' => 'NoteController@editNote']);

            Route::post('update', ['as' => 'UpdateNote', 'uses' => 'NoteController@updateNote']);

            Route::get('delete/{id}', ['as' => 'DeleteNote', 'uses' => 'NoteController@deleteNote']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Register reports routes
    |--------------------------------------------------------------------------
    */

    Route::group(['prefix' => 'registerReports'], function() {

        /*
        |--------------------------------------------------------------------------
        | Payment slips routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'paymentSlips'], function() {

            Route::get('list', ['as' => 'GetPaymentSlips', 'uses' => 'RegisterReportController@getPaymentSlips']);

            Route::get('add', ['as' => 'AddPaymentSlip', 'uses' => 'RegisterReportController@addPaymentSlip']);

            //ajax route
            Route::post('insert', ['uses' => 'RegisterReportController@insertPaymentSlip']);

            Route::get('delete/{id}', ['as' => 'DeletePaymentSlip', 'uses' => 'RegisterReportController@deletePaymentSlip']);

            Route::get('pdf/{id}', ['as' => 'PDFPaymentSlip', 'uses' => 'RegisterReportController@pdfPaymentSlip']);
        });

        /*
        |--------------------------------------------------------------------------
        | Payout slips routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'payoutSlips'], function() {

            Route::get('list', ['as' => 'GetPayoutSlips', 'uses' => 'RegisterReportController@getPayoutSlips']);

            Route::get('add', ['as' => 'AddPayoutSlip', 'uses' => 'RegisterReportController@addPayoutSlip']);

            //ajax route
            Route::post('insert', ['uses' => 'RegisterReportController@insertPayoutSlip']);

            Route::get('edit/{id}', ['as' => 'EditPayoutSlip', 'uses' => 'RegisterReportController@editPayoutSlip']);

            //ajax route
            Route::post('update', ['uses' => 'RegisterReportController@updatePayoutSlip']);

            Route::get('delete/{id}', ['as' => 'DeletePayoutSlip', 'uses' => 'RegisterReportController@deletePayoutSlip']);

            Route::get('pdf/{id}', ['as' => 'PDFPayoutSlip', 'uses' => 'RegisterReportController@pdfPayoutSlip']);

            //ajax route
            Route::post('deleteItem', ['uses' => 'RegisterReportController@deletePayoutSlipItem']);
        });

        /*
        |--------------------------------------------------------------------------
        | Reports routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'reports'], function() {

            Route::get('list', ['as' => 'GetRegisterReports', 'uses' => 'RegisterReportController@getRegisterReports']);

            //ajax route
            Route::post('insert', ['uses' => 'RegisterReportController@insertRegisterReport']);

            Route::get('preview/{id}', ['as' => 'PreviewRegisterReport',
                'uses' => 'RegisterReportController@previewRegisterReport']);

            Route::get('delete/{id}', ['as' => 'DeleteRegisterReport', 'uses' => 'RegisterReportController@deleteRegisterReport']);

            Route::get('pdf/{id}/{items?}', ['as' => 'PDFRegisterReport', 'uses' => 'RegisterReportController@pdfRegisterReport']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Travel warrants routes
    |--------------------------------------------------------------------------
    */

    Route::group(['prefix' => 'travelWarrants', 'middleware' => 'admin'], function() {

        /*
        |--------------------------------------------------------------------------
        | Vehicles routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'vehicles'], function() {

            Route::get('list', ['as' => 'GetVehicles', 'uses' => 'TravelWarrantController@getVehicles']);

            Route::get('add', ['as' => 'AddVehicle', 'uses' => 'TravelWarrantController@addVehicle']);

            Route::post('insert', ['as' => 'InsertVehicle', 'uses' => 'TravelWarrantController@insertVehicle']);

            Route::get('edit/{id}', ['as' => 'EditVehicle', 'uses' => 'TravelWarrantController@editVehicle']);

            Route::post('update', ['as' => 'UpdateVehicle', 'uses' => 'TravelWarrantController@updateVehicle']);

            Route::get('delete/{id}', ['as' => 'DeleteVehicle', 'uses' => 'TravelWarrantController@deleteVehicle']);
        });

        /*
        |--------------------------------------------------------------------------
        | Wages routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'wages'], function() {

            Route::get('list', ['as' => 'GetWages', 'uses' => 'TravelWarrantController@getWages']);

            Route::get('add', ['as' => 'AddWage', 'uses' => 'TravelWarrantController@addWage']);

            Route::post('insert', ['as' => 'InsertWage', 'uses' => 'TravelWarrantController@insertWage']);

            Route::get('edit/{id}', ['as' => 'EditWage', 'uses' => 'TravelWarrantController@editWage']);

            Route::post('update', ['as' => 'UpdateWage', 'uses' => 'TravelWarrantController@updateWage']);

            Route::get('delete/{id}', ['as' => 'DeleteWage', 'uses' => 'TravelWarrantController@deleteWage']);
        });

        /*
        |--------------------------------------------------------------------------
        | Warrants routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'warrants'], function() {

            Route::get('list', ['as' => 'GetTravelWarrants', 'uses' => 'TravelWarrantController@getTravelWarrants']);

            Route::get('add', ['as' => 'AddTravelWarrant', 'uses' => 'TravelWarrantController@addTravelWarrant']);

            //ajax route
            Route::post('insert', ['uses' => 'TravelWarrantController@insertTravelWarrant']);

            Route::get('edit/{id}', ['as' => 'EditTravelWarrant', 'uses' => 'TravelWarrantController@editTravelWarrant']);

            //ajax route
            Route::post('update', ['uses' => 'TravelWarrantController@updateTravelWarrant']);

            Route::get('delete/{id}', ['as' => 'DeleteTravelWarrant', 'uses' => 'TravelWarrantController@deleteTravelWarrant']);

            Route::get('copy/{id}', array('as' => 'CopyTravelWarrant', 'uses' => 'TravelWarrantController@copyTravelWarrant'));

            Route::get('pdf/{id}', ['as' => 'PDFTravelWarrant', 'uses' => 'TravelWarrantController@pdfTravelWarrant']);
        });

        //ajax route
        Route::post('calculateDistance', ['uses' => 'TravelWarrantController@calculateDistance']);

        //ajax route
        Route::post('deleteItem', ['uses' => 'TravelWarrantController@deleteItem']);
    });

    /*
    |--------------------------------------------------------------------------
    | Clients routes
    |--------------------------------------------------------------------------
    */

    Route::group(['prefix' => 'clients'], function() {

        Route::get('list', ['as' => 'GetClients', 'uses' => 'ClientController@getClients']);

        Route::get('add', ['as' => 'AddClient', 'uses' => 'ClientController@addClient']);

        //ajax route
        Route::post('insert', ['uses' => 'ClientController@insertClient']);

        Route::get('edit/{id}', ['as' => 'EditClient', 'uses' => 'ClientController@editClient']);

        //ajax route
        Route::post('update', ['uses' => 'ClientController@updateClient']);

        Route::get('delete/{id}', ['as' => 'DeleteClient', 'uses' => 'ClientController@deleteClient']);

        //ajax route
        Route::post('search', ['uses' => 'ClientController@searchClients']);

        Route::get('invoices/{id}', ['as' => 'ClientInvoices', 'uses' => 'ClientController@getClientInvoices']);

        //ajax route
        Route::post('insert/clientPrice', ['uses' => 'ClientController@insertClientPrice']);

        //ajax route
        Route::post('delete/clientPrice', ['uses' => 'ClientController@deleteClientPrice']);
    });

    /*
    |--------------------------------------------------------------------------
    | Categories routes
    |--------------------------------------------------------------------------
    */

    Route::group(['prefix' => 'categories'], function() {

        Route::get('list', ['as' => 'GetCategories', 'uses' => 'ProductController@getCategories']);

        Route::get('add', ['as' => 'AddCategory', 'uses' => 'ProductController@addCategory']);

        Route::post('insert', ['as' => 'InsertCategory', 'uses' => 'ProductController@insertCategory']);

        Route::get('edit/{id}', ['as' => 'EditCategory', 'uses' => 'ProductController@editCategory']);

        Route::post('update', ['as' => 'UpdateCategory', 'uses' => 'ProductController@updateCategory']);

        Route::get('delete/{id}', ['as' => 'DeleteCategory', 'uses' => 'ProductController@deleteCategory']);

        //ajax route
        Route::get('selectList', ['uses' => 'ProductController@getCategoriesSelect']);
    });

    /*
    |--------------------------------------------------------------------------
    | Products routes
    |--------------------------------------------------------------------------
    */

    Route::group(['prefix' => 'products'], function() {

        Route::get('list', ['as' => 'GetProducts', 'uses' => 'ProductController@getProducts']);

        Route::get('add', ['as' => 'AddProduct', 'uses' => 'ProductController@addProduct']);

        Route::post('insert', ['as' => 'InsertProduct', 'uses' => 'ProductController@insertProduct']);

        Route::get('edit/{id}', ['as' => 'EditProduct', 'uses' => 'ProductController@editProduct']);

        Route::post('update', ['as' => 'UpdateProduct', 'uses' => 'ProductController@updateProduct']);

        Route::get('delete/{id}', ['as' => 'DeleteProduct', 'uses' => 'ProductController@deleteProduct']);

        //ajax route
        Route::post('search', ['uses' => 'ProductController@searchProducts']);

        //ajax route
        Route::post('checkMerchandise', ['uses' => 'ProductController@checkMerchandise']);
    });

    /*
    |--------------------------------------------------------------------------
    | Settings routes
    |--------------------------------------------------------------------------
    */

    Route::group(['prefix' => 'settings', 'middleware' => 'admin'], function() {

        /*
        |--------------------------------------------------------------------------
        | Users routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'users'], function() {

            Route::get('list', ['as' => 'GetUsers', 'uses' => 'UserController@getUsers']);

            Route::get('add', ['as' => 'AddUser', 'uses' => 'UserController@addUser']);

            Route::post('insert', ['as' => 'InsertUser', 'uses' => 'UserController@insertUser']);

            Route::get('edit/{id}', ['as' => 'EditUser', 'uses' => 'UserController@editUser']);

            Route::post('update', ['as' => 'UpdateUser', 'uses' => 'UserController@updateUser']);

            Route::get('delete/{id}', ['as' => 'DeleteUser', 'uses' => 'UserController@deleteUser']);
        });

        /*
        |--------------------------------------------------------------------------
        | Employees routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'employees'], function() {

            Route::get('list', ['as' => 'GetEmployees', 'uses' => 'UserController@getEmployees']);

            Route::get('add', ['as' => 'AddEmployee', 'uses' => 'UserController@addEmployee']);

            Route::post('insert', ['as' => 'InsertEmployee', 'uses' => 'UserController@insertEmployee']);

            Route::get('edit/{id}', ['as' => 'EditEmployee', 'uses' => 'UserController@editEmployee']);

            Route::post('update', ['as' => 'UpdateEmployee', 'uses' => 'UserController@updateEmployee']);

            Route::get('delete/{id}', ['as' => 'DeleteEmployee', 'uses' => 'UserController@deleteEmployee']);
        });

        /*
        |--------------------------------------------------------------------------
        | Tax groups routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'taxGroups'], function() {

            Route::get('list', ['as' => 'GetTaxGroups', 'uses' => 'TaxGroupController@getTaxGroups']);

            Route::get('add', ['as' => 'AddTaxGroup', 'uses' => 'TaxGroupController@addTaxGroup']);

            //ajax route
            Route::post('insert', ['uses' => 'TaxGroupController@insertTaxGroup']);

            Route::get('edit/{id}', ['as' => 'EditTaxGroup', 'uses' => 'TaxGroupController@editTaxGroup']);

            //ajax route
            Route::post('update', ['uses' => 'TaxGroupController@updateTaxGroup']);

            Route::get('delete/{id}', ['as' => 'DeleteTaxGroup', 'uses' => 'TaxGroupController@deleteTaxGroup']);
        });

        /*
        |--------------------------------------------------------------------------
        | Offices routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'offices'], function() {

            Route::get('list', ['as' => 'GetOffices', 'uses' => 'OfficeController@getOffices']);

            Route::get('add', ['as' => 'AddOffice', 'uses' => 'OfficeController@addOffice']);

            Route::post('insert', ['as' => 'InsertOffice', 'uses' => 'OfficeController@insertOffice']);

            Route::get('edit/{id}', ['as' => 'EditOffice', 'uses' => 'OfficeController@editOffice']);

            Route::post('update', ['as' => 'UpdateOffice', 'uses' => 'OfficeController@updateOffice']);

            Route::get('delete/{id}', ['as' => 'DeleteOffice', 'uses' => 'OfficeController@deleteOffice']);
        });

        /*
        |--------------------------------------------------------------------------
        | Registers routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'registers'], function() {

            Route::get('list', ['as' => 'GetRegisters', 'uses' => 'OfficeController@getRegisters']);

            Route::get('add', ['as' => 'AddRegister', 'uses' => 'OfficeController@addRegister']);

            Route::post('insert', ['as' => 'InsertRegister', 'uses' => 'OfficeController@insertRegister']);

            Route::get('edit/{id}', ['as' => 'EditRegister', 'uses' => 'OfficeController@editRegister']);

            Route::post('update', ['as' => 'UpdateRegister', 'uses' => 'OfficeController@updateRegister']);

            Route::get('delete/{id}', ['as' => 'DeleteRegister', 'uses' => 'OfficeController@deleteRegister']);
        });

        /*
        |--------------------------------------------------------------------------
        | Fiscal certificate routes
        |--------------------------------------------------------------------------
        */

        Route::group(['prefix' => 'fiscalCertificate'], function() {

            Route::get('info', ['as' => 'CertificateInfo', 'uses' => 'AdminController@getCertificateInfo']);

            Route::post('update', ['as' => 'UpdateCertificateInfo', 'uses' => 'AdminController@updateCertificateInfo']);
        });
    });
});

Auth::routes();
