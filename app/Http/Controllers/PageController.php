<?php
namespace Foobooks\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
class PageController extends Controller
{
    /**
	*
	*/
    public function help() {
        return 'This page should show help information';
    }
    /**
	*
	*/
    public function faq() {
        return 'This page should show a list of frequently asked questions';
    }
}
