<?php
namespace Foobooks\Http\Controllers;
use Illuminate\Http\Request;
use Foobooks\Http\Requests;
use Rych\Random\Random;
use Foobooks\Utilities\Quote;
use Carbon;
use DB;
use Foobooks\Book;
use Foobooks\Author;

# Access via /example/#
class PracticeController extends Controller
{
    public function example20() {
        $books = Book::with('tags')->get();

        foreach($books as $book) {
            dump($book->title.' is tagged with: ');
            foreach($book->tags as $tag) {
                dump($tag->name);
            }
        }
    }

    public function example19() {
        $book = Book::where('title', '=', 'The Great Gatsby')->first();

        dump($book->title);

        foreach($book->tags as $tag) {
            dump($tag->name);
        }
    }

    public function example18() {
        # Eager load the author with the book
        $books = Book::with('author')->get();
        # better than $books = Book::get();

        foreach($books as $book) {
            echo $book->author->first_name.' '.$book->author->last_name.' wrote '.$book->title.'<br>';
        }

        dump($books->toArray());
    }

    public function example17() {
        # Get the first book as an example
        $book = Book::first();

        # Get the author from this book using the "author" dynamic property
        # "author" corresponds to the the relationship method defined in the Book model
        $author = $book->author;

        # Output
        dump($book->title.' was written by '.$author->first_name.' '.$author->last_name);
        dump($book->toArray());
    }

    public function example16() {
        # To do this, we'll first create a new author:
        $author = new Author;
        $author->first_name = 'J.K';
        $author->last_name = 'Rowling';
        $author->bio_url = 'https://en.wikipedia.org/wiki/J._K._Rowling';
        $author->birth_year = '1965';
        $author->save();
        dump($author->toArray());

        # Then we'll create a new book and associate it with the author:
        $book = new Book;
        $book->title = "Harry Potter and the Philosopher's Stone";
        $book->published = 1997;
        $book->cover = 'http://prodimage.images-bn.com/pimages/9781582348254_p0_v1_s118x184.jpg';
        $book->purchase_link = 'http://www.barnesandnoble.com/w/harrius-potter-et-philosophi-lapis-j-k-rowling/1102662272?ean=9781582348254';
        $book->author()->associate($author); # <--- Associate the author with this book
        $book->save();
        dump($book->toArray());
    }

    /**
    * Practice from notes on Models:
    * Remove any books by the author “J.K. Rowling”
    */
    public function example96() {

        Book::where('author','LIKE','J.K. Rowling')->delete();

        # Resulting SQL: delete from `books` where `author` LIKE 'J.K. Rowling'

        return 'Deleted all books where author like J.K. Rowling';
    }


    /**
    * Practice from notes on Models:
    * Find any books by the author Bell Hooks and update the author name to be bell hooks (lowercase).
    */
    public function example95() {

        # Approach # 1
        # Get all the books that match the criteria
        $books = Book::where('author','=','Bell Hooks')->get();

        # Loop through each book and update them
        foreach($books as $book) {
            $book->author = 'bell hooks';
            $book->save();
        }

        # Resulting SQL:
        # Always:
        #   1) select * from `books` where `author` = 'Bell Hooks'
        # Only if there's something to update:
        #   2) update `books` set `updated_at` = '2016-04-12 18:46:04', `author` = 'bell hooks' where `id` = '8'

        # Approach #2
        Book::where('author', '=', 'Bell Hooks')->update(['author' => 'bell hooks']);
        # Resulting SQL:
        # Always:
        #   1) update `books` set `author` = 'bell hooks', `updated_at` = '2016-04-12 18:44:46' where `author` = 'Bell Hooks'

        return '"Bell Hooks" => "bell hooks"';
    }


    /**
    * Practice from notes on Models:
    * Retrieve all the books in descending order according to published date
    */
    public function example94() {

        $books = Book::orderBy('published','desc')->get();

        dump($books->toArray());

        # Underlying SQL: select * from `books` order by `published` desc

    }


    /**
    * Practice from notes on Models:
    * Retrieve all the books in alphabetical order by title
    */
    public function example93() {

        $books = Book::orderBy('title','asc')->get();

        dump($books->toArray());

        # Underlying SQL: select * from `books` order by `title` asc
    }


    /**
    * Practice from notes on Models:
    * Retrieve all the books published after 1950
    */
    public function example92() {

        $books = Book::where('published','>',1950)->get();

        dump($books->toArray());

        # Underlying SQL: select * from `books` where `published` > '1950'

    }


    /**
    * Practice from notes on Models:
    * Show the last 5 books that were added to the books table
    */
    public function example91() {

        # Ref: https://laravel.com/docs/5.2/queries#ordering-grouping-limit-and-offset
        $books = Book::orderBy('id', 'desc')->get()->take(5);

        dump($books->toArray());

        # Underlying SQL: select * from `books` order by `id` desc

    }

    public function example15() {
        /*
        2 separate queries on the database:
        */
        // $books = Book::orderBy('id','descending')->get(); # Query DB
        // $first_book = Book::orderBy('id','descending')->first(); # Query DB
        // dump($books);
        /*
        1 query on the database, 1 query on the collection (better):
        */
        $books = Book::orderBy('id','descending')->get(); # Query DB
        $first_book = $books->first(); # Query Collection
        dump($books);
        dump($first_book);
    }

