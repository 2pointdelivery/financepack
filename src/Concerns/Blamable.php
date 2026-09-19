<?php

namespace FinancePack\Concerns;

use Illuminate\Database\Eloquent\Model;

trait Blamable
{
    public static function bootBlamable(): void
    {
        static::creating(function (Model $model) {
            if (is_null($model->created_by) && auth()->check()) {
                $model->created_by = auth()->id();
            }
            if (is_null($model->updated_by) && auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        static::updating(function (Model $model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }
}
