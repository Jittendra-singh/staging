<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\GlobalType;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\GlobalTypeRequest;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Tag;
use DB;
use Illuminate\Support\Facades\Gate;
class GlobalTypeController extends Controller
{
    private $gateHandler = 'global-types/';
    private $viewDirectory = 'pages.global_type.';
    private $modelClass;
    public function __construct()
    {
        $this->modelClass = GlobalType::class;
    }
    public function index($slug)
    {
        $global = $this->modelClass::where("slug" , $slug)->first();
        Gate::authorize('view', $global->slug);
        $data['title'] = $global->name;
        $model = new $global->class_name();
        //$data['listingData'] = $model->orderBy('name')->get();
        $data['listingData'] = $model->orderBy('name')->paginate(50)->withQueryString();
        $data['global'] = $global;
        $data['module'] = $global->slug;
        return view($this->viewDirectory.'index', $data);
    }
    public function showForm($slug, $placeholder = null)
    {
        $global = $this->modelClass::where("slug" , $slug)->first();
        $model = new $global->class_name();
        if($placeholder){
            $data       =  $model::findOrFail(decrypt($placeholder));
            Gate::authorize('view' , $global->slug);
        }else{
            Gate::authorize('create' ,  $global->slug);
        }
        return view('pages.global_type.manage', ['formData' => $data ?? null , 'module' => $global->slug , 'global' => $global]);
    }
    public function toggleStatus($slug , $placeholder){
        $global = $this->modelClass::where("slug" , $slug)->first();
        $model = new $global->class_name();
        Gate::authorize('update' , $global->slug);
        $obj          =  $model::findOrFail(decrypt($placeholder));
        $obj->status  = !$obj->status;
        $res = $obj->save();
        $this->setFlashSession($res);
        return redirect()->route('index-global-type', ['slug' => $global->slug]);
    }
    public function status_update($list_id, Request $request)
    {
        try {
            $input = $request->all();
            $global = GlobalType::find($list_id);
            $model = $global->class_name::find($input['id']);
            if ($model == null) {
                throw new \Exception($global->name . ' Not Found');
            }
            $model->status = $input['status'];
            $model->save();
        } catch (\Exception $e) {
            // dd($e->getMessage());
        }
        // return redirect(route('admin.user'));
    }
    public function active($id)
    {
        try {
            $model = User::get_detail($id);
            if ($model == null) {
                throw new \Exception('User Not Found');
            }
            $model->status = 1;
            $model->save();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
        return redirect(route('admin.user'));
    }
    public function deactive($id)
    {
        try {
            $model = User::get_detail($id);
            if ($model == null) {
                throw new \Exception('User Not Found');
            }
            $model->status = 0;
            $model->save();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
        return redirect(route('admin.user'));
    }
    public function manage($slug, $placeholder = null)
    {
        request()->validate([
            'name' => 'required',
            'cropped_image' => 'nullable|string',
        ]);
        $global = $this->modelClass::where("slug" , $slug)->first();
        $model = new $global->class_name();
        if($placeholder){
            Gate::authorize('update' , $global->slug);
            $obj       =  $model::findOrFail(decrypt($placeholder));
        }else{
            Gate::authorize('create' ,  $global->slug);
            $obj      =  new $model();
        }
        $obj->name    = request()->name;
        $obj->slug    = str()->slug(request()->name , '-');
        if ($global->slug == 'categories' && request()->filled('cropped_image')) {
            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', request()->input('cropped_image'));
            $imageData = base64_decode($base64);

            $originalName = 'category_banner.jpg';
            $fileName = str_replace(' ', '', date('dmyhis') . $originalName);
            $destinationPath = 'event_categories/banners';
            $publicPath = public_path($destinationPath);

            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            file_put_contents($publicPath . '/' . $fileName, $imageData);

            $obj->default_banner_img = json_encode([
                'path'          => "$destinationPath/" . $fileName,
                'original_name' => $originalName,
                'extension'     => 'jpg',
            ]);
        }
        // $obj->status  = request()->status ? 1 : 0;
        if($global->slug == 'tags'){
            //$obj->type  = request()->type ?? 'event';
        }
        $res = $obj->save();
        $this->setFlashSession($res);
        return redirect()->route('index-global-type', ['slug' => $global->slug]);
    }
    function delete($slug, $placeholder)
    {
        $global = $this->modelClass::where("slug" , $slug)->first();
        $model = new $global->class_name();
        Gate::authorize('delete' , $global->slug);
        $obj       =  $model::findOrFail(decrypt($placeholder));
        if($slug == 'tags' || $slug == 'tools' || $slug == 'categories'){
            if($obj->events()->count() > 0){
                return $this->generateJsonResponse(false, 'This row is associated with some events. Please remove it from the events first.');
            }
            if($slug == 'tags' && $obj->events2()->count() > 0){
                return $this->generateJsonResponse(false, 'This row is associated with some events. Please remove it from the events first.');
            }
        }
        $res = $obj->delete();
        return $this->generateJsonResponse($res);
    }
}
