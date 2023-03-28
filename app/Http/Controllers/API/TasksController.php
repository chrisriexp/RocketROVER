<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\tasks;
use App\Models\User;
use App\Models\Notifications;
use App\Models\Notes;
use Carbon\Carbon;

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
    public function test(Request $request, $id){
        $task = tasks::find($id);
        $task->tests = $task->tests + 1;
        $task->save();

        $note = new Notes();
        $note->app_id = $task->app_id;
        $note->carrier = $task->carrier;
        $note->user = $request->user()->name;
        $note->note = "Test ".$task->tests." Completion Log: ".$request->user()->name." marked this test as completed on ".Carbon::now()->toDayDateTimeString().", please provide more details below:";
        $note->test = true;
        $note->save();

        return response()->json(['success'=> true], 200);
    }

    // Update Task Status
    public function status(Request $request, $id){
        $task = tasks::find($id);

        // Updating from Debugging Status
        if($task->status == 'debug'){
            $task->status = 'update';
            $task->save();

            $note = new Notes();
            $note->app_id = $task->app_id;
            $note->carrier = $task->carrier;
            $note->user = $request->user()->name;
            $note->note = "Status Update Log: This error's status was changed from Debugging to Implement Updates by ".$request->user()->name." on ".Carbon::now()->toDayDateTimeString();
            $note->disabled = true;
            $note->save();
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

        $carrierTypes = [
            "aon"=> 'API',
            "beyond"=> 'API',
            "cat"=> 'API',
            "dual"=> 'API',
            "flow"=> 'API',
            "frs"=> 'BOT',
            "hippo"=> 'API',
            "neptune"=> 'API',
            "palomar"=> 'API',
            "pmf"=> 'BOT',
            "sterling"=> 'API',
            "superior"=> 'BOT',
            "tower"=> 'BOT',
            "wright"=> 'API',

        ];

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

            // Set Task Type based on Carrier
            $error['type'] = $carrierTypes[$error['carrier']];

            // Set Task Status to Debugging
            $error['status'] = "debug";

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
        if($filter == null){
            return 'no filter';
        } elseif($filter == 'dashboard'){
            // Get all Notifications
            $quick_notifications = Notifications::orderBy('created_at', 'desc')->take(4)->get();
            $unread_notifications = Notifications::where('read', false)->orderBy('created_at', 'desc')->get();

            $today = date('Y-m-d');

            $quick_tasks = tasks::orderBy('created_at', 'desc')->take(5)->get();
            $tasks = tasks::orderBy('created_at', 'desc')->get();
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
            $tasks = tasks::where('status', $filter)->orderBy('created_at', 'desc')->get();

            $response = [
                'success'=> true,
                'tasks'=> $tasks
            ];

            return response()->json($response, 200);
        }
    }
}
