<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\AblyController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\User\POSController;
use App\Http\Controllers\User\TaxController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\HallController;
use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\User\AwardController;
use App\Http\Controllers\User\LeaveController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\StaffController;
use App\Http\Controllers\User\TableController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\User\ReportController;
use App\Http\Controllers\User\VendorController;
use App\Http\Controllers\User\HolidayController;
use App\Http\Controllers\User\PayrollController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\BusinessController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\CustomerController;
use App\Http\Controllers\User\PurchaseController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\User\LeaveTypeController;
use App\Http\Controllers\EmailSubscriberController;
use App\Http\Controllers\User\AttendanceController;
use App\Http\Controllers\User\DepartmentController;
use App\Http\Controllers\User\PermissionController;
use App\Http\Controllers\User\SystemUserController;
use App\Http\Controllers\Website\WebsiteController;
use App\Http\Controllers\User\DesignationController;
use App\Http\Controllers\User\SalaryScaleController;
use App\Http\Controllers\User\TransactionController;
use App\Http\Controllers\User\ProductAddonController;
use App\Http\Controllers\User\StaffDocumentController;
use App\Http\Controllers\SubscriptionPaymentController;
use App\Http\Controllers\NotificationTemplateController;
use App\Http\Controllers\User\PurchaseProductController;
use App\Http\Controllers\User\BusinessSettingsController;
use App\Http\Controllers\User\TransactionMethodController;
use App\Http\Controllers\User\TransactionCategoryController;

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

$ev = env('APP_INSTALLED', true) == true ? get_option('email_verification', 0) : 0;

