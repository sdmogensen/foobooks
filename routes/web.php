<?php
/**
* Book resource
*/
# Index page to show all the books
Route::get('/books', 'BookController@index')->name('books.index')->middleware('auth');
# Show a form to create a new book
Route::get('/books/create', 'BookController@create')->name('books.create')->middleware('auth');
# Process the form to create a new book
Route::post('/books', 'BookController@store')->name('books.store');
# Show an individual book
Route::get('/books/{id}', 'BookController@show')->name('books.show');
# Show form to edit a book
Route::get('/books/{id}/edit', 'BookController@edit')->name('books.edit');
# Process form to edit a book
Route::put('/books/{id}', 'BookController@update')->name('books.update');
# Delete a book
Route::delete('/books/{id}', 'BookController@destroy')->name('books.destroy');
# The above routes *could* all be replaced with this one line:
# Route::resource('books', 'BookController');
/**
* Misc Pages
* A way to display simple, static pages that don't really need their own controller
*/
Route::get('/help', 'PageController@help')->name('page.help');
Route::get('/faq', 'PageController@faq')->name('page.faq');
/**
* Contact page
* Single action controller that contains a __invoke method, so no action is specified
* This page could also be taken care of via the PageController, it's up to you.
*/
Route::get('/contact', 'ContactController')->name('contact');
/**
* A quick and dirty way to set up a whole bunch of practice routes
* that I'll use in lecture.
*/
Route::get('/example', 'PracticeController@index')->name('example.index');
for($i = 0; $i < 100; $i++) {
    Route::get('/example/'.$i, 'PracticeController@example'.$i)->name('example.example'.$i);
}
/**
* Development related
*/
# Make it so the logs can only be seen locally
if(App::environment() == 'local') {
    Route::get('/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
}
/**
* Main homepage
*/
// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/debug', function() {

    echo '<pre>';

    echo '<h1>Environment</h1>';
    echo App::environment().'</h1>';

    echo '<h1>Debugging?</h1>';
    if(config('app.debug')) echo "Yes"; else echo "No";

    echo '<h1>Database Config</h1>';
    /*
    The following line will output your MySQL credentials.
    Uncomment it only if you're having a hard time connecting to the database and you
    need to confirm your credentials.
    When you're done debugging, comment it back out so you don't accidentally leave it
    running on your live server, making your credentials public.
    */
    //print_r(config('database.connections.mysql'));

    echo '<h1>Test Database Connection</h1>';
    try {
        $results = DB::select('SHOW DATABASES;');
        echo '<strong style="background-color:green; padding:5px;">Connection confirmed</strong>';
        echo "<br><br>Your Databases:<br><br>";
        print_r($results);
    }
    catch (Exception $e) {
        echo '<strong style="background-color:crimson; padding:5px;">Caught exception: ', $e->getMessage(), "</strong>\n";
    }

    echo '</pre>';

});

/**
* ref: https://github.com/susanBuck/dwa15-fall2016-notes/blob/master/03_Laravel/21_Schemas_and_Migrations.md#starting-overyour-first-migrations
*/
if(App::environment('local')) {
    Route::get('/drop', function() {
        DB::statement('DROP database foobooks');
        DB::statement('CREATE database foobooks');
        return 'Dropped foobooks; created foobooks.';
    });
};

Route::get('/show-login-status', function() {
    $user = Auth::user();
    if($user) {
        dump($user->toArray());
    }
    else {
        dump('You are not logged in');
    }
    return;
});

Route::get('/', 'PageController@welcome');

Auth::routes();

Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

// Route::get('/home', 'HomeController@index');
