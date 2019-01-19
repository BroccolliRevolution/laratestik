<?php
use Illuminate\Support\Facades\DB;
use App\Task;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function ($name = 'John') {
    $person = new stdClass();
    $person->age = 32;
    $person->name = $name;
    return 'cav';
});

Route::get('/tasks/{tasks}', 'TaskController@index');

Route::get('/tasks2/{task}', function (Task $task) {
  return $task;
});

Route::get('/all', function () {
  $tasks = Task::all();
  return view('welcome', compact('tasks'));
});

Route::get('testikk/{name?}', function ($name = 'John') {
  $person = new stdClass();
  $person->age = 32;
  $person->name = $name;
  return $name;
})->name('myTestik');

//Route::get('tasks/{id}', function ($id) {
//  $task = Task::find($id);
//  return view('tasks.show', compact('task'));
//});