Route::group(['middleware' => ['install']], function () use ($ev) {

    Auth::routes(['verify' => $ev == 1 ? true : false]);
    Route::get('/logout', 'Auth\LoginController@logout');

    $initialMiddleware = ['auth', 'saas'];
    if ($ev == 1) {
        array_push($initialMiddleware, 'verified');
    }

    Route::group(['middleware' => $initialMiddleware], function () {

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        //Ably Auth
        Route::get('ably/auth', [AblyController::class, 'auth']);

        //Profile Controller
        Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update')->middleware('demo');
        Route::get('profile/change_password', [ProfileController::class, 'change_password'])->name('profile.change_password');
        Route::post('profile/update_password', [ProfileController::class, 'update_password'])->name('profile.update_password')->middleware('demo');
        Route::get('profile/notification_mark_as_read/{id}', [ProfileController::class, 'notification_mark_as_read'])->name('profile.notification_mark_as_read');
        Route::get('profile/show_notification/{id}', [ProfileController::class, 'show_notification'])->name('profile.show_notification');
        Route::get('membership/active_subscription', [MembershipController::class, 'index'])->name('membership.index');

        /** Admin Only Route **/
        Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function () {

            //User Management
            Route::get('users/{id}/login_as_user', [UserController::class, 'login_as_user'])->name('users.login_as_user');
            Route::get('users/get_table_data', [UserController::class, 'get_table_data']);
            Route::resource('users', UserController::class)->middleware("demo:PUT|PATCH|DELETE");

            //Subscription Payments
            Route::get('subscription_payments/get_table_data', [SubscriptionPaymentController::class, 'get_table_data']);
            Route::resource('subscription_payments', SubscriptionPaymentController::class)->except([
                'show', 'destroy',
            ])->middleware("demo:PUT|PATCH|DELETE");

            Route::group(['middleware' => 'demo'], function () {
                //Package Controller
                Route::resource('packages', PackageController::class);

                //Payment Gateways
                Route::resource('payment_gateways', PaymentGatewayController::class)->except([
                    'create', 'store', 'show', 'destroy',
                ]);

                //Email Subscribers
                Route::match(['get', 'post'], 'email_subscribers/send_email', [EmailSubscriberController::class, 'send_email'])->name('email_subscribers.send_email');
                Route::get('email_subscribers/export', [EmailSubscriberController::class, 'export'])->name('email_subscribers.export');
                Route::get('email_subscribers/get_table_data', [EmailSubscriberController::class, 'get_table_data']);
                Route::get('email_subscribers', [EmailSubscriberController::class, 'index'])->name('email_subscribers.index');
                Route::delete('email_subscribers/{id}/destroy', [EmailSubscriberController::class, 'destroy'])->name('email_subscribers.destroy');

                //Page Controller
                Route::post('pages/store_default_pages/{slug?}', [PageController::class, 'store_default_pages'])->name('pages.default_pages.store');
                Route::get('pages/default_pages/{slug?}', [PageController::class, 'default_pages'])->name('pages.default_pages');
                Route::resource('pages', PageController::class)->except('show');

                //FAQ Controller
                Route::resource('faqs', FaqController::class)->except('show');

                //Features Controller
                Route::resource('features', FeatureController::class)->except('show');

                //Features Controller
                Route::resource('features', FeatureController::class)->except('show');

                //Testimonial Controller
                Route::resource('testimonials', TestimonialController::class)->except('show');

                //Team Controller
                Route::resource('posts', PostController::class)->except('show');

                //Team Controller
                Route::resource('teams', TeamController::class)->except('show');

                //Currency List
                Route::resource('currency', CurrencyController::class);

                //Language Controller
                Route::get('languages/{lang}/edit_website_language', [LanguageController::class, 'edit_website_language'])->name('languages.edit_website_language');
                Route::resource('languages', LanguageController::class);

                //Utility Controller
                Route::match(['get', 'post'], 'administration/general_settings/{store?}', [UtilityController::class, 'settings'])->name('settings.update_settings');
                Route::post('administration/upload_logo', [UtilityController::class, 'upload_logo'])->name('settings.uplaod_logo');
                Route::get('administration/database_backup_list', [UtilityController::class, 'database_backup_list'])->name('database_backups.list');
                Route::get('administration/create_database_backup', [UtilityController::class, 'create_database_backup'])->name('database_backups.create')->middleware("demo:GET");
                Route::delete('administration/destroy_database_backup/{id}', [UtilityController::class, 'destroy_database_backup'])->name('database_backups.destroy');
                Route::get('administration/download_database_backup/{id}', [UtilityController::class, 'download_database_backup'])->name('database_backups.download')->middleware("demo:GET");
                Route::post('administration/remove_cache', [UtilityController::class, 'remove_cache'])->name('settings.remove_cache');
                Route::post('administration/send_test_email', [UtilityController::class, 'send_test_email'])->name('settings.send_test_email');

                //Notification Template
                Route::resource('notification_templates', NotificationTemplateController::class)->only([
                    'index', 'edit', 'update',
                ]);
            });

        });

        /** Subscriber Login **/
        Route::group(['middleware' => ['business'], 'prefix' => 'user'], function () {

            //Business Controller
            Route::get('business/{id}/users', [BusinessController::class, 'users'])->name('business.users');
            Route::resource('business', BusinessController::class)->except('show');

            //Permission Controller
            Route::get('roles/{role_id?}/access_control', [PermissionController::class, 'show'])->name('permission.show');
            Route::post('permission/store', [PermissionController::class, 'store'])->name('permission.store');
            Route::resource('roles', RoleController::class)->except('show');

            //User Controller
            Route::match(['get', 'post'], 'system_users/{userId}/{businessId}/change_role', [SystemUserController::class, 'change_role'])->name('system_users.change_role');
            Route::post('system_users/send_invitation', [SystemUserController::class, 'send_invitation'])->name('system_users.send_invitation');
            Route::delete('system_users/{id}/destroy', [SystemUserController::class, 'destroy'])->name('system_users.destroy');
            Route::get('system_users/invite/{businessId}', [SystemUserController::class, 'invite'])->name('system_users.invite');
            Route::get('system_users/{businessId}/invitation_history', [SystemUserController::class, 'invitation_history'])->name('invitation_history.index');
            Route::delete('invitation_history/{businessId}/destroy_invitation', [SystemUserController::class, 'destroy_invitation'])->name('invitation_history.destroy_invitation');

            //Business Settings Controller
            Route::post('business/{id}/send_test_email', [BusinessSettingsController::class, 'send_test_email'])->name('business.send_test_email');
            Route::post('business/{id}/store_email_settings', [BusinessSettingsController::class, 'store_email_settings'])->name('business.store_email_settings');
            Route::post('business/{id}/store_payment_gateway_settings', [BusinessSettingsController::class, 'store_payment_gateway_settings'])->name('business.store_payment_gateway_settings');
            Route::post('business/{id}/store_pos_settings', [BusinessSettingsController::class, 'store_pos_settings'])->name('business.store_pos_settings');
            Route::post('business/{id}/store_currency_settings', [BusinessSettingsController::class, 'store_currency_settings'])->name('business.store_currency_settings');
            Route::post('business/{id}/store_general_settings', [BusinessSettingsController::class, 'store_general_settings'])->name('business.store_general_settings');
            Route::get('business/{id}/settings', [BusinessSettingsController::class, 'settings'])->name('business.settings');
        });

        /** Dynamic Permission **/
        Route::group(['middleware' => ['permission'], 'prefix' => 'user'], function () {

            //Dashboard Widget
            Route::get('dashboard/current_month_sales_widget', 'DashboardController@current_month_sales_widget')->name('dashboard.current_month_sales_widget');
            Route::get('dashboard/current_day_sales_widget', 'DashboardController@current_day_sales_widget')->name('dashboard.current_day_sales_widget');
            Route::get('dashboard/current_month_expense_widget', 'DashboardController@current_month_expense_widget')->name('dashboard.current_month_expense_widget');
            Route::get('dashboard/current_month_orders_widget', 'DashboardController@current_month_orders_widget')->name('dashboard.current_month_orders_widget');
            Route::get('dashboard/cashflow_widget', 'DashboardController@cashflow_widget')->name('dashboard.cashflow_widget');
            Route::get('dashboard/sales_by_category_widget', 'DashboardController@sales_by_category_widget')->name('dashboard.sales_by_category_widget');
            Route::get('dashboard/expense_by_category_widget', 'DashboardController@expense_by_category_widget')->name('dashboard.expense_by_category_widget');

            //Customers
            Route::get('customers/get_table_data', [CustomerController::class, 'get_table_data']);
            Route::resource('customers', CustomerController::class);

            //Vendors
            Route::get('vendors/get_table_data', [VendorController::class, 'get_table_data']);
            Route::resource('vendors', VendorController::class);

            //Item Category Controller
            Route::resource('categories', CategoryController::class);

            //Item Controller
            Route::post('products/get_variation_price/{product_id}', [ProductController::class, 'get_variation_price'])->name('products.get_variation_price');
            Route::post('products/generate_variations', [ProductController::class, 'generate_variations']);
            Route::get('products/get_table_data', [ProductController::class, 'get_table_data']);
            Route::resource('products', ProductController::class)->except('show');

            //Addon Items
            Route::delete('product_addons/{productId}/destroy', [ProductAddonController::class, 'destroy'])->name('product_addons.destroy');
            Route::patch('product_addons/{productId}/update', [ProductAddonController::class, 'update'])->name('product_addons.update');
            Route::get('product_addons/{productId}/edit', [ProductAddonController::class, 'edit'])->name('product_addons.edit');
            Route::post('product_addons/{productId}/store', [ProductAddonController::class, 'store'])->name('product_addons.store');
            Route::get('product_addons/{productId}/create', [ProductAddonController::class, 'create'])->name('product_addons.create');
            Route::get('product_addons/{productId}', [ProductAddonController::class, 'index'])->name('product_addons.index');

            //Hall Controller
            Route::post('halls/{id}/update_setup', [HallController::class, 'update_setup'])->name('halls.update_setup');
            Route::resource('halls', HallController::class);

            //Table Controller
            Route::delete('tables/{id}/destroy', [TableController::class, 'destroy'])->name('tables.destroy');
            Route::patch('tables/{id}/update', [TableController::class, 'update'])->name('tables.update');
            Route::get('tables/{id}/edit', [TableController::class, 'edit'])->name('tables.edit');
            Route::post('tables/{hallId}/store', [TableController::class, 'store'])->name('tables.store');
            Route::get('tables/{hallId}/create', [TableController::class, 'create'])->name('tables.create');
            Route::get('tables/{hallId}', [TableController::class, 'index'])->name('tables.index');

            //POS Controller
            Route::get('pos/{order_id}/print_kitchen_receipt', [POSController::class, 'print_kitchen_receipt'])->name('pos.print_kitchen_receipt');
            Route::get('pos/{order_id}/print_customer_receipt', [POSController::class, 'print_customer_receipt'])->name('pos.print_customer_receipt');
            Route::match(['get', 'post'], 'pos/{tableId}/place_order', [POSController::class, 'place_order'])->name('pos.place_order');
            Route::post('pos/{tableId}/apply_tax', [POSController::class, 'apply_tax'])->name('pos.apply_tax');
            Route::match(['get', 'post'], 'pos/{tableId}/add_discount', [POSController::class, 'add_discount'])->name('pos.add_discount');
            Route::get('pos/{tableId}/empty_cart', [POSController::class, 'empty_cart'])->name('pos.empty_cart');
            Route::post('pos/{cartId}/{tableId}/update_cart', [POSController::class, 'update_cart'])->name('pos.update_cart');
            Route::get('pos/{cartId}/{tableId}/remove_cart', [POSController::class, 'remove_cart'])->name('pos.remove_cart');
            Route::post('pos/{productId}/{tableId}/add_to_cart', [POSController::class, 'add_to_cart'])->name('pos.add_to_cart');
            Route::get('pos/{productId}/product', [POSController::class, 'product'])->name('pos.product');
            Route::get('pos/active_orders', [POSController::class, 'active_orders'])->name('pos.active_orders');
            Route::get('pos/{tableId}/sell/{type?}', [POSController::class, 'pos'])->name('pos.sell');
            Route::get('pos/table', [POSController::class, 'table'])->name('pos.table');

            //Order Controller
            Route::post('orders/update_order_status/{id}', [OrderController::class, 'update_order_status']);
            Route::get('orders/tracking/{action?}', [OrderController::class, 'tracking'])->name('orders.tracking');
            Route::get('orders/get_table_data', [OrderController::class, 'get_table_data']);
            Route::delete('orders/{id}/destroy', [OrderController::class, 'destroy'])->name('orders.destroy');
            Route::patch('orders/{id}/update', [OrderController::class, 'update'])->name('orders.update');
            Route::get('orders/{id}/edit', [OrderController::class, 'edit'])->name('orders.edit');
            Route::get('orders/{id}/show', [OrderController::class, 'show'])->name('orders.show');
            Route::get('orders', [OrderController::class, 'index'])->name('orders.index');

            //Transaction Categories
            Route::resource('transaction_categories', TransactionCategoryController::class)->except('show');

            //Transaction Methods
            Route::resource('transaction_methods', TransactionMethodController::class)->except('view');

            //Transaction Controller
            Route::get('transactions/get_table_data', [TransactionController::class, 'get_table_data']);
            Route::resource('transactions', TransactionController::class);

            //Purchase Product Controller
            Route::get('purchase_items/get_table_data', [PurchaseProductController::class, 'get_table_data']);
            Route::get('purchase_items/get_product/{id}', [PurchaseProductController::class, 'get_product']);
            Route::resource('purchase_items', PurchaseProductController::class)->except('show');

            //Purchase
            Route::match(['get', 'post'], 'purchases/{id}/add_payment', [PurchaseController::class, 'add_payment'])->name('purchases.add_payment');
            Route::get('purchases/{id}/duplicate', [PurchaseController::class, 'duplicate'])->name('purchases.duplicate');
            Route::post('purchases/get_table_data', [PurchaseController::class, 'get_table_data']);
            Route::resource('purchases', PurchaseController::class);

            //HR Module
            Route::resource('departments', DepartmentController::class);
            Route::get('designations/get_designations/{deaprtment_id}', [DesignationController::class, 'get_designations']);
            Route::resource('designations', DesignationController::class)->except('show');
            Route::get('salary_scales/get_salary_scales/{designation_id}', [SalaryScaleController::class, 'get_salary_scales']);
            Route::get('salary_scales/filter_by_department/{department_id}', [SalaryScaleController::class, 'index'])->name('salary_scales.filter_by_department');
            Route::resource('salary_scales', SalaryScaleController::class);
            //Staff Controller
            Route::get('staffs/get_table_data', [StaffController::class, 'get_table_data']);
            Route::resource('staffs', StaffController::class);
            //Staff Documents
            Route::get('staff_documents/{employee_id}', [StaffDocumentController::class, 'index'])->name('staff_documents.index');
            Route::get('staff_documents/create/{employee_id}', [StaffDocumentController::class, 'create'])->name('staff_documents.create');
            Route::resource('staff_documents', StaffDocumentController::class)->except(['index', 'create', 'show']);
            //Holiday Controller
            Route::get('holidays/get_table_data', [HolidayController::class, 'get_table_data']);
            Route::match(['get', 'post'], 'holidays/weekends', [HolidayController::class, 'weekends'])->name('holidays.weekends');
            Route::resource('holidays', HolidayController::class)->except('show');
            //Leave Application
            Route::resource('leave_types', LeaveTypeController::class)->except('show');
            Route::get('leaves/get_table_data', [LeaveController::class, 'get_table_data']);
            Route::resource('leaves', LeaveController::class);
            //Attendance Controller
            Route::get('attendance/get_table_data', [AttendanceController::class, 'get_table_data']);
            Route::post('attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
            Route::resource('attendance', AttendanceController::class)->except('show', 'edit', 'update', 'destroy');
            //Award Controller
            Route::get('awards/get_table_data', [AwardController::class, 'get_table_data']);
            Route::resource('awards', AwardController::class);
            //Payslip Controller
            Route::post('payslips/store_payment', [PayrollController::class, 'store_payment'])->name('payslips.store_payment');
            Route::match(['get', 'post'], 'payslips/make_payment', [PayrollController::class, 'make_payment'])->name('payslips.make_payment');
            Route::get('payslips/get_table_data', [PayrollController::class, 'get_table_data']);
            Route::resource('payslips', PayrollController::class);

            //Taxes
            Route::resource('taxes', TaxController::class);

            //Report Controller
            Route::match(['get', 'post'], 'reports/sales_report', [ReportController::class, 'sales_report'])->name('reports.sales_report');
            Route::match(['get', 'post'], 'reports/item_wise_sales_report', [ReportController::class, 'item_wise_sales_report'])->name('reports.item_wise_sales_report');
            Route::match(['get', 'post'], 'reports/profit_and_loss', [ReportController::class, 'profit_and_loss'])->name('reports.profit_and_loss');
            Route::match(['get', 'post'], 'reports/attendance_report', [ReportController::class, 'attendance_report'])->name('reports.attendance_report');
            Route::match(['get', 'post'], 'reports/expense_report', [ReportController::class, 'expense_report'])->name('reports.expense_report');
            Route::match(['get', 'post'], 'reports/purchase_report', [ReportController::class, 'purchase_report'])->name('reports.purchase_report');
            Route::match(['get', 'post'], 'reports/payroll_report', [ReportController::class, 'payroll_report'])->name('reports.payroll_report');
            Route::match(['get', 'post'], 'reports/tax_report', [ReportController::class, 'tax_report'])->name('reports.tax_report');
        });

        //Switch Business
        Route::get('business/switch_business/{id}', [BusinessController::class, 'switch_business'])->name('business.switch_business');

        //Ajax Select2 Controller
        Route::get('ajax/get_table_data', 'Select2Controller@get_table_data');

    });

    Route::get('users/back_to_admin', [UserController::class, 'back_to_admin'])->name('users.back_to_admin')->middleware('auth');

    Route::get('switch_language/', function () {
        if (isset($_GET['language'])) {
            session(['language' => $_GET['language']]);
            return back();
        }
    })->name('switch_language');

    //Frontend Website
    Route::get('/about', [WebsiteController::class, 'about']);
    Route::get('/features', [WebsiteController::class, 'features']);
    Route::get('/pricing', [WebsiteController::class, 'pricing']);
    Route::get('/faq', [WebsiteController::class, 'faq']);
    Route::get('/blogs/{slug?}', [WebsiteController::class, 'blogs']);
    Route::get('/contact', [WebsiteController::class, 'contact']);
    Route::post('/send_message', 'Website\WebsiteController@send_message');
    Route::post('/post_comment', 'Website\WebsiteController@post_comment');
    Route::post('/email_subscription', 'Website\WebsiteController@email_subscription');

    if (env('APP_INSTALLED', true)) {
        Route::get('/{slug?}', [WebsiteController::class, 'index']);
    } else {
        Route::get('/', function () {
            echo "Installation";
        });
    }

});

