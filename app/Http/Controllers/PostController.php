<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Auth;
use Validator;
use Illuminate\Support\Facades\File;


class PostController extends Controller
{
    public function index() {
        $user_id = Auth::user()->id;
        $data= Post::where('user_id', $user_id)->get();  
        $tagsArray = [];

         foreach ($data as $post) {
             $tags = $post->tags;
             foreach ($tags as $tag) {
                 $tagsArray[] = $tag;
             }
         }
        return view('post', ['tags'=> $tagsArray]);
    }
    public function create() {
        $user_id=Auth::user()->id;
        $user = User::where('id', $user_id)->first();  
        
        return view('newPost', ['user' => $user]);
    }
    public function edit(Request $request, $id) {
        $user_id=Auth::user()->id;
        $user = User::where('id', $user_id)->first(); 
        $post = Post::where('id', $id)->first(); 
        $tags = $post->tags;
        return view('editPost', ['user' => $user, 'post'=>$post, 'tags' => $tags]);
    }
    public function store(Request $request) {
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tags' => 'required'
        ];

        $validator = Validator::make($request->all('title', 'description', 'image'), $rules);
        if ($validator->fails()) {
            return response()->json(['code'=>'401', 'msg'=> $validator->errors()->toArray()]);
        }

        $image = $request->file('image');
        $fileName = time() . '.' . $request->image->extension();
        $image->move(public_path('img'), $fileName);

        $imagePath = public_path('img/' . $fileName);
        $croppedImagePath = public_path('img/cropped_' . $fileName);
        $cropWidth = 150;
        $cropHeight = 150;
        $image2 = Image::make($imagePath);
        $croppedImage = $image2->fit($cropWidth, $cropHeight);
        $croppedImage->save($croppedImagePath);
        
        $post = new Post;
        $post->title = $request->input('title');
        $post->description = $request->input('description');
        $post->user_id = $request->input('user_id');
        $post->image = $fileName;
        $post->save();

        $tags = explode(',', $request->input('tags'));
        $tags = array_map('trim', $tags);
        $tags = array_filter($tags);

        $tagsTotal = Tag::get();
        

        foreach ($tags as $tag) {
            $found = false;
        
            foreach ($tagsTotal as $tagItem) {
                if (strcasecmp($tagItem->name, $tag) === 0) {
                    $post->tags()->attach($tagItem->id);
                    $found = true;
                    break;
                }
            }
        
            if (!$found) {
                $newTag = new Tag;
                $newTag->name = $tag;
                $newTag->save();
                $post->tags()->attach($newTag->id);
            }
        }

