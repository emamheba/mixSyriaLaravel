<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Blog\app\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::latest()->paginate(10);

        return view('backend.tags.index', compact('tags'));
    }


    public function create(Request $request)
    {
        return view('backend.tags.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tag = Tag::create([
            'name' => $request->name
        ]);

        return redirect()->route('tags.index')->with('success', 'Tag created successfully.');

    }


    public function edit(Request $request, Tag $tag)
    {
        return view('backend.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tag->update([
            'name' => $request->name,
        ]);

        return redirect()->route('tags.index')->with('success', 'Tag updated successfully.');
    }

    public function destroy($id){
        Tag::find($id)->delete();
        return redirect()->back()->with(FlashMsg::item_new('Tag Deleted Success'));
    }

    public function changeStatus($id){
        $tag = Tag::select('status')->where('id',$id)->first();
        if($tag->status==1){
            $status = 0;
        }else{
            $status = 1;
        }
        Tag::where('id',$id)->update(['status'=>$status]);
        return redirect()->back()->with(FlashMsg::item_new('Status Change Success'));
    }
}
