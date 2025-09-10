<?php

namespace Formation\DataTable;

use Illuminate\Database\Eloquent\Model;

use Spatie\Translatable\HasTranslations;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

trait WithTranslation
{
    use HasTranslations {
        getCasts as defaultGetCasts;
        getAttributeValue as defaultGetAttributeValue;
        setAttribute as defaultSetAttribute;
    }

    public static function boot()
    {
        parent::boot();

        static::retrieved(function (Model $model) {
            $locale = App::getLocale() ?: config('app.fallback_locale', 'en');
            foreach($model->getTranslatableAttributes() as $attribute) {
                $model->{$attribute} = $model->getTranslation($attribute, $locale);
            }
        });
    }

    public function getCasts(): array
    {
        if(Str::startsWith(Request::path(), 'api')) {
            return parent::getCasts();
        } else {
            return $this->defaultGetCasts();
        }
    }

    public function getAttributeValue($key): mixed
    {
        if(Str::startsWith(Request::path(), 'api')) {
            return parent::getAttributeValue($key);
        } else {
            return $this->defaultGetAttributeValue($key);
        }
    }

    public function setAttribute($key, $value)
    {
        if(Str::startsWith(Request::path(), 'api')) {
            return parent::setAttribute($key, $value);
        } else {
            return $this->defaultSetAttribute($key, $value);
        }
    }
}