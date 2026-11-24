<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'is_active', 'unsubscribe_token'];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::creating(function (NewsletterSubscriber $subscriber) {
            $subscriber->unsubscribe_token ??= Str::random(48);
        });
    }

    public function unsubscribeUrl(): string
    {
        return route('newsletter.unsubscribe.confirm', ['token' => $this->unsubscribe_token]);
    }
}
