<?php

use Illuminate\Database\Seeder;
use Foobooks\Tag;

class TagsTableSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        $data = ['novel','fiction','classic','wealth','women','autobiography','nonfiction'];

        foreach($data as $tagName) {
            $tag = new Tag();
            $tag->name = $tagName;
            $tag->save();
        }
    }
}
