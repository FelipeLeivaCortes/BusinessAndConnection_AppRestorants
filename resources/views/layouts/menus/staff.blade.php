<li>
	<a href="{{ route('dashboard.index') }}"><i class="fas fa-th-large"></i><span>{{ _lang('Dashboard') }}</span></a>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-utensils"></i><span>{{ _lang('Food Items') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if(has_permission('categories.index'))
        <li class="nav-item"><a class="nav-link" href="{{ route('categories.index') }}">{{ _lang('Categories') }}</a></li>
		@endif

		@if(has_permission('products.index'))
        <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">{{ _lang('Food Items') }}</a></li>
		@endif
	</ul>
</li>

@if(has_permission('halls.index'))
<li>
	<a href="{{ route('halls.index') }}"><i class="fas fa-border-none"></i><span>{{ _lang('Table & Hall Plans') }}</span></a>
</li>
@endif

@if(has_permission('pos.table'))
<li>
	<a href="{{ route('pos.table') }}" target="_blank"><i class="fas fa-tablet-alt"></i><span>{{ _lang('Point Of Sale') }}</span></a>
</li>
@endif

@if(has_permission('orders.tracking'))
<li>
	<a href="{{ route('orders.tracking') }}"><i class="fas fa-broadcast-tower"></i><span>{{ _lang('Order Tracking') }}</span></a>
</li>
@endif

@if(has_permission('orders.index'))
<li>
	<a href="{{ route('orders.index') }}"><i class="fas fa-history"></i><span>{{ _lang('Order History') }}</span></a>
</li>
@endif

@if(has_permission('customers.index'))
<li>
	<a href="{{ route('customers.index') }}"><i class="fas fa-user-friends"></i><span>{{ _lang('Customers') }}</span></a>
</li>
@endif

@if(has_permission('vendors.index'))
<li>
	<a href="{{ route('vendors.index') }}"><i class="fas fa-user-friends"></i><span>{{ _lang('Vendors') }}</span></a>
</li>
@endif

<li>
	<a href="javascript: void(0);"><i class="fas fa-shopping-bag"></i><span>{{ _lang('Purchases') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if(has_permission('purchase_items.index'))
        <li class="nav-item"><a class="nav-link" href="{{ route('purchase_items.index') }}">{{ _lang('Products') }}</a></li>
        @endif

		@if(has_permission('purchases.index'))
		<li class="nav-item"><a class="nav-link" href="{{ route('purchases.create') }}">{{ _lang('New Purchase') }}</a></li>
        @endif

		@if(has_permission('purchases.index'))
		<li class="nav-item"><a class="nav-link" href="{{ route('purchases.index') }}">{{ _lang('Purchase History') }}</a></li>
		@endif
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-university"></i><span>{{ _lang('Expenses') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if(has_permission('transactions.index'))
        <li class="nav-item"><a class="nav-link" href="{{ route('transactions.index') }}">{{ _lang('Manage Expenses') }}</a></li>
        @endif

		@if(has_permission('transaction_categories.index'))
		<li class="nav-item"><a class="nav-link" href="{{ route('transaction_categories.index') }}">{{ _lang('Expense Categories') }}</a></li>
		@endif
	</ul>
</li>

@if(package()->payroll_module == 1)
<li>
	<a href="javascript: void(0);"><i class="fas fa-money-check-alt"></i><span>{{ _lang('HR & Payroll') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if(has_permission('staffs.index'))
		<li class="nav-item"><a class="nav-link" href="{{ route('staffs.index') }}">{{ _lang('Staff Management') }}</a></li>
		@endif

		@if(has_permission('departments.index'))
		<li class="nav-item"><a class="nav-link" href="{{ route('departments.index') }}">{{ _lang('Departments') }}</a></li>
		@endif

		@if(has_permission('attendance.index'))
		<li class="nav-item"><a class="nav-link" href="{{ route('attendance.index') }}">{{ _lang('Staff Attendance') }}</a></li>
		@endif

		@if(has_permission('salary_scales.index'))
		<li class="nav-item"><a class="nav-link" href="{{ route('salary_scales.index') }}">{{ _lang('Salary Pay Scale') }}</a></li>
		@endif

		@if(has_permission('payslips.index'))
		<li class="nav-item"><a class="nav-link" href="{{ route('payslips.index') }}">{{ _lang('Manage Payslip') }}</a></li>
		@endif

		@if(has_permission('payslips.make_payment'))
		<li class="nav-item"><a class="nav-link" href="{{ route('payslips.make_payment') }}">{{ _lang('Make Payment') }}</a></li>
		@endif

		@if(has_permission('holidays.index'))
		<li class="nav-item"><a class="nav-link" href="{{ route('holidays.index') }}">{{ _lang('Holidays') }}</a></li>
		@endif

		@if(has_permission('leaves.index'))
		<li class="nav-item"><a class="nav-link" href="{{ route('leaves.index') }}">{{ _lang('Leaves') }}</a></li>
		@endif

		@if(has_permission('awards.index'))
		<li class="nav-item"><a class="nav-link" href="{{ route('awards.index') }}">{{ _lang('Awards') }}</a></li>
		@endif
    </ul>
</li>
@endif

<li>
	<a href="javascript: void(0);"><i class="far fa-chart-bar"></i><span>{{ _lang('Reports') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if(has_permission('reports.sales_report'))
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.sales_report') }}">{{ _lang('Sales Report') }}</a></li>
		@endif

		@if(has_permission('reports.item_wise_sales_report'))
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.item_wise_sales_report') }}">{{ _lang('Item Wise Sales') }}</a></li>
		@endif

		@if(has_permission('reports.profit_and_loss'))
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.profit_and_loss') }}">{{ _lang('Profit & Loss Report') }}</a></li>
		@endif

		@if(has_permission('reports.expense_report'))
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.expense_report') }}">{{ _lang('Expense Report') }}</a></li>
		@endif

		@if(has_permission('reports.purchase_report'))
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.purchase_report') }}">{{ _lang('Purchases Report') }}</a></li>
		@endif

		@if(package()->payroll_module == 1)
		@if(has_permission('reports.attendance_report'))
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.attendance_report') }}">{{ _lang('Attendance Report') }}</a></li>
		@endif
		@if(has_permission('reports.payroll_report'))
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.payroll_report') }}">{{ _lang('Payroll Report') }}</a></li>
        @endif
        @endif

		@if(has_permission('reports.tax_report'))
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.tax_report') }}">{{ _lang('Tax Report') }}</a></li>
		@endif
	</ul>
</li>