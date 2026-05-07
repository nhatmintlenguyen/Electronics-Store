<?php
declare(strict_types=1);

class PageController
{
    public function about(): void
    {
        view('pages/about.php', [
            'page_title' => t('about_us'),
            'page_description' => 'Information about TechStore and how the website meets the project requirements.',
        ]);
    }

    public function contact(): void
    {
        view('pages/contact.php', [
            'page_title' => t('contact'),
            'page_description' => 'Contact information, website overview, and ways to connect with TechStore.',
        ]);
    }

    public function locations(): void
    {
        view('pages/locations.php', [
            'page_title' => 'Stores',
            'page_description' => 'List of TechStore locations with addresses and Google Maps links.',
            'locations' => Location::all(getDBConnection()),
        ]);
    }

    public function profile(): void
    {
        requireLogin();

        $user = User::findProfile(getDBConnection(), (int) $_SESSION['user_id']);

        if (!$user) {
            redirectTo('logout.php');
        }

        view('pages/profile.php', [
            'page_title' => t('profile'),
            'page_description' => 'Account information for the currently signed-in TechStore user.',
            'user' => $user,
        ]);
    }
}
