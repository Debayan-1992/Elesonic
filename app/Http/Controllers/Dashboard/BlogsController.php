<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Blog;
use Carbon\Carbon;

class BlogsController extends Controller
{
    public function index(){
        $data['activemenu']['main'] = 'blogs';
        $data['activemenu']['sub'] = 'index';

        if(!\Myhelper::can('view_blogs')){
            abort(401);
        }

        return view('dashboard.blogs.index', $data);
    }

    public function add(){
        $data['activemenu']['main'] = 'blogs';
        $data['activemenu']['sub'] = 'add';

        if(!\Myhelper::can('add_blog')){
            abort(401);
        }

        return view('dashboard.blogs.submit', $data);
    }

    public function edit($id='none'){
        $data['activemenu']['main'] = 'blogs';
        $data['activemenu']['sub'] = 'index';

        if(!\Myhelper::can('edit_blog')){
            abort(401);
        }

        $data['blog'] = Blog::findorfail($id);

        return view('dashboard.blogs.submit', $data);
    }

    public function submit(Request $post){
        switch ($post->operation) {
            case 'new':
                $rules = array(
                    'title' => 'required',
                    'content' => 'required',
                    'blog_image' => 'required|image',
                );

                $permission = 'add_blog';
            break;

            case 'edit':
                $rules = array(
                    'title' => 'required',
                    'content' => 'required',
                    'id' => 'required',
                );

                $permission = 'edit_blog';
            break;

            case 'changeaction':
                $rules = array(
                    'id' => 'required',
                );

                $permission = 'edit_blog';
            break;

            case 'delete':
                $rules = array(
                    'id' => 'required',
                );

                $permission = 'delete_blog';
            break;

            default:
                return response()->json(['status' => 'Invalid Request'], 400);
            break;
        }

        if(isset($rules)){
            $validator = \Validator::make($post->all(), $rules);
            if($validator->fails()){
                foreach($validator->errors()->messages() as $key => $value){
                    return response()->json(['status' => $value[0]], 400);
                }
            }
        }

        if(isset($permission) && !\Myhelper::can($permission)){
            return response()->json(['status' => 'Permission not Allowed'], 401);
        }

        switch ($post->operation) {
            case 'changeaction':
                $blog = Blog::findorfail($post->id);
                if($blog->status){
                    $post['status'] = '0';
                } else{
                    $post['status'] = '1';
                }
            case 'edit':
                $blog = Blog::findorfail($post->id);
                if($blog->image != NULL){
                    $deletefile = 'uploads/blogs/'.$blog->image;
                }
            case 'new':
                $post['created_by'] = \Auth::id();

                if($post->file('blog_image')){
                    $file = $post->file('blog_image');
                    $filename = Carbon::now()->timestamp.'_'.$file->getClientOriginalName();

                    if(\Image::make($file->getRealPath())->save('uploads/blogs/'.$filename)){
                        $post['image'] = $filename;

                        if(isset($deletefile)){
                            \File::delete($deletefile);
                        }
                    } else{
                        return response()->json(['status' => 'File cannot be saved to server.'], 400);
                    }
                }

                $action = Blog::updateorcreate(['id' => $post->id], $post->all());
            break;

            case 'delete':
                $blog = Blog::findorfail($post->id);
                $action = $blog->delete();
            break;
        }

        if($action){
            return response()->json(['status' => 'Task completed successfully'], 200);
        } else{
            return response()->json(['status' => 'Task failed. Please try again'], 400);
        }
    }
}
