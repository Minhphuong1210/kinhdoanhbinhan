<?php
namespace App\Modules\CategoryPost\Models;

use App\Modules\Post\Models\Post;
use Illuminate\Database\Eloquent\Model;

class CategoryPost extends Model
{
    protected $table = 'category_posts';
    protected $fillable = ['name','slug','active'];

    public function posts()
    {
        return $this->hasMany(Post::class, 'category_post_id');
    }
}
