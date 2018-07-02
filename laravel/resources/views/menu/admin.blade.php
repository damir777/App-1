<li @if (Request::is('statistics')) {{ "class=active" }} @endif>
    <a href="{{ route('AdminStatistics') }}">
        <i class="fa fa-bar-chart"></i> <span class="nav-label">{{ trans('main.statistics') }}</span>
    </a>
</li>

<li @if (Request::is('docs/*') || Request::is('adHoc/*')) {{ "class=active" }} @endif>
    <a href="#">
        <i class="fa fa-file-text-o"></i> <span class="nav-label">{{ trans('main.documents') }}</span>
        <span class="fa arrow"></span>
    </a>
    <ul class="nav nav-second-level">
        <li @if (Request::is('docs/offers/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetOffers') }}">{{ trans('main.offers') }}</a>
        </li>
        <li @if (Request::is('docs/invoices/*')) {{ "class=active" }} @endif>
            <a href="#">{{ trans('main.invoices') }} <span class="fa arrow"></span></a>
            <ul class="nav nav-third-level">
                <li @if (Request::is('docs/invoices/1/*')) {{ "class=active" }} @endif>
                    <a href="{{ route('GetInvoices', 1) }}">{{ trans('main.retail') }}</a>
                </li>
                <li @if (Request::is('docs/invoices/2/*')) {{ "class=active" }} @endif>
                    <a href="{{ route('GetInvoices', 2) }}">{{ trans('main.wholesale') }}</a>
                </li>
            </ul>
        </li>
        <li @if (Request::is('docs/dispatches/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetDispatches') }}">{{ trans('main.dispatches') }}</a>
        </li>
        <li @if (Request::is('docs/contracts/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetContracts') }}">{{ trans('main.contracts') }}</a>
        </li>
        <li @if (Request::is('docs/orderForms/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetOrderForms') }}">{{ trans('main.order_forms') }}</a>
        </li>
        <li @if (Request::is('docs/notes/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetNotes') }}">{{ trans('main.notes') }}</a>
        </li>
        @if (Auth::user()->company_id == 65 || Auth::user()->company_id == 66)
            <li @if (Request::is('adHoc/invoices/*')) {{ "class=active" }} @endif>
                <a href="#" style="color: #ec4758">Stari računi <span class="fa arrow"></span></a>
                <ul class="nav nav-third-level">
                    <li @if (Request::is('adHoc/invoices/1/list')) {{ "class=active" }} @endif>
                        <a href="{{ route('GetAdHocInvoices', 1) }}">{{ trans('main.retail') }}</a>
                    </li>
                    <li @if (Request::is('adHoc/invoices/2/list')) {{ "class=active" }} @endif>
                        <a href="{{ route('GetAdHocInvoices', 2) }}">{{ trans('main.wholesale') }}</a>
                    </li>
                </ul>
            </li>
        @endif
    </ul>
</li>

<li @if (Request::is('registerReports/*')) {{ "class=active" }} @endif>
    <a href="#">
        <i class="fa fa-eur"></i> <span class="nav-label">{{ trans('main.register_report') }}</span>
        <span class="fa arrow"></span>
    </a>
    <ul class="nav nav-second-level">
        <li @if (Request::is('registerReports/paymentSlips/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetPaymentSlips') }}">{{ trans('main.payment_slips') }}</a>
        </li>
        <li @if (Request::is('registerReports/payoutSlips/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetPayoutSlips') }}">{{ trans('main.payout_slips') }}</a>
        </li>
        <li @if (Request::is('registerReports/reports/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetRegisterReports') }}">{{ trans('main.reports') }}</a>
        </li>
    </ul>
</li>

<li @if (Request::is('travelWarrants/*')) {{ "class=active" }} @endif>
    <a href="#">
        <i class="fa fa-car"></i> <span class="nav-label">{{ trans('main.travel_warrants') }}</span>
        <span class="fa arrow"></span>
    </a>
    <ul class="nav nav-second-level">
        <li @if (Request::is('travelWarrants/vehicles/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetVehicles') }}">{{ trans('main.vehicles') }}</a>
        </li>
        <li @if (Request::is('travelWarrants/wages/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetWages') }}">{{ trans('main.wages') }}</a>
        </li>
        <li @if (Request::is('travelWarrants/warrants/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetTravelWarrants') }}">{{ trans('main.warrants') }}</a>
        </li>
    </ul>
</li>

<li @if (Request::is('clients/*')) {{ "class=active" }} @endif>
    <a href="{{ route('GetClients') }}">
        <i class="fa fa-user-circle"></i> <span class="nav-label">{{ trans('main.clients') }}</span>
    </a>
</li>

<li @if (Request::is('categories/*') || Request::is('products/*')) {{ "class=active" }} @endif>
    <a href="#">
        <i class="fa fa-bars"></i> <span class="nav-label">{{ trans('main.products') }}</span>
        <span class="fa arrow"></span>
    </a>
    <ul class="nav nav-second-level">
        <li @if (Request::is('categories/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetCategories') }}">{{ trans('main.categories') }}</a>
        </li>
        <li @if (Request::is('products/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetProducts') }}">{{ trans('main.products') }}</a>
        </li>
    </ul>
</li>

<li @if (Request::is('settings/*')) {{ "class=active" }} @endif>
    <a href="#">
        <i class="fa fa-cog"></i> <span class="nav-label">{{ trans('main.settings') }}</span>
        <span class="fa arrow"></span>
    </a>
    <ul class="nav nav-second-level">
        <li @if (Request::is('settings/users/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetUsers') }}">{{ trans('main.users') }}</a>
        </li>
        <li @if (Request::is('settings/employees/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetEmployees') }}">{{ trans('main.employees') }}</a>
        </li>
        <li @if (Request::is('settings/taxGroups/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetTaxGroups') }}">{{ trans('main.tax_groups') }}</a>
        </li>
        <li @if (Request::is('settings/offices/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetOffices') }}">{{ trans('main.offices') }}</a>
        </li>
        <li @if (Request::is('settings/registers/*')) {{ "class=active" }} @endif>
            <a href="{{ route('GetRegisters') }}">{{ trans('main.registers') }}</a>
        </li>
        <li @if (Request::is('settings/fiscalCertificate/*')) {{ "class=active" }} @endif>
            <a href="{{ route('CertificateInfo') }}">{{ trans('main.fiscalization') }}</a>
        </li>
    </ul>
</li>