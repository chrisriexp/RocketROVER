<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\tasks;
use App\Models\User;
use App\Models\Notifications;
use App\Models\Notes;
use App\Models\Uploads;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class TasksController extends Controller
{   
    // Get Application ID Tasks/Errors
    public function get($app_id){
        $tasks = tasks::where('app_id', $app_id)->get();

        $response = [
            'success' => true,
            'errors' => $tasks
        ];

        return response()->json($response, 200);
    }

    // Read Application Notification
    public function read_notification($id){
        $notification = Notifications::find($id);
        $notification->read = true;
        $notification->save();

        return response()->json(['success'=> true], 200);
    }

    // Get Error Attachments
    public function uploads($app_id, $carrier){
        $uploads = Uploads::where('app_id', $app_id)->where('carrier', $carrier)->where('note', null)->get();

        return response()->json(['success'=>true, 'uploads'=>$uploads]);
    }

    // Get Note Attachments
    public function note_uploads($note_id){
        $uploads = Uploads::where('note', $note_id)->get();

        return response()->json(['success'=>true, 'uploads'=>$uploads]);
    }

    // Delete Attachment
    public function delete_upload($id){
        $upload = Uploads::find($id);

        Storage::delete('public/uploads/'.$upload->name);
        $upload->delete();

        return response()->json(['success'=>true, 'message'=>'Upload deleted sucessfully.'], 200);
    }

    // Save Note Attachment
    public function note_upload(Request $request){
        foreach ($request->uploads as $upload){
            $file_path = $upload->storeAs('uploads', $request->note."_".$upload->getClientOriginalName(), 'public');

            $fileUpload = new Uploads();
            $fileUpload->name = $request->note."_".$upload->getClientOriginalName();
            $fileUpload->path = '/storage/'.$file_path;
            $fileUpload->app_id = $request->app_id;
            $fileUpload->carrier = $request->carrier;
            $fileUpload->note = $request->note;
            $fileUpload->save();
        }

        return response()->json(['success'=>true, 'message'=>'File Uploads Successful.'], 200);
    }

    // Get Error Notes
    public function notes($app_id, $carrier){
        $notes = Notes::where('app_id', $app_id)->where('carrier', $carrier)->orderBy('created_at', 'desc')->get();
        
        return response()->json(['success'=> true, 'notes'=> $notes], 200);
    }

    // Add Note
    public function add_note(Request $request){
        $validator = Validator::make($request->all(), [
            'app_id'=> 'required',
            'carrier'=> 'required',
            'note'=> 'required'
        ]);

        $note = Notes::create($request->all());
        $note->save();

        return response()->json(['success'=> true], 200);
    }

    // Update Note
    public function update_note(Request $request, $id){
        $note = Notes::find($id);

        $note->fill($request->all());
        $note->save();

        return response()->json(['success'=> true], 200);
    }

    // Delete Note
    public function drop_note($id){
        $note = Notes::destroy($id);

        return response()->json(['success'=> true], 200);
    }

    // Log Test
    public function log_test(Request $request, $id){
        $task = tasks::find($id);
        $task->tests = $task->tests + 1;
        $task->save();
        $status = $request->passed == 'true' ? "PASSED" : "FAILED";

        $note = new Notes();
        $note->app_id = $task->app_id;
        $note->carrier = $task->carrier;
        $note->user = $request->user()->name;
        $note->note = "Error Test Log: Test ".$task->tests." was marked as ".$status." by ".$request->user()->name." on ".Carbon::now()->toDayDateTimeString()."\r\n\r\n"."Test Details: ".$request->details."\r\n\r\n".($request->has('uploads') ? "Please see the addtional reference material related to this error, count:".count($request->uploads) : "");
        $note->disabled = true;
        $note->save();

        if($request->has('uploads') && count($request->uploads) > 0){
            foreach($request->uploads as $upload){
                $file_path = $upload->storeAs('uploads', $upload->getClientOriginalName(), 'public');
        
                $fileUpload = new Uploads();
                $fileUpload->name = $upload->getClientOriginalName();
                $fileUpload->path = '/storage/'.$file_path;
                $fileUpload->app_id = $task->app_id;
                $fileUpload->carrier = $task->carrier;
                $fileUpload->note = $note->id;
                $fileUpload->save();
            }
        }
    }

    // Update Task Status
    public function status(Request $request, $id){
        $task = tasks::find($id);

        // Updating from Debugging Status and API Error
        if($task->status == 'debug'){
            $task->status = 'update';
            $task->cause = $request->cause;
            $task->updates = $request->updates;
            $task->save();

            $note = new Notes();
            $note->app_id = $task->app_id;
            $note->carrier = $task->carrier;
            $note->user = $request->user()->name;
            $note->note = "Status Update Log: This error's status was changed from Debugging to Implement Updates by ".$request->user()->name." on ".Carbon::now()->toDayDateTimeString()."\r\n\r\n"."Cause of the Error: ".$request->cause."\r\n\r\n"."Updates to be Implemented: ".$request->updates."\r\n\r\n".($request->has('uploads') ? "Please see the addtional reference material related to this error, count:".count($request->uploads) : "");
            $note->disabled = true;
            $note->save();

            if($request->has('uploads') && count($request->uploads) > 0){
                foreach($request->uploads as $upload){
                    $file_path = $upload->storeAs('uploads', $upload->getClientOriginalName(), 'public');
            
                    $fileUpload = new Uploads();
                    $fileUpload->name = $upload->getClientOriginalName();
                    $fileUpload->path = '/storage/'.$file_path;
                    $fileUpload->app_id = $task->app_id;
                    $fileUpload->carrier = $task->carrier;
                    $fileUpload->note = $note->id;
                    $fileUpload->save();
                }
            }
        }
        // Updating from Implementing Updates Status
        elseif($task->status == 'update'){
            $task->status = 'test';
            $task->save();

            $note = new Notes();
            $note->app_id = $task->app_id;
            $note->carrier = $task->carrier;
            $note->user = $request->user()->name;
            $note->note = "Status Update Log: This error's status was changed from Implement Updates to Testing by ".$request->user()->name." on ".Carbon::now()->toDayDateTimeString();
            $note->disabled = true;
            $note->save();
        }
        // Updating from Testing Status
        elseif($task->status == 'test'){
            $task->status = 'fixed';
            $task->fixed = true;
            $task->fixed_date = Carbon::now();
            $task->save();

            $note = new Notes();
            $note->app_id = $task->app_id;
            $note->carrier = $task->carrier;
            $note->user = $request->user()->name;
            $note->note = "Status Update Log: This error's status was changed from Testing to Fixed by ".$request->user()->name." on ".Carbon::now()->toDayDateTimeString();
            $note->disabled = true;
            $note->save();
        }
        // Updating from DigiPrompt to Fixed Status
        elseif($task->status == 'digiprompt'){
            $task->status = 'fixed';
            $task->fixed = true;
            $task->fixed_date = Carbon::now();
            $task->cause = $request->cause;
            $task->updates = $request->updates;
            $task->save();

            $note = new Notes();
            $note->app_id = $task->app_id;
            $note->carrier = $task->carrier;
            $note->user = $request->user()->name;
            $note->note = "Status Update Log: This error's status was changed from DigiPrompt Queue to Fixed by ".$request->user()->name." on ".Carbon::now()->toDayDateTimeString()."\r\n\r\n"."Cause of the Error: ".$request->cause."\r\n\r\n"."Updates Implemented: ".$request->updates."\r\n\r\n".($request->has('uploads') ? "Please see the addtional reference material related to this error, count:".count($request->uploads) : "");
            $note->disabled = true;
            $note->save();

            if($request->has('uploads') && count($request->uploads) > 0){
                foreach($request->uploads as $upload){
                    $file_path = $upload->storeAs('uploads', $upload->getClientOriginalName(), 'public');
            
                    $fileUpload = new Uploads();
                    $fileUpload->name = $upload->getClientOriginalName();
                    $fileUpload->path = '/storage/'.$file_path;
                    $fileUpload->app_id = $task->app_id;
                    $fileUpload->carrier = $task->carrier;
                    $fileUpload->note = $note->id;
                    $fileUpload->save();
                }
            }
        }
    }

    // Add Tasks/Errors for Application
    public function add(Request $request){
        $validator = Validator::make($request->all(), [
            'app_id'=> 'required',
            'errors'=> 'required'
        ]);

        if($validator->fails()){
            $response = [
                'success'=>false,
                'message'=>$validator->errors()
            ];

            return response()->json($response, 400);
        }

        $source = "";
        if(str_contains($request->app_id, "RFAA")){
            $source = "RA";
        } elseif(str_contains($request->app_id, "RMGA")){
            $source = "MGA";
        }

        $count = count($request->errors);

        $first_carrier = $request->errors[0]['carrier'];

        // Create Task for each Error
        foreach ($request->errors as $error){
            // Set App ID
            $error['app_id'] = $request->app_id;

            // Set Task Source based on Appliction ID
            $error['source'] = $source;

            // Set Task Product
            if($request->has('product')){
                $error['product'] = $request->product;

                if($request->product == "HOME"){
                    $error['carrier'] = $error['rater']."-".$error['carrier'];
                }
            }

            // Set Task Status to Debugging
            if($error['type'] == 'API'){
                $error['status'] = "debug";
            }elseif($error['type'] == 'BOT'){
                $error['status'] = "digiprompt";
            }

            // Assign task to a user
            $user = User::where('role', 'user')
                        ->where(function ($query) use ($error) {
                            $query->where('source', $error['source'])
                            ->orWhere('source', 'ALL');
                        })
                        ->where(function ($query) use ($error) {
                            $query->where('type', $error['type'])
                            ->orWhere('type', 'ALL');
                        })
                        ->first();

            $error['assigned'] = $user->name;

            $task = tasks::create($error);
            $task->save();

            // Save Uploaded Files
            if(array_key_exists('uploads', $error) && count($error['uploads']) > 0){
                foreach ($error['uploads'] as $upload){
                    $file_path = $upload->storeAs('uploads', $upload->getClientOriginalName(), 'public');

                    $fileUpload = new Uploads();
                    $fileUpload->name = $upload->getClientOriginalName();
                    $fileUpload->path = '/storage/'.$file_path;
                    $fileUpload->app_id = $request->app_id;
                    $fileUpload->carrier = $error['carrier'];
                    $fileUpload->save();
                }
            }
        }

        // Create  Application Notification
        $notification = new Notifications();
        $notification->app_id = $request->app_id;
        $notification->source = $source;
        $notification->errors = $count;
        $notification->carrier = $first_carrier;
        $notification->save();

        $response = [
            'success' => true,
            'message' => 'Tasks created successfully.'
        ];

        return response()->json($response, 200);
    }

    // Get Data for Specific Dashboard/Tasks Views
    public function index(Request $request, $filter = null){
        $type = $request->user()->type;
        $source = $request->user()->source;
        $product = $request->user()->product;

        if($filter == null){
            return 'no filter';
        } elseif($filter == 'dashboard'){
            // Get all Notifications
            if($source == "ALL"){
                $quick_notifications = Notifications::orderBy('created_at', 'desc')->take(4)->get();
                $unread_notifications = Notifications::where('read', false)->orderBy('created_at', 'desc')->get();
            } else {
                $quick_notifications = Notifications::where('source', $source)->orderBy('created_at', 'desc')->take(4)->get();
                $unread_notifications = Notifications::where('read', false)->where('source', $source)->orderBy('created_at', 'desc')->get();
            }

            $today = date('Y-m-d');

            if($source == "ALL"){
                if($product == "ALL"){
                    if($type == "ALL"){
                        $quick_tasks = tasks::orderBy('created_at', 'desc')->take(5)->get();
                        $tasks = tasks::orderBy('created_at', 'desc')->get();
                    }else {
                        $quick_tasks = tasks::where('type', $type)->orderBy('created_at', 'desc')->take(5)->get();
                        $tasks = tasks::where('type', $type)->orderBy('created_at', 'desc')->get();
                    }
                } else{
                    if($type == "ALL"){
                        $quick_tasks = tasks::where('product', $product)->orderBy('created_at', 'desc')->take(5)->get();
                        $tasks = tasks::where('product', $product)->orderBy('created_at', 'desc')->get();
                    }else {
                        $quick_tasks = tasks::where('product', $product)->where('type', $type)->orderBy('created_at', 'desc')->take(5)->get();
                        $tasks = tasks::where('product', $product)->where('type', $type)->orderBy('created_at', 'desc')->get();
                    }
                }
            }else {
                if($product == "ALL"){
                    if($type == "ALL"){
                        $quick_tasks = tasks::where('source', $source)->orderBy('created_at', 'desc')->take(5)->get();
                        $tasks = tasks::where('source', $source)->orderBy('created_at', 'desc')->get();
                    }else {
                        $quick_tasks = tasks::where('source', $source)->where('type', $type)->orderBy('created_at', 'desc')->take(5)->get();
                        $tasks = tasks::where('source', $source)->where('type', $type)->orderBy('created_at', 'desc')->get();
                    }
                } else{
                    if($type == "ALL"){
                        $quick_tasks = tasks::where('source', $source)->where('product', $product)->orderBy('created_at', 'desc')->take(5)->get();
                        $tasks = tasks::where('source', $source)->where('product', $product)->orderBy('created_at', 'desc')->get();
                    }else {
                        $quick_tasks = tasks::where('source', $source)->where('product', $product)->where('type', $type)->orderBy('created_at', 'desc')->take(5)->get();
                        $tasks = tasks::where('source', $source)->where('product', $product)->where('type', $type)->orderBy('created_at', 'desc')->get();
                    }
                }
            }

            $tasks_today = 0;
            $tasks_pending = 0;
            $tasks_fixed = 0;

            foreach($tasks as $task){
                if(str_contains((string) $task->created_at, $today)){
                    $tasks_today += 1;
                }

                if($task->fixed){
                    $tasks_fixed += 1;
                } else {
                    $tasks_pending += 1;
                }
            }

            $response = [
                'success' => true,
                'quick_notifications' => $quick_notifications,
                'unread_notifications' => $unread_notifications,
                'tasks' => $quick_tasks,
                'today' => $tasks_today,
                'pending' => $tasks_pending,
                'fixed' => $tasks_fixed
            ];

            return response()->json($response, 200);
        } else {
            if($source == "ALL"){
                if($product == "ALL"){
                    if($type == "ALL"){
                        $tasks = tasks::where('status', $filter)->orderBy('created_at', 'desc')->get();
                    }else {
                        $tasks = tasks::where('type', $type)->where('status', $filter)->orderBy('created_at', 'desc')->get();
                    }
                } else{
                    if($type == "ALL"){
                        $tasks = tasks::where('product', $product)->where('status', $filter)->orderBy('created_at', 'desc')->get();
                    }else {
                        $tasks = tasks::where('product', $product)->where('type', $type)->where('status', $filter)->orderBy('created_at', 'desc')->get();
                    }
                }
            }else {
                if($product == "ALL"){
                    if($type == "ALL"){
                        $tasks = tasks::where('source', $source)->where('status', $filter)->orderBy('created_at', 'desc')->get();
                    }else {
                        $tasks = tasks::where('source', $source)->where('type', $type)->where('status', $filter)->orderBy('created_at', 'desc')->get();
                    }
                } else{
                    if($type == "ALL"){
                        $tasks = tasks::where('source', $source)->where('product', $product)->where('status', $filter)->orderBy('created_at', 'desc')->get();
                    }else {
                        $tasks = tasks::where('source', $source)->where('product', $product)->where('type', $type)->where('status', $filter)->orderBy('created_at', 'desc')->get();
                    }
                }
            }

            $response = [
                'success'=> true,
                'tasks'=> $tasks
            ];

            return response()->json($response, 200);
        }
    }
}
