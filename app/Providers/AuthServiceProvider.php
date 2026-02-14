<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\StaffMember;
use App\Policies\PagePolicy;
use App\Policies\StaffMemberPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        StaffMember::class => StaffMemberPolicy::class,
        Page::class => PagePolicy::class,
    ];

    public function boot(): void
    {
        // agar Gate qo‘shimcha kerak bo‘lsa shu yerda yozasiz
    }
}