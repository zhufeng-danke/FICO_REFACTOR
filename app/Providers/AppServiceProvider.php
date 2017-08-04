<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (isProduction()) {
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        }

        // $src 实际上包含 @css 括号中的所有符号，包括两端的引号
        \Blade::directive('css', function ($src) {
            return \Html::style(cdn(trim($src, "'\"")));
        });

        \Blade::directive('js', function ($src) {
            return \Html::script(cdn(trim($src, "'\"")));
        });

        \Blade::directive('fa', function ($icon) {
            return '<i class="fa fa-' . trim($icon, "'\"") .'"></i>';
        });

        // 自定义验证
        \Validator::resolver(
            function ($translator, array $data, array $rules, array $messages, array $customAttributes) {
                return new \CustomValidator($translator, $data, $rules, $messages, $customAttributes);
            }
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 部分组件只需要在本地加载
        if (!isProduction()) {
            foreach (config('app.providers-dev', []) as $service) {
                $this->app->register($service);
            }
        }
    }
}
