<li>
	<a href="{{ route('dashboard.index') }}"><i class="fas fa-th-large"></i><span>{{ _lang('Dashboard') }}</span></a>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-utensils"></i><span>{{ _lang('Food Items') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
        <li class="nav-item"><a class="nav-link" href="{{ route('categories.index') }}">{{ _lang('Categories') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">{{ _lang('Food Items') }}</a></li>
	</ul>
</li>

<li>
	<a href="{{ route('halls.index') }}"><i class="fas fa-border-none"></i><span>{{ _lang('Table & Hall Plans') }}</span></a>
</li>

<li>
	<a href="{{ route('pos.table') }}" target="_blank"><i class="fas fa-tablet-alt"></i><span>{{ _lang('Point Of Sale') }}</span></a>
</li>

<li>
	<a href="{{ route('orders.tracking') }}"><i class="fas fa-broadcast-tower"></i><span>{{ _lang('Order Tracking') }}</span></a>
</li>

<li>
	<a href="{{ route('orders.index') }}"><i class="fas fa-history"></i><span>{{ _lang('Order History') }}</span></a>
</li>

<li>
	<a href="{{ route('customers.index') }}"><i class="fas fa-user-friends"></i><span>{{ _lang('Customers') }}</span></a>
</li>

<li>
	<a href="{{ route('vendors.index') }}"><i class="fas fa-user-friends"></i><span>{{ _lang('Vendors') }}</span></a>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-shopping-bag"></i><span>{{ _lang('Purchases') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
        <li class="nav-item"><a class="nav-link" href="{{ route('purchase_items.index') }}">{{ _lang('Products') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('purchases.create') }}">{{ _lang('New Purchase') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('purchases.index') }}">{{ _lang('Purchase History') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-university"></i><span>{{ _lang('Expenses') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
        <li class="nav-item"><a class="nav-link" href="{{ route('transactions.index') }}">{{ _lang('Manage Expenses') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('transaction_categories.index') }}">{{ _lang('Expense Categories') }}</a></li>
	</ul>
</li>

@if(package()->payroll_module == 1)
<li>
	<a href="javascript: void(0);"><i class="fas fa-money-check-alt"></i><span>{{ _lang('HR & Payroll') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('staffs.index') }}">{{ _lang('Staff Management') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('departments.index') }}">{{ _lang('Departments') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('attendance.index') }}">{{ _lang('Staff Attendance') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('salary_scales.index') }}">{{ _lang('Salary Pay Scale') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('payslips.index') }}">{{ _lang('Manage Payslip') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('payslips.make_payment') }}">{{ _lang('Make Payment') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('holidays.index') }}">{{ _lang('Holidays') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('leaves.index') }}">{{ _lang('Leaves') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('awards.index') }}">{{ _lang('Awards') }}</a></li>
    </ul>
</li>
@endif

<li>
	<a href="javascript: void(0);"><i class="far fa-chart-bar"></i><span>{{ _lang('Reports') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.sales_report') }}">{{ _lang('Sales Report') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.item_wise_sales_report') }}">{{ _lang('Item Wise Sales') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.profit_and_loss') }}">{{ _lang('Profit & Loss Report') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.expense_report') }}">{{ _lang('Expense Report') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.purchase_report') }}">{{ _lang('Purchases Report') }}</a></li>
		@if(package()->payroll_module == 1)
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.attendance_report') }}">{{ _lang('Attendance Report') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.payroll_report') }}">{{ _lang('Payroll Report') }}</a></li>
        @endif
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.tax_report') }}">{{ _lang('Tax Report') }}</a></li>
    </ul>
</li>

@if(request()->isOwner)
<li>
	<a href="javascript: void(0);"><i class="fas fa-tools"></i><span>{{ _lang('Administration') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a href="{{ route('business.settings', request()->activeBusiness->id) }}">{{ _lang('System Settings') }}</a></li>
		<li class="nav-item"><a href="{{ route('transaction_methods.index') }}">{{ _lang('Transaction Methods') }}</a></li>
		<li class="nav-item"><a href="{{ route('taxes.index') }}">{{ _lang('Tax Settings') }}</a></li>
		<li class="nav-item"><a href="{{ route('business.index') }}">{{ _lang('Manage Restaurant') }}</a></li>
		<li class="nav-item"><a href="{{ route('roles.index') }}">{{ _lang('Roles & Permission') }}</a></li>
    </ul>
</li>
@endif