<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('barengin:about', function () {
    $this->info('Barengin REST API backend is ready for development.');
})->purpose('Display Barengin backend information');