//Subscription Payment
Route::group(['prefix' => 'subscription_callback', 'namespace' => 'User\SubscriptionGateway'], function () {
    Route::get('paypal', 'PayPal\ProcessController@callback')->name('subscription_callback.PayPal');
    Route::post('stripe', 'Stripe\ProcessController@callback')->name('subscription_callback.Stripe');
    Route::post('razorpay', 'Razorpay\ProcessController@callback')->name('subscription_callback.Razorpay');
    Route::get('paystack', 'Paystack\ProcessController@callback')->name('subscription_callback.Paystack');
    Route::get('flutterwave', 'Flutterwave\ProcessController@callback')->name('subscription_callback.Flutterwave');
    Route::get('mollie', 'Mollie\ProcessController@callback')->name('subscription_callback.Mollie');
    Route::match(['get', 'post'], 'instamojo', 'Instamojo\ProcessController@callback')->name('subscription_callback.Instamojo');
});

//Accept Invitation
Route::get('system_users/accept_invitation/{id}', [SystemUserController::class, 'accept_invitation'])->name('system_users.accept_invitation');

//Membership Subscription
Route::get('membership/packages', [MembershipController::class, 'packages'])->name('membership.packages')->middleware('auth');
Route::post('membership/choose_package', [MembershipController::class, 'choose_package'])->name('membership.choose_package')->middleware('auth');
Route::get('membership/payment_gateways', [MembershipController::class, 'payment_gateways'])->name('membership.payment_gateways')->middleware('auth');
Route::get('membership/make_payment/{gateway}', [MembershipController::class, 'make_payment'])->name('membership.make_payment')->middleware('auth');

Route::get('dashboard/json_sales_by_category', 'DashboardController@json_sales_by_category')->middleware('auth');
Route::get('dashboard/json_expense_by_category', 'DashboardController@json_expense_by_category')->middleware('auth');
Route::get('dashboard/json_cashflow', 'DashboardController@json_cashflow')->middleware('auth');

Route::get('dashboard/json_package_wise_subscription', 'DashboardController@json_package_wise_subscription')->middleware('auth');
Route::get('dashboard/json_yearly_reveneu', 'DashboardController@json_yearly_reveneu')->middleware('auth');

//Social Login
Route::get('/login/{provider}', 'Auth\SocialController@redirect');
Route::get('/login/{provider}/callback', 'Auth\SocialController@callback');

Route::get('/installation', 'Install\InstallController@index');
Route::get('install/database', 'Install\InstallController@database');
Route::post('install/process_install', 'Install\InstallController@process_install');
Route::get('install/create_user', 'Install\InstallController@create_user');
Route::post('install/store_user', 'Install\InstallController@store_user');
Route::get('install/system_settings', 'Install\InstallController@system_settings');
Route::post('install/finish', 'Install\InstallController@final_touch');

//Update System
Route::get('migration/update', 'Install\UpdateController@update_migration');