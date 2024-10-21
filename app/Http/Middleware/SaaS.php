<?php

namespace App\Http\Middleware;

use app\Models\Business;
use Closure;
use Illuminate\Support\Facades\Session;

class SaaS {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		if (auth()->check()) {
			$user = auth()->user();
			$routeName = $request->route()->getName();

			if (!$request->has('businessList') && $user->user_type == 'user') {

				$business = $user->business();
				$businessList = $business->withoutGlobalScopes()->get();

				if (!$businessList->isEmpty()) {
					$activeBusiness = $business->withoutGlobalScopes()->wherePivot('is_active', 1)->with('user.package')->first();
					if ($activeBusiness == null) {
						$activeBusiness = $user->business()->withoutGlobalScopes()->with('user.package')->first();
						$user->business()->updateExistingPivot($activeBusiness->id, ['is_active' => 1]);
					}
					$isOwner = $activeBusiness->pivot->owner_id == $user->id ? true : false;
					$permissionList = $user->select('permissions.*')
						->join('business_users', 'users.id', 'business_users.user_id')
						->join('business', 'business_users.business_id', 'business.id')
						->join('permissions', 'business_users.role_id', 'permissions.role_id')
						->where('business.id', $activeBusiness->id)
						->where('users.id', $user->id)
						->get();

					date_default_timezone_set(get_business_option('timezone', 'Asia/Dhaka'));

					$request->merge([
						'businessList' => $businessList,
						'activeBusiness' => $activeBusiness,
						'isOwner' => $isOwner,
						'permissionList' => $permissionList,
					]);

					if ($activeBusiness->user->package_id != null && $activeBusiness->user->getRawOriginal('valid_to') < date('Y-m-d')) {
						if ($isOwner) {
							if ($routeName != 'business.switch_business') {
								return redirect()->route('membership.payment_gateways')->with('error', _lang("Please make your subscription payment"));
							}
						} else {
							if ($routeName != 'dashboard.index' && $routeName != 'business.switch_business') {
								return redirect()->route('dashboard.index')->with('error', _lang("Your selected business subscription is expired"));
							}
							Session::flash('error', _lang("Your selected business subscription is expired"));
						}
					}

					//IF PASSWORD IS NULL
					if ($user->password == NULL) {
						if ($routeName != 'profile.change_password' && $routeName != 'profile.update_password') {
							return redirect()->route('profile.change_password')->with('success', _lang("Invitation Accepted. Please create your password for further login"));
						}
					}

				} else {
					if ($user->package_id != null && ($user->getRawOriginal('valid_to') < date('Y-m-d') || $user->getRawOriginal('valid_to') == null)) {
						$package = $user->package;

						//Apply Free Package
						if ($package->cost == 0) {
							$user->membership_type = 'member';
							$user->subscription_date = now();
							$user->valid_to = update_membership_date($package, $user->getRawOriginal('subscription_date'));
							$user->s_email_send_at = null;
							$user->save();
						}

						//Apply Trial Package
						if ($package->cost > 0 && $package->trial_days > 0 && $user->membership_type == '') {
							$user->membership_type = 'trial';
							$user->subscription_date = now();
							$user->valid_to = date('Y-m-d', strtotime($user->getRawOriginal('subscription_date') . " + $package->trial_days days"));
							$user->save();
						}
					}

					if ($user->package_id != null) {
						$business = Business::createDefaultBusiness();
						return redirect()->route('business.edit', $business->id)->with('error', _lang("Please update your default business account"));
					}

					if ($user->package_id == null) {
						return redirect()->route('membership.packages')->with('error', _lang("Please choose your package first"));
					}

				}

			}
		}

		return $next($request);
	}
}
