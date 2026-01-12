<?php
namespace App\Exceptions;
use Exception;
class CustomException extends Exception
{
    protected $status ; 
    protected $message ;
    protected $route ; 
    public function __construct($message, $status = 403, $route = null){
        $this->message = $message; 
        $this->status  = $status;
        $this->route   = $route;
    }
    public function render()
    {
        if(request()->expectsJson()) {
            return response()->json([
                'status' => false,
                'message' => $this->message,
            ], $this->status);
        }
        if($this->route) {
            return redirect()->route($this->route)->with('flash_data', [
                'status' => false,
                'message' => $this->message,
            ]);
        }else{
            return redirect()->back()->with('flash_data', [
                'status' => false,
                'message' => $this->message,
            ]);
        }
    }
}
