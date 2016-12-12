<?php

namespace Foobooks;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    /* Relationship methods */
    public function books() {
        # Author has many Books
        # Define a one-to-many relationship.
        return $this->hasMany('Foobooks\Book');
    }

    /* End Relationship Methods */
    /**
    *
    */
    public static function getForDropdown() {
        # Author
        $authors = Author::orderBy('last_name', 'ASC')->get();
        $authors_for_dropdown = [];
        foreach($authors as $author) {
            $authors_for_dropdown[$author->id] = $author->first_name.' '.$author->last_name;
        }
        return $authors_for_dropdown;
    }
}