        return response()->json(['code' => 200, 'msg' => 'Спасибо. Ваша заявка принята в обработку.']);
    }
    public function update(Request $request, $id) {
        Post::where('id',$id)->update([
            'title' => $request->title, 
            'description' => $request->description,
        ]);
        $post = Post::findOrFail($id);

        $image = $request->file('image');
        $fileName = time() . '.' . $request->image->extension();
        $image->move(public_path('img'), $fileName);

        $imagePath = public_path('img/' . $fileName);
        $croppedImagePath = public_path('img/cropped_' . $fileName);
        $cropWidth = 150;
        $cropHeight = 150;
        $image2 = Image::make($imagePath);
        $croppedImage = $image2->fit($cropWidth, $cropHeight);
        $croppedImage->save($croppedImagePath);

        $post->image = $fileName;
        $post->save();

        $inputTags = array_map('trim', explode(',', $request->input('tags')));
        $tagsPost = $post->tags;
        $tagsTotal = Tag::get();
    
        $existingTags = [];
        $newTags = [];
    
        foreach ($inputTags as $inputTag) {
            $tagExists = false;
    
            foreach ($tagsPost as $tagPost) {
                if (strcasecmp($tagPost->name, $inputTag) === 0) {
                    $existingTags[] = $tagPost->id;
                    $tagExists = true;
                    break;
                }
            }
    
            if (!$tagExists) {
                foreach ($tagsTotal as $tagTotal) {
                    if (strcasecmp($tagTotal->name, $inputTag) === 0) {
                        $existingTags[] = $tagTotal->id;
                        $tagExists = true;
                        break;
                    }
                }
    
                if (!$tagExists) {
                    $newTag = new Tag;
                    $newTag->name = $inputTag;
                    $newTag->save();
                    $newTags[] = $newTag->id;
                }
            }
        }
    
        // Detach tags that are no longer present
        foreach ($tagsPost as $tagPost) {
            if (!in_array($tagPost->id, $existingTags)) {
                $post->tags()->detach($tagPost->id);
            }
        }
    
        // Sync existing and new tags
        $post->tags()->sync(array_merge($existingTags, $newTags));

        return response()->json(['code' => 200, 'msg' => 'Спасибо. Ваша заявка принята в обработку.']);
    }
    public function showImage(Request $request, $id) {
        $post = Post::where('id', $id)->first();
        return view('imageById', ['post' => $post]);
    }
    public function getPosts(Request $request, $user_id)
    {

        if($request->ajax())
        {         
         $output = '';  
         // Paginate the results and return as JSON
         $perPage = 10; // Set the number of items per page
         $page = $request->input('page', 1); 
         $data= Post::where('user_id', $user_id)->paginate($perPage, ['*'], 'page', $page);  
         $tagsArray = [];

         foreach ($data as $post) {
             $tags = $post->tags;
             foreach ($tags as $tag) {
                 $tagsArray[] = $tag;
             }
         }

         $total_row=$data->count();
         if($total_row > 0){
            foreach($data as $row) {
                $output .= '
                <tr>
                 <td class="row_id">'.$row->id.'</td>
                 <td class="row_title">'.$row->title.'</td>
                 <td class="row_description">'.$row->description.'</td>   
                 <td class="row_description">
                    <div class="container_image">
                        <a href="/image/'.$row->id.'" target="_blank">
                            <img id="preview" class="preview" src="' . asset('img/cropped_' . ($row->image ? $row->image : 'no-pictures.png')) . '" alt="your image" class="mt-3"/>
                            <div class="middle">
                                <div class="preview_text">Просмотр</div>
                            </div>
                        </a>
                    </div>
                </td>   
                 <td>
                 <div class="action_icons">  
                <a href="/editpost/'.$row->id.'" class="draw-icon" data-sid='.$row->id.'>'.'</a>              
                <button class="delete-icon" data-sid='.$row->id.'>'.'</button>
                </div>
                </td>
                </tr>
                ';
            }
        } else {
            $output = '
       <tr>
        <td align="center" colspan="8">No Data Found</td>
       </tr>
       ';
        }

        $data = array(
            'table_data'  => $output,
            'total_data'  => $total_row,
            'tags' => $tagsArray,
            'currentPage' => $data->currentPage(),
            'lastPage' => $data->lastPage(),
           );
        echo json_encode($data);
        }
    }
    function filterPosts(Request $request, $id)  {

        $user_id = Auth::user()->id;   
        $posts= Post::where('user_id', $user_id)->get(); 

        $perPage = 10;
        $page = $request->input('page', 1); 

        if($request->ajax())
        {
        $output = '';
        $query = $request->get('id');
        if($query == 'all') {
            $data = Post::where('user_id', $user_id)->paginate($perPage, ['*'], 'page', $page);
        } elseif($query == $id) {
            $tag = Tag::with('posts')->where('id', $id)->first();
            $tags = $tag->posts;
            $data = $tag->posts;           
        }
        $total_row = $data->count();
        if($total_row > 0)
        {
        foreach($data as $row)
        {
            $output .= '
            <tr>
            <td class="row_id">'.$row->id.'</td>
            <td class="row_title">'.$row->title.'</td>
            <td class="row_description">'.$row->description.'</td>   
            <td class="row_description">
            <div class="container_image">
                <a href="/image/'.$row->id.'" target="_blank">
                    <img id="preview" class="preview" src="' . asset('img/cropped_' . $row->image) . '" alt="your image" class="mt-3"/>
                    <div class="middle">
                        <div class="preview_text">Просмотр</div>
                    </div>
                </a>
            </div>
            </td>   
            <td>
            <div class="action_icons">  
            <a href="/editpost/'.$row->id.'" class="draw-icon" data-sid='.$row->id.'>'.'</a>               
            <button class="delete-icon" data-sid='.$row->id.'>'.'</button>
            </div>
            </td>
            </tr>
            ';
        }
        }
        else
        {
        $output = '
        <tr>
            <td align="center" colspan="8">No Data Found</td>
        </tr>
        ';
        }
        $data = array(
        'table_data'  => $output,
        'total_data'  => $total_row
        );

        echo json_encode($data);
        }
    }   
    public function search(Request $request) { 

        if($request->ajax())
        {
        $perPage = 10; // Set the number of items per page
        $page = $request->input('page', 1); 

        $user_id = Auth::user()->id;
        $query = $request->input('query');        
        $data = Post::where('user_id', $user_id)
                        ->where('title', 'like', '%'.$query.'%')
                        ->paginate($perPage, ['*'], 'page', $page);

        $output = '';
        $total_row = $data->count();
        if($total_row > 0)
        {
        foreach($data as $row)
        {
            $output .= '
            <tr>
            <td class="row_id">'.$row->id.'</td>
            <td class="row_title">'.$row->title.'</td>
            <td class="row_description">'.$row->description.'</td>   
            <td class="row_description">
            <div class="container_image">
                <a href="/image/'.$row->id.'" target="_blank">
                    <img id="preview" class="preview" src="' . asset('img/cropped_' . $row->image) . '" alt="your image" class="mt-3"/>
                    <div class="middle">
                        <div class="preview_text">Просмотр</div>
                    </div>
                </a>
            </div>
            </td>   
            <td>
            <div class="action_icons">  
            <a href="/editpost/'.$row->id.'" class="draw-icon" data-sid='.$row->id.'>'.'</a>                
            <button class="delete-icon" data-sid='.$row->id.'>'.'</button>
            </div>
            </td>
            </tr>
            ';
        }
        }
        else
        {
        $output = '
        <tr>
            <td align="center" colspan="8">No Data Found</td>
        </tr>
        ';
        }
        $data = array(
        'table_data'  => $output,
        'total_data'  => $total_row,        
        'currentPage' => $data->currentPage(),
        'lastPage' => $data->lastPage(),

        );

        echo json_encode($data);
        }
    }
    public function delete($post_id)
    {
        $posts =Post::find($post_id); 
        $posts->delete();

        return response()->json(['msg' => 'Удалено.']);
    }
    public function deleteImage($id)
    {
        // Get the post or image record from the database based on the ID
        $post = Post::find($id);

        if ($post) {
            // Delete the image file from the public folder
            $imagePath = public_path('img/' . $post->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }

            // Delete the image name from the column in the table
            $post->image = null;
            $post->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
