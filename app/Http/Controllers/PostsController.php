<?php

namespace App\Http\Controllers;

use Gate;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\AccessPost;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\PostRequest;
use App\Posts;
use Auth;
class PostsController extends Controller
{

	public function __construct()
	{  
        //
	}

    public function index()
    {
        $post = Posts::status('1')->orderBy('created_at')->get();
        return view('home', compact('post'));
    }

    //Show the Post
    public function show($id)
    {
    	$post = Posts::findOrFail($id);
    	return view('posts.show', compact('post'));
    }

    //Show Upload Post Form
    /*public function create()
    {
        $post = new Posts;
        return view('posts.create', compact('post'));
    }*/

    /*public function store(PostRequest $request)
    {
        $post = new Posts($request->all());
        $post['approval'] = 2;
        $post['residence_id'] = 1;
        $posts = Auth::user()->posts()->save($post);
        return redirect()->action('PostsController@show', [$posts]);
        flash()->success('Your Post has been Submitted.');
    }*/

    //Show Edit Post Form
    public function edit($id)
    {
        $post = Posts::find($id);
        if (Gate::allows('owns_post', $post)) {
            return view('posts.edit', compact('post'));
        }
        else{
            flash()->error('Sorry, you do not seem to have access to this post');
            return redirect()->action('PostsController@show', [$post]);
        }
    }

    //Function that handles the update post data
    public function update(UpdatePostRequest $request, $id)
    {
        $post = Posts::findOrFail($id);
        $user = Auth::user()->name;
        if (Gate::allows('owns_post', $post)) {
            //tells the query to add the user user's name ot the table
            $post['edited_by'] = $user;
            $post->update($request->all());
            flash()->success('Your Post has been Updated!');
            return redirect()->action('PostsController@show', [$post]);
        }
        else{
            flash()->warning('You do not have access to editing that resource');
            return redirect()->action('PostsController@show', [$posts]);      
        }
        
    }

    public function myPosts()
    {
        //get all the current user's posts
        $posts = Auth::user()->posts()->get();
        return view('posts.user.myPosts', compact('posts'));
    }
    
    public function approvedPosts()
    {
        //get all the current user's approved posts
        $posts = Auth::user()->posts()->status('1')->orderBy('created_at')->get();
        return view('posts.user.myApprovedPosts', compact('posts'));
    }
}
