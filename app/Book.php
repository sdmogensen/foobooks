<?php

namespace Foobooks;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /* Relationship methods */
    public function author() {
        # Book belongs to Author
        # Define an inverse one-to-many relationship.
        return $this->belongsTo('Foobooks\Author');
    }

    public function tags()
    {
        # With timetsamps() will ensure the pivot table has its created_at/updated_at fields automatically maintained
        return $this->belongsToMany('Foobooks\Tag')->withTimestamps();
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
