<?php

namespace App\Traits;

use App\Models\Module;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

trait UserMenuTrait
{
    public function getPermissionAttribute() {
        if($this->isAdmin()){
            return Permission::pluck('name')->toArray();
        }
        return $this->getAllPermissions()->pluck('name')->toArray();
    }

    public function getUserMenuAttribute()
    {
        return $this->userMenu();
    }

    public function userMenu()
    {
        $modules = [];
        if(!empty(request()->route())){
            $modules['dashboard'] = [
                'id' => '123',
                'text' => 'Dashboard',
                'link' => route('dashboard'),
                // 'icon' => 'meter',
                'type' => 'item',
                'active' => request()->route()->getName() == 'admin.dashboard',
                'subtitle' => '',
            ];
            $modulesApp = Module::parent()->orderBy('order')->get();
            if($modulesApp){
                foreach($modulesApp as $module){
                    $currentRoot = false;
                    $add = false;
                    $childrens_tmp = [];
                    if($module->has('children') && $module->children()->where('installed',true)->count() > 0){
                        $currentRoot = false;
                        foreach($module->children()->where('installed',true)->orderBy('order')->get() as $children){
                            if(Route::has($children->slug) && (auth()->user()->isAdmin() || auth()->user()->can('browse-'.str_replace('.index','',$children->slug)))){
                                $add = true;
                                if(!$currentRoot){
                                    $currentRoot = request()->route()->getName() == $children->slug || str_replace(['.index','.create','.show','.edit','.meli'],'.',request()->route()->getName()) == str_replace(['.index','.create','.show','.edit'],'.',$children->slug) ? true : false;
                                }
                                $childrens_tmp[] = [
                                    'id' => $children->id,
                                    'text' => $children->name,
                                    'link' => route($children->slug),
                                    // 'icon' => $children->icon,
                                    'type' => $children->header == true ? 'group' : 'item',
                                    'active' => request()->route()->getName() == $children->slug || str_replace(['.index','.create','.show','.edit'],'.',request()->route()->getName()) == str_replace(['.index','.create','.show','.edit'],'.',$children->slug) ? true : false,
                                    'badge' => '',
                                    // 'badge' => [
                                        // 'text' => '27',
                                        // 'bg' => 'red',
                                        // 'classes' => 'px-8 bg-pink-600 text-white rounded-full',
                                    // ],
                                ];
                            }
                        }
                        if($add){
                            $modules[$module->slug] = [
                                'id' => $module->id,
                                'text' => $module->name,
                                'link' => $module->url,
                                // 'icon' => $module->icon,
                                'type' => $module->header == true ? 'group' : 'item',
                                'subMenu' => $childrens_tmp,
                                'active' => $currentRoot,
                                'subtitle' => $module->description,
                            ];
                        }
                    }else{
                        if(Route::has($module->slug) && (auth()->user()->isAdmin() || auth()->user()->can('browse-'.str_replace('.index','',$module->slug)))){
                            $currentRoot = request()->route()->getName() == $module->slug || str_replace(['.index','.create','.show','.edit','.meli'],'.',request()->route()->getName()) == str_replace(['.index','.create','.show','.edit'],'.',$module->slug) ? true : false;
                            $modules[$module->slug] = [
                                'id' => $module->id,
                                'text' => $module->name,
                                'link' => route($module->slug),
                                // 'icon' => $module->icon,
                                'type' => $module->header == true ? 'group' : 'item',
                                'active' => request()->route()->getName() == $module->slug || str_replace(['.index','.create','.show','.edit'],'.',request()->route()->getName()) == str_replace(['.index','.create','.show','.edit'],'.',$module->slug) ? true : false,
                                // 'badge' => [
                                    // 'text' => '27',
                                    // 'bg' => 'red',
                                    // 'classes' => 'px-8 bg-pink-600 text-white rounded-full',
                                // ],
                            ];
                        }
                    }
                }
            }
        }
        return $modules;
    }
}