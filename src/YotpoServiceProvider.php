<?php
/**
 * Urb_Yotpo
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Urb
 * @package        Urb_Yotpo
 * @copyright      Copyright (c) 2017
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         george <george@underratedbrilliance.com>
 */

namespace Urb\Yotpo;

use Illuminate\Support\ServiceProvider;

class YotpoServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton(Yotpo::class, function($app) {


            return new Yotpo([
                'app_key'                 => config('yotpo.app_key',null),
                'app_secret'              => config('yotpo.app_secret',null),
                'http_client_handler'     => config('yotpo.http_client_handler','curl'),
            ]);
        });

        $this->app[Yotpo::class] = $this->app->singleton(
            function($app) {
                $app['Yotpo.loaded'] = true;
                dd(config('yotpo'));
                return new Yotpo([
                    'app_key'                 => config('yotpo.app_key',null),
                    'app_secret'              => config('yotpo.app_secret',null),
                    'http_client_handler'     => config('yotpo.http_client_handler','curl'),
                ]);
            }
        );
        $this->app->singleton(YotpoLatestReviews::class, function($app) {
            return new YotpoLatestReviews(
               new Yotpo([
                   'app_key'                 => config('yotpo.app_key',null),
                   'app_secret'              => config('yotpo.app_secret',null),
                   'http_client_handler'     => config('yotpo.http_client_handler','curl'),
               ])
            );
        });


        $this->app->alias('yotpo', Yotpo::class);
    }

    public function boot()
    {
        $app = $this->app;

        $configPath = __DIR__ .'/../config/yotpo.php';
        $this->publishes([$configPath => config_path('yotpo.php')], 'config');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Yotpo::class,
            YotpoLatestReviews::class,
            'yotpo'
        ];
    }
}
