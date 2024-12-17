<?php

namespace App\Http\Controllers\Admin\Settings;

use Inertia\Inertia;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use Psr\Container\ContainerInterface;
use App\Http\Requests\NewsPaperLogoRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Setting::all();
        $defaultGroupTypes = $this->getGroupType('email_notification');
        $defaultTypeValues = $this->getTypeValues('email_notification', $defaultGroupTypes[0]->type);
        return Inertia::render('Settings/Generals/Index', [
            'settingsList' => $settings,
            'typevalues' => $defaultTypeValues,
            'groupname' => 'email_notification',
            'typename' => $defaultGroupTypes[0]->type,
            'grouptypes' => $defaultGroupTypes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function updateSettings(SettingRequest $request)
    {
        try {
            DB::beginTransaction();
            foreach ($request->settings as $key => $value) {
                $name = $value['name'];
                Setting::where('id', $value['id'])->update([
                    'value' => $value['value']
                ]);
            }
            DB::commit();
            flash('Settings saved', 'success');
            return \redirect()->route('dashboard.settings.generals.index');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            flash('Unable to find this settings', 'danger');
            return \redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function groupSetting($group)
    {
        $group = str_replace('-', '_', $group);
        $grouptypes = $this->getGroupType($group);
        $typevalues = $this->getTypeValues($group, $grouptypes[0]->type);
        return Inertia::render('Settings/Generals/Index', [
            'typevalues' => $typevalues,
            'groupname' => $group,
            'typename' => $grouptypes[0]->type,
            'grouptypes' => $grouptypes,
        ]);
    }

    public function groupType($group, Setting $type)
    {
        $group = str_replace('-', '_', $group);
        $typevalues = $this->getTypeValues($group, $type->type);
        return Inertia::render('Settings/Generals/Index', [
            'typevalues' => $typevalues,
            'groupname' => $type->group,
            'typename' => $type->type,
            'grouptypes' => $this->getGroupType($type->group)
        ]);
    }
    // generic function to get the type against each group
    public function getGroupType($groupname)
    {
        $data = Setting::select('*')->groupBy('type')->where('group', $groupname)->orderBy('id', 'asc')->get();
        return $data;
    }

    public function getTypeValues($group, $type)
    {
        $data = Setting::where('group', $group)->where('type', $type)->get();
        switch ($group) {
            case 'number_format_settings':
                return $data;
                break;
            case 'time_format_settings':
                return $data;
                break;
            case 'tax_model_settings':
                return $data;
                break;
            case 'stripe_connect_settings':
                return $data;
                break;
            case 'driver_assignment_settings':
                return $data;
            case 'checkout_fields_settings':
                return $data;
                break;
            case 'social_authentication':
                foreach ($data as $key) {
                    $value = $key->value;
                    if ($key->key == 'enable_facebook' || $key->key == 'enable_twitter' || $key->key == 'enable_google') {
                        $key->value = $value == 1 ? true : false;
                    }
                }
                return $data;
                break;
            default:
                foreach ($data as $key) {
                    $value = $key->value;
                    if ($type != 'General' || $key->key == 'enalble_push_notifications') {
                        $key->value = $value == 1 ? true : false;
                    }
                }
                return $data;
                break;
        }
    }
    public function changeTypeValues(SettingRequest $request)
    {
        // try {
        DB::beginTransaction();
        foreach ($request->settings as $key => $type) {
            $value = $type['value'];
            if ($request->group == 'email_notification' && $request->group == 'push_notification' && $type['type'] != 'General') {
                $value = $value === true ? '1' : '0';
            } else if ($request->group == 'push_notification' && $type['type'] == 'General') {
                if ($type['key'] == 'firebase_config_file') {
                    $value = uploadFirebaseConfigFile($type);
                }
            }
            $settings = Setting::findOrFail($type['id']);
            $settings->value = $value;
            $settings->save();
        }
        if ($request->group == 'stripe_connect_settings') {
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
        }
        DB::commit();
        // dd(env('APP_ENV'));
        if (env('APP_ENV') == 'staging') {
            exec("sudo supervisorctl restart queue-worker 2>&1", $output, $return_var);

            if ($return_var !== 0) {
                $error_message = end($output);
                Log::info($error_message);
                // handle the error
            } else {
                // command executed successfully
            }

            // dd('jns');
            // Artisan::call('supervisor:restart-program', ['name' => 'queue-worker']);
        }
        session()->flash('flash.type', 'success');
        session()->flash('flash.message', 'Settings Updated!');
        return redirect()->back();
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     session()->flash('flash.type', 'error');
        //     session()->flash('flash.message', $e->getMessage());
        //     return redirect()->back();
        // }
    }

    public function getNewsPaperLogo()
    {
        $logo = config()->get('image.media.news_Paper_logo');
        $newsPapaperLogo = Setting::where('key', 'newspaper_logo')->first();
        return Inertia::render('Settings/NewsPaperLogo/Index', [
            'newsPapreLogo' => $newsPapaperLogo,
            'logoSize' => $logo,
        ]);
    }

    public function updateNewsPaperLog(NewsPaperLogoRequest $request, $id)
    {
        try {
            $newsPapaperLogo = Setting::findOrFail($id);
            $logo = config()->get('image.media.news_Paper_logo');
            $width = $logo['width'];
            $height = $logo['height'];
            if ($newsPapaperLogo->value) {
                deleteFile($newsPapaperLogo->value);
            }
            if ($request->hasFile('value')) {
                $extension = $request->value->extension();
                $newsPapaperLogo->value = saveResizeImage($request->value, "NewsPaper/Logo", $width, $height, $extension);
            }
            $newsPapaperLogo->save();
            flash('News Paper Logo Uploaded Successfully', 'success');
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function saveDeviceToken(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $user->devices()->delete();
            $user->devices()->updateOrCreate([
                'device_token' => $request->device_token,
            ], [
                'device_type' => $request->device_type,
                'device_name' => $request->device_name,
                'language' => $request->language,
                'send_notification' => $request->notification
            ]);
        }
    }
}
