<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\FaqContent;
use App\Model\CmsContent;
use App\Model\TestimonialContent;
use Carbon\Carbon;

class CmsController extends Controller
{
    public function index($type){
        $data['activemenu']['main'] = 'cms';

        switch ($type) {
            case 'faqs':
                $view = 'faqs';
                $data['activemenu']['sub'] = 'faqs';
                $permission = 'view_faqs';
            break;

            case 'contents':
                $view = 'contents';
                $data['activemenu']['sub'] = 'contents';
                $permission = 'view_contents';
            break;

            case 'testimonials':
                $view = 'testimonials';
                $data['activemenu']['sub'] = 'testimonials';
                $permission = 'view_testimonials';
            break;

            default:
                abort(404);
            break;
        }

        if(isset($permission) && !\Myhelper::can($permission)){
            abort(401);
        }

        return view('dashboard.cms.'.$view, $data);
    }

    public function edit($type, $id){
        $data['activemenu']['main'] = 'cms';

        switch ($type) {
            case 'content':
                $view = 'contentedit';
                $data['activemenu']['sub'] = 'contents';
                $permission = 'edit_content';

                $data['content'] = CmsContent::findorfail($id);
            break;

            default:
                abort(404);
            break;
        }

        if(isset($permission) && !\Myhelper::can($permission)){
            abort(401);
        }

        return view('dashboard.cms.'.$view, $data);
    }

    public function submitcms(Request $post){
        switch ($post->operation) {
            case 'faqnew':
                $rules = array(
                    'question' => 'required',
                    'answer' => 'required',
                );

                $permission = 'add_faq';
            break;

            case 'faqedit':
                $rules = array(
                    'question' => 'required',
                    'answer' => 'required',
                    'id' => 'required',
                );

                $permission = 'edit_faq';
            break;

            case 'faqchangeaction':
                $rules = array(
                    'id' => 'required',
                );

                $permission = 'edit_faq';
            break;

            case 'faqdelete':
                $rules = array(
                    'id' => 'required',
                );

                $permission = 'delete_faq';
            break;

            case 'contentedit':
                $rules = array(
                    'id' => 'required',
                    'page_title' => 'required',
                );

                $permission = 'edit_content';
            break;

            case 'testimonialnew':
                $rules = array(
                    'name' => 'required',
                    'designation' => 'required',
                    'avatar_image' => 'required|image',
                );

                $permission = 'add_testimonial';
            break;

            case 'testimonialedit':
                $rules = array(
                    'name' => 'required',
                    'designation' => 'required',
                    'id' => 'required',
                );

                $permission = 'edit_testimonial';
            break;

            case 'testimonialchangeaction':
                $rules = array(
                    'id' => 'required',
                );

                $permission = 'edit_testimonial';
            break;

            case 'testimonialdelete':
                $rules = array(
                    'id' => 'required',
                );

                $permission = 'delete_testimonial';
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
            case 'faqchangeaction':
                $content = FaqContent::findorfail($post->id);
                if($content->status){
                    $post['status'] = '0';
                } else{
                    $post['status'] = '1';
                }
            case 'faqnew':
            case 'faqedit':
                $action = FaqContent::updateorcreate(['id' => $post->id], $post->all());
            break;

            case 'faqdelete':
                $content = FaqContent::findorfail($post->id);
                $action = $content->delete();
            break;

            case 'contentedit':
                $action = CmsContent::updateorcreate(['id' => $post->id], $post->all());
            break;

            case 'testimonialchangeaction':
                $content = TestimonialContent::findorfail($post->id);
                if($content->status){
                    $post['status'] = '0';
                } else{
                    $post['status'] = '1';
                }
            case 'testimonialedit':
                $content = TestimonialContent::findorfail($post->id);
                if($content->image != NULL){
                    $deletefile = 'uploads/testimonials/'.$content->image;
                }
            case 'testimonialnew':
                if($post->file('avatar_image')){
                    $file = $post->file('avatar_image');
                    $filename = Carbon::now()->timestamp.'_'.$file->getClientOriginalName();

                    //Resizing and compressing the image
                    if(\Image::make($file->getRealPath())->resize(100, 100)->save('uploads/testimonials/'.$filename, 60)){
                        $post['image'] = $filename;

                        if(isset($deletefile)){
                            \File::delete($deletefile);
                        }
                    } else{
                        return response()->json(['status' => 'File cannot be saved to server.'], 400);
                    }
                }

                $action = TestimonialContent::updateorcreate(['id' => $post->id], $post->all());
            break;

            case 'testimonialdelete':
                $content = TestimonialContent::findorfail($post->id);
                $action = $content->delete();
            break;
        }

        if($action){
            return response()->json(['status' => 'Task completed successfully'], 200);
        } else{
            return response()->json(['status' => 'Task failed. Please try again'], 400);
        }
    }
}
