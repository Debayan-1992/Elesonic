<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\model\Attribute;


class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sort_search = null;
        $data['activemenu']['sub'] = 'index';
        $data['activemenu']['main'] = 'products';
        return view('dashboard.attribute.index', $data);
    }

      /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attribute = new Attribute;
        $attribute->name = $request->name;
        $attribute->save();
        return response()->json(['status' => 'attribute added'], 200);
    }



    public function statusChange(Request $request){
        switch($request->type){
            case 'statusChange':
                $status = Attribute::findorfail($request->id);
                if($status->status == 'A' ){
                    $request['status'] = 'I';
                } else{
                    $request['status'] = 'A';
                }
                Attribute::where('id', $request->id)->update($request->except(['_token','type']));
            break;
            case 'delete':
                $request['status'] = 'D';
                Attribute::where('id', $request->id)->update($request->except(['_token','type']));
            break;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $data['activemenu']['main'] = 'products';
        $data['activemenu']['sub'] = 'edit';
        $id =  request()->segment(4);
        $attribute = Attribute::findOrFail($id);
        $data['attribute'] = $attribute;
      
        return view('dashboard.attribute.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request->id;
        $attribute = Attribute::findOrFail($id);
        $attribute->name = $request->name;
        $attribute->save();
        return response()->json(['status' => 'attribute updated'], 200);
    }
}
