<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class xxTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->char('code', 2)->unique();
        });

        Schema::create('languages', function(Blueprint $table) {
            $table->increments('id');
            $table->string('code', 10)->unique();
        });

        Schema::create('currencies', function(Blueprint $table) {
            $table->increments('id');
            $table->string('code', 10)->unique();
        });

        Schema::create('payment_types', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('code', 30);
        });

        Schema::create('units', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('code', 20);
        });

        Schema::create('zip_codes', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('code');
            $table->string('name');
        });

        Schema::create('companies', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email', 70)->nullable();
            $table->string('oib', 20)->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('website', 50)->nullable();
            $table->string('bank_account')->nullable();
            $table->string('iban')->nullable();
            $table->string('swift')->nullable();
            $table->text('document_footer')->nullable();
            $table->string('logo')->nullable();
            $table->string('logo2')->nullable();
            $table->string('certificate')->nullable();
            $table->string('certificate_password', 25)->nullable();
            $table->char('pdv_user', 1)->default('T');
            $table->char('sljednost_prostor', 1)->default('T');
            $table->text('payment_terms')->nullable();
            $table->text('general_terms')->nullable();
            $table->char('profile', 1)->default('F');
            $table->date('licence_end');
            $table->integer('legal_form')->default(1);
            $table->string('invoice_email', 70)->nullable();
            $table->char('active', 1)->default('T');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email', 50)->unique();
            $table->string('password');
            $table->string('phone', 30)->nullable();
            $table->integer('office_id')->nullable();
            $table->integer('register_id')->nullable();
            $table->char('active', 1)->default('F');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('clients', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->tinyInteger('client_type');
            $table->string('name');
            $table->string('oib', 11)->nullable();
            $table->string('tax_number')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code', 30)->nullable();
            $table->integer('zip_code_id')->unsigned();
            $table->char('country', 2);
            $table->string('phone', 30)->nullable();
            $table->string('email', 50)->nullable();
            $table->char('int_client', 1)->default('F');
            $table->integer('rebate')->nullable();
            $table->char('deleted', 1)->default('F');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('zip_code_id')->references('id')->on('zip_codes');
            $table->foreign('country')->references('code')->on('countries');

            $table->index('name');
        });

        Schema::create('categories', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->string('name');
            $table->char('deleted', 1)->default('F');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('tax_groups', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->string('name');
            $table->text('note')->nullable();
            $table->char('deleted', 1)->default('F');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('taxes', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('group_id')-> unsigned();
            $table->decimal('tax', 10, 2);
            $table->date('tax_date');

            $table->foreign('group_id')->references('id')->on('tax_groups')->onDelete('cascade');
        });

        Schema::create('products', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->integer('unit_id')->unsigned();
            $table->integer('tax_group_id')->unsigned();
            $table->string('code');
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->char('service', 1)->default('T');
            $table->text('description')->nullable();
            $table->char('deleted', 1)->default('F');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('tax_group_id')->references('id')->on('tax_groups');

            $table->index('name');
        });

        Schema::create('offices', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->string('label');
            $table->string('name', 50);
            $table->string('address');
            $table->string('city');
            $table->string('phone', 70)->nullable();
            $table->char('deleted', 1)->default('F');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('registers', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->integer('office_id')->unsigned();
            $table->integer('label');
            $table->char('deleted', 1)->default('F');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('office_id')->references('id')->on('offices');
        });

        Schema::create('offers', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('doc_number');
            $table->string('offer_id', 30);
            $table->integer('office_id')->unsigned();
            $table->datetime('offer_date');
            $table->integer('client_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->integer('payment_type_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->integer('input_currency_id')->unsigned();
            $table->decimal('currency_ratio', 8, 5);
            $table->date('valid_date');
            $table->char('tax', 1)->default('T');
            $table->text('note')->nullable();
            $table->text('int_note')->nullable();
            $table->char('realized', 1)->default('F');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('office_id')->references('id')->on('offices');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('language_id')->references('id')->on('languages');
            $table->foreign('payment_type_id')->references('id')->on('payment_types');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('input_currency_id')->references('id')->on('currencies');

            $table->index('offer_date');
        });

        Schema::create('offer_products', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('offer_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->decimal('quantity', 10, 2);
            $table->decimal('price', 10, 2);
            $table->char('custom_price', 1)->default('F');
            $table->char('brutto', 1)->default('F');
            $table->integer('tax_group_id')->unsigned();
            $table->integer('rebate')->default(0);
            $table->text('note')->nullable();

            $table->foreign('offer_id')->references('id')->on('offers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('tax_group_id')->references('id')->on('tax_groups');
        });

        Schema::create('offer_notes', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('offer_id')->unsigned();
            $table->text('note');

            $table->foreign('offer_id')->references('id')->on('offers')->onDelete('cascade');
        });

        Schema::create('invoices', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('doc_number');
            $table->string('invoice_id', 30);
            $table->char('retail', 1)->default('F');
            $table->integer('office_id')->unsigned();
            $table->integer('register_id')->unsigned();
            $table->datetime('invoice_date');
            $table->integer('client_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->integer('payment_type_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->integer('input_currency_id')->unsigned();
            $table->decimal('currency_ratio', 10, 6);
            $table->date('due_date');
            $table->date('delivery_date')->nullable();
            $table->text('note')->nullable();
            $table->text('int_note')->nullable();
            $table->char('paid', 1)->default('F');
            $table->char('reversed', 1)->default('F');
            $table->integer('reversed_id');
            $table->string('zki', 100)->nullable();
            $table->string('jir', 100)->nullable();
            $table->char('tax', 1)->default('T');
            $table->char('advance', 1)->default('F');
            $table->integer('contract_id')->unsigned()->nullable();
            $table->integer('current_contract_invoice')->unsigned()->nullable();
            $table->decimal('partial_paid_sum', 10, 2)->nullable();
            $table->char('show_model', 1)->default('F');
            $table->string('model', 4)->nullable();
            $table->string('reference_number', 22)->nullable();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('office_id')->references('id')->on('offices');
            $table->foreign('register_id')->references('id')->on('registers');
            $table->foreign('language_id')->references('id')->on('languages');
            $table->foreign('payment_type_id')->references('id')->on('payment_types');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('input_currency_id')->references('id')->on('currencies');

            $table->index('invoice_date');
        });

        Schema::create('invoice_products', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('invoice_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->decimal('quantity', 12, 3);
            $table->decimal('price', 10, 2);
            $table->char('custom_price', 1)->default('F');
            $table->char('brutto', 1)->default('F');
            $table->integer('tax_group_id')->unsigned();
            $table->integer('rebate')->default(0);
            $table->text('note')->nullable();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('tax_group_id')->references('id')->on('tax_groups');
        });

        Schema::create('invoice_notes', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('invoice_id')->unsigned();
            $table->text('note');

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });

        Schema::create('dispatches', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('doc_number');
            $table->string('dispatch_id', 30);
            $table->datetime('dispatch_date');
            $table->integer('client_id')->unsigned();
            $table->text('note')->nullable();
            $table->char('show_prices', 1)->default('T');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients');
        });

        Schema::create('dispatch_products', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('dispatch_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->decimal('quantity', 10, 2);
            $table->decimal('price', 10, 2);
            $table->integer('tax_group_id')->unsigned();
            $table->text('note')->nullable();

            $table->foreign('dispatch_id')->references('id')->on('dispatches')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('tax_group_id')->references('id')->on('tax_groups');
        });

        Schema::create('order_forms', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('doc_number');
            $table->string('order_form_id', 30);
            $table->datetime('order_form_date');
            $table->integer('client_id')->unsigned();
            $table->date('delivery_date');
            $table->string('location');
            $table->text('note')->nullable();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients');
        });

        Schema::create('order_form_products', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('order_form_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->decimal('quantity', 10, 2);
            $table->decimal('price', 10, 2);
            $table->integer('tax_group_id')->unsigned();
            $table->text('note')->nullable();

            $table->foreign('order_form_id')->references('id')->on('order_forms')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('tax_group_id')->references('id')->on('tax_groups');
        });

        Schema::create('employees', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->string('first_name', 70);
            $table->string('last_name', 70);
            $table->string('email', 70)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('job_title', 100);
            $table->char('deleted', 1)->default('F');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('vehicles', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->string('vehicle_type', 150);
            $table->string('name');
            $table->string('register_number', 20);
            $table->string('year', 4);
            $table->string('km', 7);
            $table->char('deleted', 1)->default('F');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('wages', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->string('name', 150);
            $table->char('country', 2);
            $table->decimal('price', 8, 2);
            $table->char('deleted', 1)->default('F');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('country')->references('code')->on('countries');
        });

        Schema::create('travel_warrants', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->integer('creator_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->mediumInteger('doc_number')->unsigned();
            $table->string('warrant_id', 30);
            $table->date('warrant_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('vehicle_id')->unsigned();
            $table->integer('start_mileage');
            $table->integer('end_mileage');
            $table->string('duration');
            $table->string('location');
            $table->string('purpose');
            $table->string('description');
            $table->decimal('advance', 8, 2)->nullable();
            $table->decimal('non_costs', 8, 2)->nullable();
            $table->text('note')->nullable();
            $table->text('report')->nullable();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('employees');
            $table->foreign('user_id')->references('id')->on('employees');
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
        });

        Schema::create('warrant_wages', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('warrant_id')->unsigned();
            $table->integer('wage_id')->unsigned();
            $table->date('wage_date');
            $table->char('country', 2);
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->integer('wage');

            $table->foreign('warrant_id')->references('id')->on('travel_warrants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('wage_id')->references('id')->on('wages');
            $table->foreign('country')->references('code')->on('countries');
        });

        Schema::create('directions', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('warrant_id')->unsigned();
            $table->date('direction_date');
            $table->string('transport_type', 150);
            $table->string('start_location');
            $table->string('end_location');
            $table->integer('distance');
            $table->decimal('km_price');

            $table->foreign('warrant_id')->references('id')->on('travel_warrants')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('costs', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('warrant_id')->unsigned();
            $table->date('cost_date');
            $table->string('cost_type');
            $table->string('description');
            $table->decimal('sum', 8, 2);
            $table->decimal('non_costs', 8, 2)->nullable();

            $table->foreign('warrant_id')->references('id')->on('travel_warrants')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('payment_slips', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->integer('doc_number');
            $table->string('slip_id', 30);
            $table->date('slip_date');
            $table->integer('office_id')->unsigned();
            $table->string('location', 100)->nullable();
            $table->integer('client_id')->unsigned();
            $table->string('payer', 150);
            $table->string('item');
            $table->string('description')->nullable();
            $table->decimal('sum', 8, 2);
            $table->integer('invoice_id')->unsigned();
            $table->char('reversed', 1)->default('F');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('payout_slips', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->integer('doc_number');
            $table->string('slip_id', 30);
            $table->date('slip_date');
            $table->integer('office_id')->unsigned();
            $table->string('location', 150)->nullable();
            $table->integer('employee_id')->unsigned();
            $table->text('note')->nullable();
            $table->char('income', 1)->default('F');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('payout_slip_items', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('slip_id')->unsigned();
            $table->string('item');
            $table->string('description')->nullable();
            $table->decimal('sum', 8, 2);

            $table->foreign('slip_id')->references('id')->on('payout_slips')->onDelete('cascade');
        });

        Schema::create('register_reports', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->integer('doc_number');
            $table->string('report_id', 30);
            $table->integer('office_id')->unsigned();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('sum', 12, 2);

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('contracts', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('office_id')->unsigned();
            $table->integer('register_id')->unsigned();
            $table->date('contract_date');
            $table->string('contract_number', 30);
            $table->integer('client_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->integer('payment_type_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->integer('input_currency_id')->unsigned();
            $table->integer('due_days')->unsigned();
            $table->text('note')->nullable();
            $table->text('int_note')->nullable();
            $table->char('tax', 1)->default('T');
            $table->integer('number_of_invoices');
            $table->integer('create_day')->unsigned();
            $table->char('previous_month_create', 1)->default('T');
            $table->char('create_after_end', 1)->default('F');
            $table->char('email_sending', 1)->default('F');
            $table->char('active', 1)->default('T');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('office_id')->references('id')->on('offices');
            $table->foreign('register_id')->references('id')->on('registers');
            $table->foreign('language_id')->references('id')->on('languages');
            $table->foreign('payment_type_id')->references('id')->on('payment_types');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('input_currency_id')->references('id')->on('currencies');
        });

        Schema::create('contract_products', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('contract_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->decimal('quantity', 10, 2);
            $table->decimal('price', 10, 2);
            $table->char('custom_price', 1)->default('F');
            $table->char('brutto', 1)->default('F');
            $table->integer('tax_group_id')->unsigned();
            $table->integer('rebate')->default(0);
            $table->text('note')->nullable();

            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('tax_group_id')->references('id')->on('tax_groups');
        });

        Schema::create('notes', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('company_id')->unsigned();
            $table->text('text');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        Schema::create('client_prices', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('client_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->decimal('price');

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::create('racuni', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('kompanija');
            $table->char('maloprodaja', 1);
            $table->string('oznaka_racuna');
            $table->string('broj_racuna', 100);
            $table->date('datum');
            $table->string('klijent');
            $table->text('opis');
            $table->string('zki', 100);
            $table->string('jir', 100);
            $table->string('korisnik', 200);
        });

        Schema::create('racuni_proizvodi', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('broj_racuna');
            $table->string('proizvod');
            $table->decimal('kolicina', 10, 2);
            $table->decimal('rabat', 10, 2);
            $table->decimal('iznos_rabata', 10, 2);
            $table->integer('pdv');
            $table->decimal('cijena', 10, 2);
            $table->string('jedinica_mjere', 100);
            $table->string('napomena');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
