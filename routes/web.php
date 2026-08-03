<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\AboutController;
use App\Http\Controllers\Web\AcademyEnrolmentController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\CourseController;
use App\Http\Controllers\Web\GalleryController;
use App\Http\Controllers\Web\StudentPortalRegistrationController;
use App\Http\Controllers\Web\StoreController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LocaleController;
use App\Http\Controllers\Web\PractitionerController;
use App\Http\Controllers\Web\SeoController;
use App\Http\Controllers\Web\TreatmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('web.home');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/about', [AboutController::class, 'index'])->name('web.about');
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/treatments', [TreatmentController::class, 'index'])->name('web.treatments.index');
Route::get('/treatments/{slug}', [TreatmentController::class, 'show'])->name('web.treatments.show');
Route::get('/practitioners', [PractitionerController::class, 'index'])->name('web.practitioners.index');
Route::get('/academy', [StudentPortalRegistrationController::class, 'create'])->name('web.academy.index');
Route::get('/academy/student-portal', [StudentPortalRegistrationController::class, 'register'])
    ->name('web.academy.student-portal.create');
Route::post('/academy/student-portal', [StudentPortalRegistrationController::class, 'store'])->name('web.academy.student-portal.store');
Route::get('/courses', [CourseController::class, 'index'])->name('web.courses.index');
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('web.courses.show');
Route::get('/store', [StoreController::class, 'index'])->name('web.store.index');
Route::get('/store/{slug}', [StoreController::class, 'show'])->name('web.store.show');
Route::get('/cart', [CartController::class, 'index'])->name('web.cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('web.cart.store');
Route::patch('/cart/items/{item}', [CartController::class, 'update'])->name('web.cart.items.update');
Route::delete('/cart/items/{item}', [CartController::class, 'destroy'])->name('web.cart.items.destroy');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('web.cart.coupon.apply');
Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])->name('web.cart.coupon.remove');
Route::get('/checkout', [CheckoutController::class, 'show'])->name('web.checkout.show');
Route::post('/checkout/pay', [CheckoutController::class, 'pay'])->name('web.checkout.pay');
Route::get('/checkout/processing/{number}', [CheckoutController::class, 'processing'])->name('web.checkout.processing');
Route::get('/checkout/callback', [CheckoutController::class, 'callback'])->name('web.checkout.callback');
Route::get('/checkout/success/{number}', [CheckoutController::class, 'success'])->name('web.checkout.success');
Route::get('/checkout/failure/{reference}', [CheckoutController::class, 'failure'])->name('web.checkout.failure');
Route::get('/payments/mock/{reference}', [CheckoutController::class, 'mockPay'])->name('web.payments.mock');
Route::post('/payments/mock/{reference}/complete', [CheckoutController::class, 'mockComplete'])->name('web.payments.mock.complete');
Route::get('/gallery', [GalleryController::class, 'index'])->name('web.gallery');
Route::get('/blog', [BlogController::class, 'index'])->name('web.blog.index');
Route::get('/blog/{post}', [BlogController::class, 'show'])->name('web.blog.show');
Route::view('/contact', 'web.contact.index')->name('web.contact');
Route::get('/enrol', [AcademyEnrolmentController::class, 'create'])->name('web.enrol');
Route::post('/enrol', [AcademyEnrolmentController::class, 'store'])->name('web.enrol.store');

Route::get('/book', [BookingController::class, 'create'])->name('web.booking.create');
Route::get('/book/slots', [BookingController::class, 'slots'])->name('web.booking.slots');
Route::post('/book', [BookingController::class, 'store'])->name('web.booking.store');
Route::get('/book/confirmation/{reference}', [BookingController::class, 'confirmation'])
    ->name('web.booking.confirmation');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user) {
        return redirect()->route($user->portalHomeRoute());
    }

    return redirect()->route('login');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
