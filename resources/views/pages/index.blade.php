<x-main-layout>
    @include('components.home.hero-modern')
    <x-home.news :latestNews="$latestNews" :otherNews="$otherNews" :announcements="$announcements" />
    @include('components.home.edu-links')
    <x-home.research-activities :announcementsWithActivity="$announcementsWithActivity" />
    <x-home.faculty :faculties="$faculties" />
    <x-home.pointers :stat="$stat" />
    <x-home.departments :departments="$departments" />
    <x-home.gallery :galleryImages="$galleryImages" />
    <x-home.video-gallery />
    <x-home.tags :tags="$tags" />
</x-main-layout>
