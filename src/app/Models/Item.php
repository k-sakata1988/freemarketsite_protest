<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'image_path',
        'category_id',
        'condition',
        'brand',
        'status',
        'is_recommended',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function categories(){
        return $this->belongsToMany(Category::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function favorites(){
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function purchase(){
        return $this->hasOne(Purchase::class);
    }

    public function scopeRecommended($query){
        return $query->where('is_recommended', true);
    }
    public function isSoldOut(): bool
    {
        return $this->status === 'sold';
    }
    public function getCategoryIdsAttribute(){
        return $this->category_id ? explode(',', $this->category_id) : [];
    }

    public function getCategoriesAttribute(){
        return Category::whereIn('id', $this->category_ids)->get();
    }

    public function getImageUrlAttribute(){
        if (!$this->image_path) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }

        return asset('storage/' . $this->image_path);
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }
}
