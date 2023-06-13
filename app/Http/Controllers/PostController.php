<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index() {
        return view('post');
    }
    public function store(Request $request) {
        $post_data = $request->all();

        Post::create($post_data);

        return response()->json(['code' => 200, 'msg' => 'Спасибо. Ваша заявка принята в обработку.']);
    }
    public function getPosts(Request $request)
    {
        if($request->ajax())
        {         
         $output = '';  
         $data= Post::get();       

         $total_row=$data->count();
         if($total_row > 0){
            foreach($data as $row) {
                $output .= '
                <tr>
                 <td>'.$row->id.'</td>
                 <td>'.$row->title.'</td>
                 <td>'.$row->description.'</td>   
                 <td>
                 <div class="action_icons">  
                <button class="draw-icon" data-sid='.$row->id.'>'.'</button>              
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
            'total_data'  => $total_row
           );
        echo json_encode($data);
        }
    }
}
