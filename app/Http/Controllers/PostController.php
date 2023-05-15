<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;



class PostController extends Controller
{
    const LOCAL_STORAGE_FOLDER = 'public/images/';

    private $post; // object of post model
    private $category; // object of category model

    public function __construct(Post $post, Category $category){
        $this->post = $post;
        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $all_categories = $this->category->all();
        return view('users.posts.create')->with('all_categories', $all_categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        #validate the data
        $request->validate([
            'category'    => 'required|array|between:1,3',
            'description' => 'required|min:1|max:1000', //1000 characters
            'image'       => 'required|mimes:jpeg,jpg,png,gif|max:1048'
        ]);

        # save the post
        $this->post->user_id    = Auth::user()->id;
        $this->post->image      = $this->saveImage($request);
        $this->post->description = $request->description;
        $this->post->save();

        # Save the categories to the category_post pivot table
        # create() -- createMany() -- it accepts 2d array as an input 
        foreach ($request->category as $category_id) { //3
            $category_post[] = ['category_id' => $category_id];
        }
        
        $this->post->categoryPost()->createMany($category_post);
        return redirect()->route('index');
    }

    private function saveImage($request){
        # Change the filename into local time, to avoid overwriting
        $image_name = time() . "." . $request->image->extension();
        // 1724587777.jpeg

        # save the image into the storage/public/images
        $request->image->storeAs(self::LOCAL_STORAGE_FOLDER, $image_name);

        return $image_name; //1683201660.jpeg
    }   

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $post = $this->post->findOrFail($id);
        return view('users.posts.show')->with('post', $post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        # step 1: retrieve the record of the specific post
        $post = $this->post->findOrFail($id); // SELECT * FROM posts WHERE id = '$id';

        # step 2: check if the user trying to edit is the owner of the post, if not redirect to the homepage
        if(Auth::user()->id != $post->user->id){
            return redirect()->route('index'); //homepage
        }

        # step 3: retrieve the selected categories of that specific post, save it in an array
        $all_categories = $this->category->all(); // SELECT * FROM category;

        $selected_categories = []; // this will hold the id of the selected categories
        foreach ($post->categoryPost as $category_post) {
            $selected_categories[] = $category_post->category_id; // coming the category_post table
        }

        # step 4: return to the edit page, along with all the data we have
        return view('users.posts.edit')
            ->with('post', $post)
            ->with('all_categories', $all_categories)
            ->with('selected_categories', $selected_categories);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        # 1. validate the data first
        $request->validate([
            'category'      => 'required|array|between:1,3',
            'description'   => 'required|min:1|max:1000', //text
            'image'         => 'mimes:jpeg,jpg,png,gif|max:1048'
        ]);

        #2. Update the post
        $post = $this->post->findOrFail($id); //find($id)
        $post->description = $request->description;

        #3. Check if there is new image
        if ($request->image) {
            # Delete or removed old image in the storage
            $this->deleteImage($post->image);

            # Move the new image to the local storage
            $post->image = $this->saveImage($request);
        }

        $post->save();
        
        # Update the categories-- deleting all categories already in the db
        # to this specific post only
        $post->categoryPost()->delete(); // DELETE * FROM category_post WHERE id = '$id'

        # save the new categories
        foreach($request->category as $category_id){
            $category_post[] = [
                'category_id' => $category_id
            ];
        }

        $post->categoryPost()->createMany($category_post);

        return redirect()->route('post.show', $id);

    }

    private function deleteImage($image_name){
        $image_path = self::LOCAL_STORAGE_FOLDER . $image_name;
        //$image_path = '/public/images/1683201660.jpg'

        //if image is existing
        if (Storage::disk('local')->exists($image_path)) {
            Storage::disk('local')->delete($image_path);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $post = $this->post->findOrFail($id);
        $this->deleteImage($post->image);
        $post->delete();
        return redirect()->route('index');
    }
}
