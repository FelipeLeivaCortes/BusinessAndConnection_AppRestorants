<li>
	<a href="{{ route('dashboard.index') }}"><i class="fas fa-th-large"></i><span>{{ _lang('Dashboard') }}</span></a>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-gift"></i><span>{{ _lang('Packages') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('packages.index') }}">{{ _lang('All Packages') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('packages.create') }}">{{ _lang('Add New') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-user-friends"></i><span>{{ _lang('Users') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">{{ _lang('All Users') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('users.create') }}">{{ _lang('Add User') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-credit-card"></i><span>{{ _lang('Payments') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('subscription_payments.index') }}">{{ _lang('Payment History') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('subscription_payments.create') }}">{{ _lang('Add Offline Payment') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('payment_gateways.index') }}">{{ _lang('Payment Gateways') }}</a></li>		
	</ul>
</li>

<li><a href="{{ route('pages.default_pages') }}"><i class="far fa-file-alt"></i><span>{{ _lang('Default Pages') }}</span></a></li>
<li><a href="{{ route('pages.index') }}"><i class="fas fa-file-alt"></i><span>{{ _lang('Custom Pages') }}</span></a></li>
<li><a href="{{ route('faqs.index') }}"><i class="far fa-question-circle"></i><span>{{ _lang('Manage FAQ') }}</span></a></li>
<li><a href="{{ route('features.index') }}"><i class="fas fa-compass"></i><span>{{ _lang('Manage Features') }}</span></a></li>
<li><a href="{{ route('testimonials.index') }}"><i class="far fa-star"></i><span>{{ _lang('Testimonials') }}</span></a></li>
<li><a href="{{ route('posts.index') }}"><i class="fas fa-rss"></i><span>{{ _lang('Manage Blogs') }}</span></a></li>
<li><a href="{{ route('teams.index') }}"><i class="fas fa-user-circle"></i><span>{{ _lang('Manage Teams') }}</span></a></li>
<li><a href="{{ route('pages.default_pages', 'header_footer') }}"><i class="fas fa-brush"></i><span>{{ _lang('Header & Footer Settings') }}</span></a></li>
<li><a href="{{ route('pages.default_pages', 'gdpr_cookie_consent') }}"><i class="fas fa-cookie-bite"></i><span>{{ _lang('GDPR Cookie Consent') }}</span></a></li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-globe"></i><span>{{ _lang('Languages') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('languages.index') }}">{{ _lang('All Language') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('languages.create') }}">{{ _lang('Add New') }}</a></li>
	</ul>
</li>

<li><a href="{{ route('email_subscribers.index') }}"><i class="far fa-envelope"></i><span>{{ _lang('Email Subscribers') }}</span></a></li>

<li><a href="{{ route('settings.update_settings') }}"><i class="fas fa-cog"></i><span>{{ _lang('System Settings') }}</span></a></li>
<li><a href="{{ route('currency.index') }}"><i class="fas fa-dollar-sign"></i><span>{{ _lang('Currency Management') }}</span></a></li>
<li><a href="{{ route('notification_templates.index') }}"><i class="fas fa-envelope-open-text"></i><span>{{ _lang('Notification Templates') }}</span></a></li>
<li><a href="{{ route('database_backups.list') }}"><i class="fas fa-server"></i><span>{{ _lang('Database Backup') }}</span></a></li>