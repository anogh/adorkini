<?php
/*
Template Name: Account
*/

// Redirect logged-in users to my-account page
if (is_user_logged_in()) {
    wp_redirect(home_url('/my-account'));
    exit;
}

get_header(); ?>

<main class="flex-grow">
    <div class="container mx-auto px-4 py-8 max-w-md">
        <!-- Mobile Header -->
        <div class="lg:hidden mb-6">
            <div class="flex items-center gap-4">
                <button onclick="history.back()" class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full hover:bg-gray-200/50 dark:hover:bg-gray-700/50">
                    <span class="material-symbols-outlined text-gray-600 dark:text-gray-300" data-icon="arrow_back"></span>
                </button>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white"><?php echo __t('Account'); ?></h1>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 dark:border-gray-700 dark:bg-background-dark">
            <!-- Account Icon -->
            <div class="text-center mb-6">
                <div class="flex justify-center mb-4">
                    <span class="material-symbols-outlined text-5xl text-gray-400 dark:text-gray-500" data-icon="account_circle"></span>
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white"><?php echo __t('Welcome to Your Account'); ?></h2>
                <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm"><?php echo __t('Please log in or register to access your account features'); ?></p>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <!-- Login Button (Primary) -->
                <a href="<?php echo esc_url(home_url('/login')); ?>" class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-base font-bold hover:bg-primary/90 transition-colors">
                    <span class="material-symbols-outlined mr-2 text-xl" data-icon="login"></span>
                    <span><?php echo __t('Login'); ?></span>
                </a>
                
                <!-- Register Button (Secondary/Outline) -->
                <a href="<?php echo esc_url(home_url('/register')); ?>" class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-white border border-gray-300 text-gray-700 text-base font-bold hover:bg-gray-50 transition-colors dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    <span class="material-symbols-outlined mr-2 text-xl" data-icon="person_add"></span>
                    <span><?php echo __t('Register'); ?></span>
                </a>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