    public function example14() {
        $books = Book::all();
        dump($books);
        echo $books.'<br>';
        foreach($books as $book) {
            echo $book->title;
            echo $book['title'];
        }
    }

    /**
    * Delete a book via the Book model
    */
    public function example13() {
        # First get a book to delete
        $book = Book::where('author', 'LIKE', '%Scott%')->first();
        # If we found the book, delete it
        if($book) {
            # Goodbye!
            $book->delete();
            return "Deletion complete; check the database to see if it worked...";
        }
        else {
            return "Can't delete - Book not found.";
        }
    }
    /**
    * Update a book via the Book model
    */
    public function example12() {
        # First get a book to update
        $book = Book::where('author', 'LIKE', '%Scott%')->first();
        # If we found the book, update it
        if($book) {
            # Give it a different title
            $book->title = 'The Really Great Gatsby';
            # Save the changes
            $book->save();
            echo "Update complete; check the database to see if your update worked...";
        }
        else {
            echo "Book not found, can't update.";
        }
    }
    /**
    * Get all the books via the Book model
    */
    public function example11() {
        $books = Book::all();
        # Make sure we have results before trying to print them...
        if(!$books->isEmpty()) {
            # Output the books
            foreach($books as $book) {
                echo $book->title.'<br>';
            }
        }
        else {
            echo 'No books found';
        }
    }
    /**
    * Create a new book via the Book model
    */
    public function example10() {
        # Instantiate a new Book Model object
        $book = new Book();
        # Set the parameters
        # Note how each parameter corresponds to a field in the table
        $book->title = 'Harry Potter';
        $book->author = 'J.K. Rowling';
        $book->published = 1997;
        $book->cover = 'http://prodimage.images-bn.com/pimages/9780590353427_p0_v1_s484x700.jpg';
        $book->purchase_link = 'http://www.barnesandnoble.com/w/harry-potter-and-the-sorcerers-stone-j-k-rowling/1100036321?ean=9780590353427';
        # Invoke the Eloquent save() method
        # This will generate a new row in the `books` table, with the above data
        $book->save();
        echo 'Added: '.$book->title;
    }
    /**
    * Update a book using the Query builder.
    *
    */
    public function example9() {
        # This was how I wrote it in lecture and it wasn't working:
        //$book = DB::table('books')->find(2)->update(['title' => 'foobar']);
        # This does work:
        $book = DB::table('books')->where('id','=','2')->update(['title' => 'foobar']);
        # Upon closer inspection, it appears that update has to work on a "Builder" instance
        # The following two dumps demonstrate the difference
        dump(DB::table('books')->find(2)); # Gets a Object with the book data
        dump(DB::table('books')->where('id','=','2')); # Gets a Builder instance
    }
    /**
    * Create a book using QueryBuilder
    */
    public function example8() {
        DB::table('books')->insert([
            'created_at' => Carbon\Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon\Carbon::now()->toDateTimeString(),
            'title' => 'The Great Gatsby',
            'author' => 'F. Scott Fitzgerald',
            'published' => 1925,
            'cover' => 'http://img2.imagesbn.com/p/9780743273565_p0_v4_s114x166.JPG',
            'purchase_link' => 'http://www.barnesandnoble.com/w/the-great-gatsby-francis-scott-fitzgerald/1116668135?ean=9780743273565',
        ]);
        echo 'Added a new book';
    }
    /**
    * Get all the books using QueryBuilder
    */
    public function example7() {
        # Use the QueryBuilder to get all the books
        $books = DB::table('books')->get();
        # Output the results
        foreach ($books as $book) {
            echo $book->title;
        }
    }
    /**
    * Debugging curl extension for students;
    * ref: https://piazza.com/class/iqiwxxw3sex3r2?cid=143
    */
    public function example6() {
        return \Magyarjeti\LaravelLipsum\LipsumFacade::html();
    }
    /**
    *
    */
    public function example5() {
        $quote = new Quote();
        return $quote->getRandomQuote();
    }
    /**
    *
    */
    public function example4() {
        $random = new Random();
        return $random->getRandomString(8);
    }
    /**
    *
    */
    public function example3() {
        echo \App::environment();
        echo 'App debug: '.config('app.debug');
    }
    /**
    * Demonstrates useful data output methods like dump, and dd
    */
    public function example2() {
        $fruits = ['apples','oranges','pears'];
        dump($fruits);
        var_dump($fruits);
        dd($fruits);
    }
    /**
    *
    */
    public function example1() {
        return 'This is example 1';
    }
    /**
    * Display an index of all available index methods
    */
    public function index() {
        # Get all the methods in this class
        $actionsMethods = get_class_methods($this);
        # Loop through all the methods
        foreach($actionsMethods as $actionMethod) {
            # Only if the method includes the word "example"...
            if(strstr($actionMethod, 'example')) {
                # Display a link to that method's route
                echo '<a target="_blank" href="/example/'.str_replace('example','',$actionMethod).'">'.$actionMethod.'</a>';
            }
        }
    }
}
