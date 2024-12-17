<?php

namespace App\Http\Controllers\Admin\Settings;

use Inertia\Inertia;
use App\Models\StandardTag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatSettingsController extends Controller
{
    public function index(){
        $modules = StandardTag::where('type', 'module')->get();
        return Inertia::render('Settings/ChatSettings/Index', [
            'modulesList' => $modules
        ]);
    }

    public function updateChatSettings(Request $request)
    {
        try {
            $modules = $request->input('modules');
            foreach ($modules as $module) {
                $tag = StandardTag::where('id', $module['id'])->first();
                $tag->can_chat = $module['can_chat'];
                $tag->saveQuietly();
            }
            flash('Module chat settings updated successfully.', 'success');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
