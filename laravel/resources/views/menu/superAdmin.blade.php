<li @if (Request::is('superadmin/statistics')) {{ "class=active" }} @endif>
    <a href="{{ route('SuperAdminStatistics') }}">
        <i class="fa fa-bar-chart"></i> <span class="nav-label">{{ trans('main.statistics') }}</span>
    </a>
</li>

<li @if (Request::is('superadmin/companies')) {{ "class=active" }} @endif>
    <a href="{{ route('SuperAdminGetCompanies') }}">
        <i class="fa fa-briefcase"></i> <span class="nav-label">{{ trans('main.companies') }}</span>
    </a>
</li>

<li @if (Request::is('superadmin/users')) {{ "class=active" }} @endif>
    <a href="{{ route('SuperAdminGetUsers') }}">
        <i class="fa fa-user"></i> <span class="nav-label">{{ trans('main.users') }}</span>
    </a>
</li>

<li @if (Request::is('superadmin/subscribers/*')) {{ "class=active" }} @endif>
    <a href="#">
        <i class="fa fa-money"></i> <span class="nav-label">{{ trans('main.subscribers') }}</span>
        <span class="fa arrow"></span>
    </a>
    <ul class="nav nav-second-level">
        <li @if (Request::is('superadmin/subscribers/monthly')) {{ "class=active" }} @endif>
            <a href="{{ route('GetMonthlySubscribers') }}">{{ trans('main.monthly') }}</a>
        </li>
        <li @if (Request::is('superadmin/subscribers/annual')) {{ "class=active" }} @endif>
            <a href="{{ route('GetAnnualSubscribers') }}">{{ trans('main.annual') }}</a>
        </li>
    </ul>
</li>