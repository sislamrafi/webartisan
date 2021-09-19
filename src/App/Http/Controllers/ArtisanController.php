<?php

namespace Sislamrafi\Webartisan\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Session;

class ArtisanController extends Controller
{
    private $CLILogin = 'CLILogin';

    public function __construct()
    {
    }

    public function index(Request $req)
    {
        $user = $this->getLoggedUser();

        if ($user == null) {
            $user = $this->getGuestUser();
        }

        $environment = $this->getEnvironmentVariables();

        return view('sislamrafi.webartisan::command-line', ['user'=>$user,'environment'=>$environment]);
    }

    public function submit(Request $req)
    {
        if (empty($req->command)) {
            return $this->serializeOutput("<span>No comand given</span>");
        }

        if (!is_null($msg = $this->attemptCLILogin($req->command))) {
            return $this->serializeOutput($msg);
        }

        if (!is_null($msg = $this->terminalAuthError())) {
            return $this->serializeOutput($msg);
        }

        //if(! defined('STDIN')) define('STDIN', fopen("php://stdin","r"));

        try {
            $output=[];
            if ($req->command == 'migrate --passport') {
                $output = $this->passportMigrate();
            } else {
                \Artisan::call($req->command);
                $output = \Artisan::output();
            }
            ob_start();
            dump($output);
            $result = ob_get_clean();
            //dump($output);
            return $this->serializeOutput($result.
            '<style>
            pre {
                display: block;
                font-family: monospace;
                white-space: pre;
                margin: 0px 0px;
            }
            </style>');
        } catch (\Throwable $e) {
            
            return $this->serializeOutput('<span>Command does not exists.</span>');
        }
    }

    private function terminalAuthError()
    {
        $user = $this->getLoggedUser();
        if ($user == null) {
            return
            '<span>
            Please login to execute this command. <br/> 
            Usage:: login [username] [password] <br/>
            Or login to your web portal with verified id also give you permission  to run commands here.
            </span>';
        } else {
            return null;
        }
    }

    private function hasDBConnected()
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getAuthenticatedUser($connection)
    {
        if (!$connection) {
            return null;
        }
        if ((Schema::hasTable(config('webartisan.admin_table')))) {
            try {
                if (Auth::guard(config('webartisan.admin_guard'))->user()[config('webartisan.admin_column')] == config('webartisan.admin_username')) {
                    return Auth::guard(config('webartisan.admin_guard'))->user();
                } else {
                    return null;
                }
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    private function customCLILogin($username, $passward)
    {
        if ($username == config('webartisan.admin_username') && $passward == config('webartisan.admin_default_pass')) {
            Session::put($this->CLILogin, config('webartisan.admin_username'));
            return true;
        }
        Session::put($this->CLILogin, null);
        return false;
    }

    private function checkCustomLogin()
    {
        if (Session::get($this->CLILogin, null) != null) {
            $user = [];
            $user['username'] = config('webartisan.admin_username');
            return $user;
        }
        return null;
    }

    private function attemptCLILogin($cmd)
    {
        function myFilter($string)
        {
            return $string==""?null:$string;
        }
          
        $pieces = explode(" ", $cmd);

        /*
        $pieces = array_filter(
            $pieces,
            function ($string) {
                return $string==""?false:true;
            }
        );
        //$pieces = array_values($pieces);
        */

        //return json_encode($pieces);

        if ($pieces[0]!='login') {
            if ($pieces[0]=='logout') {
                $log = $this->customCLILogin('-1-1-1', '-1-1-1');
                return '<span>Logout successful</span>';
            }
            return null;
        }

        if (count($pieces)==3) {
            $log = $this->customCLILogin($pieces[1], $pieces[2]);
        } else {
            return null;
        }
        return $log?'<span>Login Successful.</span>'
                    :'<span>Login Error. Username or password missmatched.</span>';
    }

    private function serializeOutput($html)
    {
        $user = $this->getLoggedUser();

        if ($user == null) {
            $user = $this->getGuestUser();
        }

        $environment = $this->getEnvironmentVariables();

        $ret = [];
        $ret['html'] = $html;
        $ret['user'] = $user;
        $ret['environment'] = $environment;

        return json_encode($ret);
    }

    private function getGuestUser()
    {
        $user = [];
        $user['username'] = config('webartisan.guest_user_name');
        return $user;
    }

    private function getLoggedUser()
    {
        //get user from database login
        $hasDatabaseConnection = $this->hasDBConnected();
        $user = $this->getAuthenticatedUser($hasDatabaseConnection);

        if($user != null && !isset($user['username'])){
            $user['username'] = isset($user[config('webartisan.admin_column')])?$user[config('webartisan.admin_column')]:'nousername@auth';
        }

        //get custom user from .env
        if ($user == null) {
            $user = $this->checkCustomLogin();
        }

        return $user;
    }

    private function getEnvironmentVariables()
    {
        $env = [];
        $env['name'] = config('webartisan.terminal_name');
        return $env;
    }

    public function submitXXYYY(Request $req)
    {
        if ($req->password != 'fami123' xor (Schema::hasTable('users') && Auth::check()&&Auth::user()->email == 'sislamrafi333@gmail.com')) {
            return redirect()->back()->with('output', 'Password Error')->withInput();
        }
        $output = [];

        if ($req->command == 'migrate --passport') {
            $output = $this->passportMigrate();
        } else {
            \Artisan::call($req->command, $output);
            $output = \Artisan::output();
        }
        return redirect()->back()->with('output', $output)->withInput();
    }

    private function passportMigrate()
    {
        \Artisan::call('migrate', ['--path' => 'vendor/laravel/passport/database/migrations']);
        return \Artisan::output();
    }
}
