<?php

namespace App\Providers;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\Events\Auth\GoogleAccountLinked;
use App\Events\Auth\GoogleAccountUnlinked;
use App\Events\Auth\SocialAccountRegistered;
use App\Events\EnrolmentActivated;
use App\Events\StudentAccountActivated;
use App\Listeners\Emails\SendAuthenticationEmails;
use App\Listeners\Notifications\SendPortalInAppNotifications;
use App\Models\Enrolment;
use App\Models\WebPage;
use App\Policies\Student\EnrolmentPolicy;
use App\Services\Payments\MockPaymentService;
use App\Services\Payments\PaystackPaymentService;
use App\Support\GoogleAuth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, function () {
            if (config('payments.mock') || config('payments.default') === 'mock') {
                return new MockPaymentService;
            }

            return new PaystackPaymentService;
        });
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        config([
            'authentication.google.enabled' => GoogleAuth::enabled(),
        ]);

        View::composer('web.*', function ($view) {
            $page = WebPage::forRoute(request()->route()?->getName());
            $preview = $page
                && (int) request()->query('cms_preview') === $page->id
                && auth()->check()
                && auth()->user()->can('content.manage');

            if ($page && ! $page->is_published && ! $preview) {
                abort(404);
            }

            $view->with('cmsPage', $page);
            $view->with('cmsPreview', $preview);
        });

        Gate::policy(Enrolment::class, EnrolmentPolicy::class);

        $emailListener = SendAuthenticationEmails::class;

        Event::listen(Registered::class, [$emailListener, 'handleWelcome']);
        Event::listen(SocialAccountRegistered::class, [$emailListener, 'handleSocialRegistered']);
        Event::listen(GoogleAccountLinked::class, [$emailListener, 'handleGoogleLinked']);
        Event::listen(GoogleAccountUnlinked::class, [$emailListener, 'handleGoogleUnlinked']);

        $portalNotifications = SendPortalInAppNotifications::class;
        Event::listen(EnrolmentActivated::class, [$portalNotifications, 'handleEnrolmentActivated']);
        Event::listen(StudentAccountActivated::class, [$portalNotifications, 'handleStudentAccountActivated']);
    }
}
