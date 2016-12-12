<?php
namespace Foobooks\Http\Controllers;
use Illuminate\Http\Request;
use Foobooks\Http\Requests;
use Foobooks\Book;
use Foobooks\Author;
use Foobooks\Tag;
use Session;

class BookController extends Controller
{
    /**
    * GET
    */
    public function index(Request $request)
    {

        $user = $request->user();

        # Note: We're getting the user from the request, but you can also get it like this:
        //$user = Auth::user();

        if($user) {
            # Approach 1)
            //$books = Book::where('user_id', '=', $user->id)->orderBy('id','DESC')->get();

            # Approach 2) Take advantage of Model relationships
            $books = $user->books()->get();
        }
        else {
            $books = [];
        }

        return view('book.index')->with([
            'books' => $books
        ]);
    }
    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        return view('book.create');
    }
    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        # Validate
        $this->validate($request, [
            'title' => 'required|min:3',
            'published' => 'required|min:4|numeric',
            'cover' => 'required|url',
            'purchase_link' => 'required|url',
        ]);

        # If there were errors, Laravel will redirect the
        # user back to the page that submitted this request
        # If there were NO errors, the script will continue...
        # Get the data from the form
        #$title = $_POST['title']; # Option 1) Old way, don't do this.
        $title = $request->input('title'); # Option 2) USE THIS ONE! :)
        # Here's where your code for what happens next should go.
        # Examples:
        # Save book in the database

        $book = new Book();
        $book->title = $request->input('title');
        $book->published = $request->input('published');
        $book->cover = $request->input('cover');
        $book->purchase_link = $request->input('purchase_link');
        $book->user_id = $request->user()->id;
        $book->save();

        Session::flash('flash_message', 'Your book '.$book->title.' was added.');

        # When done - what should happen?
        # You can return a String (not ideal), or a View, or Redirect to some other page:
        return redirect('/books');
        # FYI: There's also a Laravel helper that could shorten the above line to this:
        // return view('book.store')->with('title',$title);
    }
    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        $book = Book::find($id);

        return view('book.show')->with('book',$book);
    }

    /**
    * GET
    */
    public function edit($id)
    {
        $book = Book::find($id);
        # Possible authors
        $authors_for_dropdown = Author::getForDropdown();
        # Possible tags
        $tags_for_checkboxes = Tag::getForCheckboxes();
        # Just the tags for this book
        $tags_for_this_book = [];
        foreach($book->tags as $tag) {
            $tags_for_this_book[] = $tag->id;
        }
        return view('book.edit')->with(
            [
                'book' => $book,
                'authors_for_dropdown' => $authors_for_dropdown,
                'tags_for_checkboxes' => $tags_for_checkboxes,
                'tags_for_this_book' => $tags_for_this_book,
            ]
        );
    }
    /**
    * POST
    */
    public function update(Request $request, $id)
    {
        // dump($request->all());
        $this->validate($request, [
            'title' => 'required|min:3',
            'published' => 'required|min:4|numeric',
            'cover' => 'required|url',
            'purchase_link' => 'required|url',
        ]);

        $book = Book::find($request->id);
        $book->title = $request->title;
        $book->cover = $request->cover;
        $book->published = $request->published;
        $book->author_id = $request->author_id;
        $book->purchase_link = $request->purchase_link;

        $book->save();

        # If there were tags selected...
        if($request->tags) {
            $tags = $request->tags;
        }
        # If there were no tags selected (i.e. no tags in the request)
        # default to an empty array of tags
        else {
            $tags = [];
        }
        # Above if/else could be condensed down to this: $tags = ($request->tags) ?: [];
        # Sync tags
        $book->tags()->sync($tags);
        $book->save();

        Session::flash('flash_message', 'Your changes to '.$book->title.' were saved.');

        return redirect('/books');
    }
    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        $book = Book::find($id);
        $book->delete();
        Session::flash('flash_message', $book->title.' was deleted.');

        return redirect('/books');
    }
    /**
    * This was example code I wrote in Lecture 7
    * It shows, roughly, what a controller action for your P3 might look like
    * It is not at all related to the Book resource.
    */
    public function getLoremIpsumText(Request $request)
    {
        # Validate the request....
        # Generate the lorem ipsum text
        $howManyParagraphs = $request->input('howManyParagraphs');
        # Logic...
        $loremenator = \SBuck\Loremenator();
        $text = $loremenator->getParagraphs($howManyParagraphs);
        # Display the results...
        return view('lorem')->with('text', $text);
    }
}
