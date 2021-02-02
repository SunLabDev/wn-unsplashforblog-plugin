<?php namespace SunLab\UnsplashForBlog;

use Backend;
use Illuminate\Support\Facades\Event;
use System\Classes\PluginBase;
use System\Classes\PluginManager;

class Plugin extends PluginBase
{
    public $require = ['SunLab.UnsplashPicker'];

    public function pluginDetails()
    {
        return [
            'name'        => 'UnsplashForBlog',
            'description' => 'No description provided yet...',
            'author'      => 'SunLab',
            'icon'        => 'icon-leaf'
        ];
    }

    public function boot()
    {
        if (\SunLab\UnsplashPicker\Models\Settings::instance()->isConfigured()) {
            $pluginManager = PluginManager::instance();

            if ($pluginManager->hasPlugin('RainLab\Blog')) {
                $this->applyOnRainLabBlog();
            }

            if ($pluginManager->hasPlugin('Lovata\GoodNews')) {
                $this->applyOnLovataGoodNews();
            }
        }
    }

    protected function applyOnRainLabBlog()
    {
        Event::listen('backend.form.extendFields', static function (Backend\Widgets\Form $widget) {
            if (!$widget->getController() instanceof \RainLab\Blog\Controllers\Posts) {
                return;
            }

            if (!$widget->model instanceof \RainLab\Blog\Models\Post) {
                return;
            }

            $fileuploadField = $widget->getField('featured_images');
            $fileuploadField->config['type'] = 'unsplashpicker';

            $widget->removeField('featured_images');

            $widget->addSecondaryTabFields([
                'featured_images' => $fileuploadField->config
            ]);
        });
    }

    protected function applyOnLovataGoodNews()
    {
        Event::listen('backend.form.extendFields', static function (Backend\Widgets\Form $widget) {
            if (!$widget->getController() instanceof \Lovata\GoodNews\Controllers\Articles) {
                return;
            }

            if (!$widget->model instanceof \Lovata\GoodNews\Models\Article) {
                return;
            }

            $fileuploadField = $widget->getField('preview_image');
            $fileuploadField->config['type'] = 'unsplashpicker';

            $widget->removeField('preview_image');

            $widget->addTabFields([
                'preview_image' => $fileuploadField->config
            ]);
        });
    }
}
