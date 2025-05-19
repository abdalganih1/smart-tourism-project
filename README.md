<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


---

تمام، لنقم ببناء لوحة تحكم المدير لإدارة الفنادق، أنواع الغرف، والغرف. سنتبع نفس النمط الذي استخدمناه لإدارة المستخدمين والمواقع السياحية.

**ملاحظات هامة قبل البدء:**

*   نفترض أنك قد نفذت `php artisan storage:link` بنجاح للسماح بالوصول العام إلى الملفات المخزنة.
*   نفترض أن لديك نماذج `Hotel`, `HotelRoomType`, `HotelRoom`, `User` (مع العلاقة `profile`)، وأن ملفات Migration الخاصة بها صحيحة وتستخدم `id` كمفتاح أساسي وأن المفاتيح الأجنبية تشير إلى `id` (`foreignId`).
*   نفترض أن Gate `can:access-admin-panel` معرف في `AuthServiceProvider.php`.
*   نفترض أن لديك روابط `Route::resource` لـ `hotels`, `hotel-room-types`, `hotel-rooms` داخل مجموعة مسارات المدير في `routes/web.php` مع البادئة `admin.`.
    ```php
    // ... inside Route::prefix('admin')->name('admin.')->middleware([...])->group(...)
    Route::resource('hotels', Admin\HotelController::class);
    Route::resource('hotel-room-types', Admin\HotelRoomTypeController::class); // Note: plural name convention
    Route::resource('hotel-rooms', Admin\HotelRoomController::class); // Note: plural name convention
    // ...
    ```

---

**الجزء 1: إدارة أنواع غرف الفنادق (Hotel Room Types)**

**1. محتوى متحكم `app/Http/Controllers/Admin/HotelRoomTypeController.php`:**

هذا المتحكم بسيط لأن `HotelRoomTypes` جدول lookup أساسي.

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HotelRoomType; // Import HotelRoomType model
use App\Http\Requests\Admin\StoreHotelRoomTypeRequest; // Import custom Store Request
use App\Http\Requests\Admin\UpdateHotelRoomTypeRequest; // Import custom Update Request
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // For authorization check (optional, handled by middleware/policy)


class HotelRoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible at GET /admin/hotel-room-types
     */
    public function index()
    {
        // Fetch all hotel room types, ordered by name
        // No pagination needed for a lookup table unless it's huge
        $roomTypes = HotelRoomType::orderBy('name')->get();

        // Return the view and pass the data
        return view('admin.hotel_room_types.index', compact('roomTypes'));
    }

    /**
     * Show the form for creating a new resource.
     * Accessible at GET /admin/hotel-room-types/create
     */
    public function create()
    {
        // Return the view for creating a room type
        return view('admin.hotel_room_types.create');
    }

    /**
     * Store a newly created resource in storage.
     * Accessible at POST /admin/hotel-room-types
     *
     * @param  \App\Http\Requests\Admin\StoreHotelRoomTypeRequest  $request // Use custom request for validation
     */
    public function store(StoreHotelRoomTypeRequest $request)
    {
        // Validation is handled by StoreHotelRoomTypeRequest

        try {
            $roomTypeData = $request->only(['name', 'description']);

            // Create the HotelRoomType
            HotelRoomType::create($roomTypeData);

            return redirect()->route('admin.hotel-room-types.index')->with('success', 'Hotel room type created successfully!');

        } catch (\Exception $e) {
            Log::error('Error creating hotel room type: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error creating hotel room type: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Accessible at GET /admin/hotel-room-types/{hotelRoomType}
     * Note: Show is less common for simple lookup tables. Can be omitted.
     *
     * @param  \App\Models\HotelRoomType  $hotelRoomType // Route Model Binding
     */
    public function show(HotelRoomType $hotelRoomType)
    {
         // Redirect to edit page as show page is often redundant for simple resources
         return redirect()->route('admin.hotel-room-types.edit', $hotelRoomType);

        // // Or return a show view if you create one
        // return view('admin.hotel_room_types.show', compact('hotelRoomType'));
    }

    /**
     * Show the form for editing the specified resource.
     * Accessible at GET /admin/hotel-room-types/{hotelRoomType}/edit
     *
     * @param  \App\Models\HotelRoomType  $hotelRoomType // Route Model Binding
     */
    public function edit(HotelRoomType $hotelRoomType)
    {
        // Return the view for editing a room type and pass the data
        return view('admin.hotel_room_types.edit', compact('hotelRoomType'));
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /admin/hotel-room-types/{hotelRoomType}
     *
     * @param  \App\Http\Requests\Admin\UpdateHotelRoomTypeRequest  $request // Use custom request for validation
     * @param  \App\Models\HotelRoomType  $hotelRoomType // Route Model Binding
     */
    public function update(UpdateHotelRoomTypeRequest $request, HotelRoomType $hotelRoomType)
    {
         // Validation is handled by UpdateHotelRoomTypeRequest

        try {
            $roomTypeData = $request->only(['name', 'description']);

            // Update the HotelRoomType
            $hotelRoomType->update($roomTypeData);

            return redirect()->route('admin.hotel-room-types.index')->with('success', 'Hotel room type updated successfully!');

        } catch (\Exception $e) {
             Log::error('Error updating hotel room type: ' . $e->getMessage(), ['room_type_id' => $hotelRoomType->id, 'exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error updating hotel room type: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /admin/hotel-room-types/{hotelRoomType}
     *
     * @param  \App\Models\HotelRoomType  $hotelRoomType // Route Model Binding
     */
    public function destroy(HotelRoomType $hotelRoomType)
    {
         try {
             // Check if any rooms use this room type (based on restrict delete in migration)
             // If rooms exist, deleting the type will fail due to foreign key constraint.
             // We can add a check here for a more user-friendly error message before deletion attempt.
             if ($hotelRoomType->rooms()->count() > 0) {
                  return redirect()->route('admin.hotel-room-types.index')->with('error', 'Cannot delete room type as it is used by existing rooms.');
             }


             $hotelRoomType->delete(); // This attempts deletion

             return redirect()->route('admin.hotel-room-types.index')->with('success', 'Hotel room type deleted successfully!');

         } catch (\Exception $e) {
             Log::error('Error deleting hotel room type: ' . $e->getMessage(), ['room_type_id' => $hotelRoomType->id, 'exception' => $e]);
             // Catch specific Integrity Constraint Violation exceptions if needed
              if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000') {
                   return redirect()->route('admin.hotel-room-types.index')->with('error', 'Room type cannot be deleted due to associated rooms.');
              }
             return redirect()->route('admin.hotel-room-types.index')->with('error', 'Error deleting hotel room type: ' . $e->getMessage());
         }
    }
}
```

**2. محتوى ملفات Request المخصصة (Hotel Room Types):**

**أ. محتوى ملف `app/Http/Requests/Admin/StoreHotelRoomTypeRequest.php`:**

```php
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreHotelRoomTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin or Employee can create room types
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'Employee']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:hotel_room_types'], // Name must be unique
            'description' => ['nullable', 'string'], // Description is optional
        ];
    }

     /**
      * Get custom attributes for validator errors.
      *
      * @return array<string, string>
      */
     public function attributes(): array
     {
         return [
             'name' => __('Room Type Name'),
             'description' => __('Description'),
         ];
     }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم نوع الغرفة مطلوب.',
            'name.unique' => 'اسم نوع الغرفة هذا موجود بالفعل.',
            // ... add messages for other rules
        ];
    }
}
```

**ب. محتوى ملف `app/Http/Requests/Admin/UpdateHotelRoomTypeRequest.php`:**

```php
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateHotelRoomTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin or Employee can update room types
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'Employee']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the room type ID from the route parameters to ignore it during unique check
        $roomTypeId = $this->route('hotel_room_type')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('hotel_room_types', 'name')->ignore($roomTypeId), // Ignore the current room type
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
      * Get custom attributes for validator errors.
      *
      * @return array<string, string>
      */
     public function attributes(): array
     {
         return [
             'name' => __('Room Type Name'),
             'description' => __('Description'),
         ];
     }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم نوع الغرفة مطلوب.',
            'name.unique' => 'اسم نوع الغرفة هذا موجود بالفعل.',
            // ... add messages for other rules
        ];
    }
}
```

**3. تعليمات تصميم ملفات الـ Views (Hotel Room Types):**

```bash
# إنشاء مجلد أنواع غرف الفنادق داخل مجلد المدير Views
mkdir resources/views/admin/hotel_room_types

# إنشاء ملف View لقائمة أنواع غرف الفنادق (Index)
php artisan make:view admin/hotel_room_types/index

# إنشاء ملف View لنموذج إضافة نوع غرفة جديد (Create)
php artisan make:view admin/hotel_room_types/create

# إنشاء ملف View لنموذج تعديل نوع غرفة موجود (Edit)
php artisan make:view admin/hotel_room_types/edit

# (Show view is often omitted for simple lookup resources)
```

**4. محتوى ملفات الـ View المقترحة (Hotel Room Types):**

**أ. محتوى ملف `resources/views/admin/hotel_room_types/index.blade.php`:**

```html
@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Manage Hotel Room Types') }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Session Status/Messages --}}
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif
                     @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Link to Create New Room Type --}}
                    <div class="mb-4">
                        <a href="{{ route('admin.hotel-room-types.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Add New Room Type') }}
                        </a>
                    </div>

                    {{-- Hotel Room Types Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('ID') }}
                                    </th>
                                     <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Name') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Description') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($roomTypes as $type)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $type->id }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $type->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $type->description ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                            {{-- No View link needed for simple resource --}}
                                            <a href="{{ route('admin.hotel-room-types.edit', $type) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-600 mr-3">Edit</a>
                                            <form action="{{ route('admin.hotel-room-types.destroy', $type) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600" onclick="return confirm('Are you sure you want to delete this room type?');">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                     {{-- No pagination needed if fetching all --}}

                </div>
            </div>
        </div>
    </div>
@endsection
```

**ب. محتوى ملف `resources/views/admin/hotel_room_types/create.blade.php`:**

```html
@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Create Hotel Room Type') }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Session Status/Messages --}}
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif
                     @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Form for creating a new room type --}}
                    <form method="POST" action="{{ route('admin.hotel-room-types.store') }}">
                        @csrf

                        {{-- Room Type Information Section --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Room Type Details') }}</h3>
                            <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                {{-- Name --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="name" :value="__('Room Type Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                {{-- Description --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="description" :value="__('Description (Optional)')" />
                                     <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Create Room Type') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
```

**ج. محتوى ملف `resources/views/admin/hotel_room_types/edit.blade.php`:**

```html
@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Edit Hotel Room Type') }}: {{ $hotelRoomType->name }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Session Status/Messages --}}
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif
                     @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Form for editing a room type --}}
                    <form method="POST" action="{{ route('admin.hotel-room-types.update', $hotelRoomType) }}">
                        @csrf
                        @method('PUT') {{-- Use PUT method for update --}}

                        {{-- Room Type Information Section --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Room Type Details') }}</h3>
                            <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                {{-- Name --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="name" :value="__('Room Type Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $hotelRoomType->name)" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                {{-- Description --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="description" :value="__('Description (Optional)')" />
                                     <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $hotelRoomType->description) }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Update Room Type') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
```

---

**الجزء 2: إدارة الفنادق (Hotels)**

**1. محتوى متحكم `app/Http/Controllers/Admin/HotelController.php`:**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel; // Import Hotel model
use App\Models\User; // Import User model (for managers)
use App\Http\Requests\Admin\StoreHotelRequest; // Import custom Store Request
use App\Http\Requests\Admin\UpdateHotelRequest; // Import custom Update Request
use Illuminate\Support\Facades\Storage; // For file storage
use Illuminate\Support\Facades\DB; // For database transactions
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // For authorization check (optional, handled by middleware/policy)


class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible at GET /admin/hotels
     */
    public function index()
    {
        // Fetch hotels with their managedBy relationship, paginated
        $hotels = Hotel::with(['managedBy:id,username']) // Load manager's basic info
                       ->orderBy('created_at', 'desc')
                       ->paginate(10); // Paginate results

        // Return the view and pass the data
        return view('admin.hotels.index', compact('hotels'));
    }

    /**
     * Show the form for creating a new resource.
     * Accessible at GET /admin/hotels/create
     */
    public function create()
    {
        // Fetch users who can manage hotels (HotelBookingManager, Admin). Pass only ID and username.
        $managers = User::whereIn('user_type', ['HotelBookingManager', 'Admin'])->select('id', 'username')->get();

        // Return the view and pass data
        return view('admin.hotels.create', compact('managers'));
    }

    /**
     * Store a newly created resource in storage.
     * Accessible at POST /admin/hotels
     *
     * @param  \App\Http\Requests\Admin\StoreHotelRequest  $request // Use custom request for validation
     */
    public function store(StoreHotelRequest $request)
    {
        // Validation is handled by StoreHotelRequest

        DB::beginTransaction();

        try {
            $hotelData = $request->only([
                'name', 'star_rating', 'description', 'address_line1',
                'city', 'country', 'latitude', 'longitude', 'contact_phone',
                'contact_email', 'managed_by_user_id'
            ]);

            // Handle file upload for main_image if present
            if ($request->hasFile('main_image')) {
                $imagePath = $request->file('main_image')->store('hotels', 'public'); // Store in 'storage/app/public/hotels'
                $hotelData['main_image_url'] = '/storage/' . $imagePath; // Save public path
            }

            // Set country default if not provided and column is nullable (though schema has default 'Syria')
             if (!isset($hotelData['country'])) {
                 $hotelData['country'] = 'Syria';
             }

            // Create the Hotel
            $hotel = Hotel::create($hotelData);

            DB::commit(); // Commit transaction

            return redirect()->route('admin.hotels.index')->with('success', 'Hotel created successfully!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction
            // Clean up uploaded file if transaction failed and file was stored
            if (isset($imagePath)) {
                 Storage::disk('public')->delete($imagePath);
            }
            Log::error('Error creating hotel: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error creating hotel: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Accessible at GET /admin/hotels/{hotel}
     *
     * @param  \App\Models\Hotel  $hotel // Route Model Binding
     */
    public function show(Hotel $hotel)
    {
        // Load relationships for the detailed view
        $hotel->load(['managedBy.profile', 'rooms.type', 'rooms.bookings']); // Load manager with profile, and rooms with type/bookings

        // Return the view and pass the data
        return view('admin.hotels.show', compact('hotel'));
    }

    /**
     * Show the form for editing the specified resource.
     * Accessible at GET /admin/hotels/{hotel}/edit
     *
     * @param  \App\Models\Hotel  $hotel // Route Model Binding
     */
    public function edit(Hotel $hotel)
    {
        // Load relationships for the edit form
        $hotel->load(['managedBy.profile', 'rooms.type']); // Rooms might be managed separately, but helpful to see

        // Fetch necessary data for the form (same as create)
        $managers = User::whereIn('user_type', ['HotelBookingManager', 'Admin'])->select('id', 'username')->get();

        // Return the view and pass data
        return view('admin.hotels.edit', compact('hotel', 'managers'));
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /admin/hotels/{hotel}
     *
     * @param  \App\Http\Requests\Admin\UpdateHotelRequest  $request // Use custom request for validation
     * @param  \App\Models\Hotel  $hotel // Route Model Binding
     */
    public function update(UpdateHotelRequest $request, Hotel $hotel)
    {
        // Validation is handled by UpdateHotelRequest

        DB::beginTransaction();
        $oldImagePath = $hotel->main_image_url; // Store old path to delete on success

        try {
            $hotelData = $request->only([
                'name', 'star_rating', 'description', 'address_line1',
                'city', 'country', 'latitude', 'longitude', 'contact_phone',
                'contact_email', 'managed_by_user_id'
            ]);

             // Handle file upload for main_image if present
            if ($request->hasFile('main_image')) {
                // Upload the new image
                $imagePath = $request->file('main_image')->store('hotels', 'public');
                $hotelData['main_image_url'] = '/storage/' . $imagePath; // Save public path

                 // Delete the old image file if it existed
                 if ($oldImagePath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldImagePath))) {
                      Storage::disk('public')->delete(str_replace('/storage/', '', $oldImagePath));
                 }
            } else if ($request->boolean('remove_main_image')) { // Handle checkbox to remove image
                 $hotelData['main_image_url'] = null;
                 if ($oldImagePath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldImagePath))) {
                     Storage::disk('public')->delete(str_replace('/storage/', '', $oldImagePath));
                 }
            }

            // Set country default if null (important if form can send null)
             if (isset($hotelData['country']) && is_null($hotelData['country'])) {
                 $hotelData['country'] = 'Syria'; // Or based on your logic
             }


            // Update the Hotel
            $hotel->update($hotelData);

            DB::commit(); // Commit transaction

            return redirect()->route('admin.hotels.index')->with('success', 'Hotel updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction
            // If a new file was uploaded, clean it up on error
            if (isset($imagePath)) {
                 Storage::disk('public')->delete($imagePath);
            }
            Log::error('Error updating hotel: ' . $e->getMessage(), ['hotel_id' => $hotel->id, 'exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error updating hotel: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /admin/hotels/{hotel}
     *
     * @param  \App\Models\Hotel  $hotel // Route Model Binding
     */
    public function destroy(Hotel $hotel)
    {
         DB::beginTransaction();
         $oldImagePath = $hotel->main_image_url; // Store old path to delete

         try {
             // Optional: Check for related data (Rooms, Bookings)
             // If your migrations have 'restrict' on foreign keys like hotel_id
             // in hotel_rooms or hotel_bookings, the delete will fail
             // and throw an exception if related records exist.
             // Add checks here if needed for more specific error messages.
             // Note: Deleting a hotel should likely cascade to rooms, which might cascade to bookings.
             // Verify your migration onDelete rules. If Rooms cascade from Hotel, and Bookings restrict from Room,
             // you might need to check Bookings first.
             if ($hotel->rooms()->whereHas('bookings')->count() > 0) {
                  return redirect()->route('admin.hotels.index')->with('error', 'Cannot delete hotel as it has rooms with associated bookings.');
             }
              // If rooms should prevent deletion even without bookings:
             // if ($hotel->rooms()->count() > 0) {
             //      return redirect()->route('admin.hotels.index')->with('error', 'Cannot delete hotel as it has associated rooms.');
             // }


             $hotel->delete(); // This attempts deletion. If using soft deletes, it marks as deleted.

             DB::commit(); // Commit transaction

             // Delete the image file after successful DB deletion
             if ($oldImagePath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldImagePath))) {
                  Storage::disk('public')->delete(str_replace('/storage/', '', $oldImagePath));
             }


             return redirect()->route('admin.hotels.index')->with('success', 'Hotel deleted successfully!');

         } catch (\Exception $e) {
             DB::rollBack(); // Rollback transaction
             Log::error('Error deleting hotel: ' . $e->getMessage(), ['hotel_id' => $hotel->id, 'exception' => $e]);
              // Catch specific Integrity Constraint Violation exceptions if needed
              if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000') {
                   return redirect()->route('admin.hotels.index')->with('error', 'Hotel cannot be deleted due to associated data (e.g., rooms, bookings).');
              }
             return redirect()->route('admin.hotels.index')->with('error', 'Error deleting hotel: ' . $e->getMessage());
         }
    }
}
```

**2. محتوى ملفات Request المخصصة (Hotels):**

**أ. محتوى ملف `app/Http/Requests/Admin/StoreHotelRequest.php`:**

```php
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreHotelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin or Employee or HotelBookingManager can create hotels
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'Employee', 'HotelBookingManager']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Define allowed user types for managers
        $allowedManagerTypes = ['Admin', 'HotelBookingManager']; // Assuming Employee doesn't manage hotels directly

        return [
            'name' => ['required', 'string', 'max:150'],
            'star_rating' => ['nullable', 'integer', 'between:1,7'], // Assuming 1-7 stars
            'description' => ['nullable', 'string'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'], // Allow null, default 'Syria' handled
             // Latitude/Longitude validation
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_email' => ['nullable', 'string', 'email', 'max:100'],
            'main_image' => ['nullable', 'image', 'max:5120'], // Optional image upload, max 5MB
            'managed_by_user_id' => [
                'nullable', // Can be null if no specific manager assigned yet
                'integer',
                // Ensure managed_by_user_id exists in users table and has an allowed type, if provided
                Rule::exists('users', 'id')->where(function ($query) use ($allowedManagerTypes) {
                     $query->whereIn('user_type', $allowedManagerTypes);
                }),
            ],
        ];
    }

     /**
      * Get custom attributes for validator errors.
      *
      * @return array<string, string>
      */
     public function attributes(): array
     {
         return [
             'name' => __('Hotel Name'),
             'star_rating' => __('Star Rating'),
             'description' => __('Description'),
             'address_line1' => __('Address Line 1'),
             'city' => __('City'),
             'country' => __('Country'),
             'latitude' => __('Latitude'),
             'longitude' => __('Longitude'),
             'contact_phone' => __('Contact Phone'),
             'contact_email' => __('Contact Email'),
             'main_image' => __('Main Image'),
             'managed_by_user_id' => __('Managed By User'),
         ];
     }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم الفندق مطلوب.',
            'star_rating.between' => 'عدد النجوم يجب أن يكون بين :min و :max.',
            'managed_by_user_id.exists' => 'المستخدم المحدد كـ "المدير" غير صالح.',
            'latitude.numeric' => 'خط العرض يجب أن يكون رقماً.',
            'longitude.numeric' => 'خط الطول يجب أن يكون رقماً.',
            'latitude.between' => 'خط العرض يجب أن يكون بين -90 و 90.',
            'longitude.between' => 'خط الطول يجب أن يكون بين -180 و 180.',
            'main_image.image' => 'الصورة الرئيسية يجب أن تكون ملف صورة.',
            'main_image.max' => 'حجم الصورة الرئيسية لا يجب أن يتجاوز 5 ميجابايت.',
            'contact_email.email' => 'صيغة البريد الإلكتروني للتواصل غير صحيحة.',
            // ... add messages for other rules
        ];
    }
}
```

**ب. محتوى ملف `app/Http/Requests/Admin/UpdateHotelRequest.php`:**

```php
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateHotelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin or Employee or HotelBookingManager can update hotels.
        // A Policy would be ideal here to check if user can update *this specific* hotel.
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'Employee', 'HotelBookingManager']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         // Get the hotel ID from the route parameters if needed
         // $hotelId = $this->route('hotel')->id;

        // Define allowed user types for managers
        $allowedManagerTypes = ['Admin', 'HotelBookingManager'];

        return [
            'name' => ['required', 'string', 'max:150'],
            'star_rating' => ['nullable', 'integer', 'between:1,7'],
            'description' => ['nullable', 'string'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_email' => ['nullable', 'string', 'email', 'max:100'],
            'main_image' => ['nullable', 'image', 'max:5120'],
            // Rule to validate 'remove_main_image' checkbox
            'remove_main_image' => ['nullable', 'boolean'], // Expect 0 or 1 from checkbox
            'managed_by_user_id' => [
                'nullable',
                'integer',
                 Rule::exists('users', 'id')->where(function ($query) use ($allowedManagerTypes) {
                     $query->whereIn('user_type', $allowedManagerTypes);
                }),
            ],
        ];
    }

    /**
      * Get custom attributes for validator errors.
      *
      * @return array<string, string>
      */
     public function attributes(): array
     {
         return [
             'name' => __('Hotel Name'),
             'star_rating' => __('Star Rating'),
             'description' => __('Description'),
             'address_line1' => __('Address Line 1'),
             'city' => __('City'),
             'country' => __('Country'),
             'latitude' => __('Latitude'),
             'longitude' => __('Longitude'),
             'contact_phone' => __('Contact Phone'),
             'contact_email' => __('Contact Email'),
             'main_image' => __('Main Image'),
             'remove_main_image' => __('Remove Main Image'),
             'managed_by_user_id' => __('Managed By User'),
         ];
     }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم الفندق مطلوب.',
            'star_rating.between' => 'عدد النجوم يجب أن يكون بين :min و :max.',
            'managed_by_user_id.exists' => 'المستخدم المحدد كـ "المدير" غير صالح.',
            'latitude.numeric' => 'خط العرض يجب أن يكون رقماً.',
            'longitude.numeric' => 'خط الطول يجب أن يكون رقماً.',
            'latitude.between' => 'خط العرض يجب أن يكون بين -90 و 90.',
            'longitude.between' => 'خط الطول يجب أن يكون بين -180 و 180.',
            'main_image.image' => 'الصورة الرئيسية يجب أن تكون ملف صورة.',
            'main_image.max' => 'حجم الصورة الرئيسية لا يجب أن يتجاوز 5 ميجابايت.',
             'contact_email.email' => 'صيغة البريد الإلكتروني للتواصل غير صحيحة.',
            // ... add messages for other rules
        ];
    }
}
```

**3. تعليمات تصميم ملفات الـ Views (Hotels):**

```bash
# إنشاء مجلد الفنادق داخل مجلد المدير Views
mkdir resources/views/admin/hotels

# إنشاء ملف View لقائمة الفنادق (Index)
php artisan make:view admin/hotels/index

# إنشاء ملف View لنموذج إضافة فندق جديد (Create)
php artisan make:view admin/hotels/create

# إنشاء ملف View لعرض تفاصيل فندق واحد (Show)
php artisan make:view admin/hotels/show

# إنشاء ملف View لنموذج تعديل فندق موجود (Edit)
php artisan make:view admin/hotels/edit
```

**4. محتوى ملفات الـ View المقترحة (Hotels):**

**أ. محتوى ملف `resources/views/admin/hotels/index.blade.php`:**

```html
@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Manage Hotels') }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Session Status/Messages --}}
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif
                     @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Link to Create New Hotel --}}
                    <div class="mb-4">
                        <a href="{{ route('admin.hotels.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Add New Hotel') }}
                        </a>
                    </div>

                    {{-- Hotels Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('ID') }}
                                    </th>
                                     <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Image') }}
                                    </th>
                                     <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Name') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Stars') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('City') }}
                                    </th>
                                     <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Manager') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($hotels as $hotel)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $hotel->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                             @if ($hotel->main_image_url)
                                                <img src="{{ asset($hotel->main_image_url) }}" alt="{{ $hotel->name }}" class="h-10 w-10 rounded object-cover">
                                            @else
                                                <span class="text-gray-400">{{ __('No Image') }}</span>
                                            @endif
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $hotel->name }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $hotel->star_rating ?? '-' }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $hotel->city ?? '-' }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $hotel->managedBy->username ?? '-' }} {{-- Display manager's username --}}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                            <a href="{{ route('admin.hotels.show', $hotel) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 mr-3">View</a>
                                            <a href="{{ route('admin.hotels.edit', $hotel) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-600 mr-3">Edit</a>
                                            <form action="{{ route('admin.hotels.destroy', $hotel) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600" onclick="return confirm('Are you sure you want to delete this hotel?');">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                     {{-- Pagination Links --}}
                     <div class="mt-4">
                         {{ $hotels->links() }}
                     </div>

                </div>
            </div>
        </div>
    </div>
@endsection
```

**ب. محتوى ملف `resources/views/admin/hotels/create.blade.php`:**

```html
@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Create Hotel') }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Session Status/Messages --}}
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif
                     @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Form for creating a new hotel --}}
                    <form method="POST" action="{{ route('admin.hotels.store') }}" enctype="multipart/form-data"> {{-- Added enctype --}}
                        @csrf

                        {{-- Hotel Information Section --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Hotel Details') }}</h3>
                            <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                {{-- Name --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="name" :value="__('Hotel Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                {{-- Star Rating --}}
                                 <div class="sm:col-span-2">
                                     <x-input-label for="star_rating" :value="__('Star Rating (1-7)')" />
                                     <x-text-input id="star_rating" class="block mt-1 w-full" type="number" name="star_rating" :value="old('star_rating')" min="1" max="7" />
                                     <x-input-error :messages="$errors->get('star_rating')" class="mt-2" />
                                 </div>

                                {{-- Managed By User --}}
                                <div class="sm:col-span-4">
                                     <x-input-label for="managed_by_user_id" :value="__('Managed By User (Optional)')" />
                                    <select id="managed_by_user_id" name="managed_by_user_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">{{ __('Select User') }}</option>
                                         @foreach ($managers as $manager)
                                            <option value="{{ $manager->id }}" {{ old('managed_by_user_id') == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->username }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('managed_by_user_id')" class="mt-2" />
                                </div>


                                 {{-- Description --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="description" :value="__('Description (Optional)')" />
                                     <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                {{-- Address Line 1 --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="address_line1" :value="__('Address Line 1 (Optional)')" />
                                    <x-text-input id="address_line1" class="block mt-1 w-full" type="text" name="address_line1" :value="old('address_line1')" />
                                    <x-input-error :messages="$errors->get('address_line1')" class="mt-2" />
                                </div>

                                 {{-- City --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="city" :value="__('City (Optional)')" />
                                    <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" />
                                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                                </div>

                                 {{-- Country --}}
                                 <div class="sm:col-span-3">
                                    <x-input-label for="country" :value="__('Country (Optional)')" />
                                    <x-text-input id="country" class="block mt-1 w-full" type="text" name="country" :value="old('country', 'Syria')" />
                                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                                </div>

                                 {{-- Latitude --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="latitude" :value="__('Latitude (Optional)')" />
                                    <x-text-input id="latitude" class="block mt-1 w-full" type="number" step="0.00000001" name="latitude" :value="old('latitude')" />
                                    <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                                </div>

                                 {{-- Longitude --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="longitude" :value="__('Longitude (Optional)')" />
                                    <x-text-input id="longitude" class="block mt-1 w-full" type="number" step="0.00000001" name="longitude" :value="old('longitude')" />
                                    <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                                </div>

                                 {{-- Contact Phone --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="contact_phone" :value="__('Contact Phone (Optional)')" />
                                    <x-text-input id="contact_phone" class="block mt-1 w-full" type="text" name="contact_phone" :value="old('contact_phone')" />
                                    <x-input-error :messages="$errors->get('contact_phone')" class="mt-2" />
                                </div>

                                 {{-- Contact Email --}}
                                 <div class="sm:col-span-3">
                                    <x-input-label for="contact_email" :value="__('Contact Email (Optional)')" />
                                    <x-text-input id="contact_email" class="block mt-1 w-full" type="email" name="contact_email" :value="old('contact_email')" />
                                    <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
                                </div>


                                {{-- Main Image Upload --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="main_image" :value="__('Main Image (Optional)')" />
                                    <input id="main_image" class="block mt-1 w-full text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-indigo-600" type="file" name="main_image" accept="image/*" />
                                    <x-input-error :messages="$errors->get('main_image')" class="mt-2" />
                                </div>

                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Create Hotel') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
```

**ج. محتوى ملف `resources/views/admin/hotels/show.blade.php`:**

```html
@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Hotel Details') }}: {{ $hotel->name }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Action Buttons --}}
                    <div class="mb-4 flex justify-end">
                         <a href="{{ route('admin.hotels.edit', $hotel) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                            {{ __('Edit Hotel') }}
                        </a>
                         <form action="{{ route('admin.hotels.destroy', $hotel) }}" method="POST" class="inline">
                             @csrf
                             @method('DELETE')
                             <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this hotel?');">
                                 {{ __('Delete Hotel') }}
                             </button>
                         </form>
                    </div>

                    {{-- Hotel Details --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Hotel Details') }}</h3>
                        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('ID') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->id }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Main Image') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                         @if ($hotel->main_image_url)
                                            <img src="{{ asset($hotel->main_image_url) }}" alt="{{ $hotel->name }}" class="h-40 w-40 object-cover rounded">
                                        @else
                                            <span class="text-gray-400">{{ __('No Image') }}</span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Name') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->name }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Star Rating') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->star_rating ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Description') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->description ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Address') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->address_line1 ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('City') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->city ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Country') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->country ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Coordinates') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                        @if($hotel->latitude && $hotel->longitude)
                                            Lat: {{ $hotel->latitude }}, Lng: {{ $hotel->longitude }}
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Contact Phone') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->contact_phone ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Contact Email') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->contact_email ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Managed By') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                        {{ $hotel->managedBy->username ?? '-' }}
                                        @if ($hotel->managedBy && $hotel->managedBy->profile)
                                            ({{ $hotel->managedBy->profile->first_name }} {{ $hotel->managedBy->profile->last_name }})
                                        @endif
                                    </dd>
                                </div>
                                 <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Created At') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->created_at->format('Y-m-d H:i:s') }}</dd>
                                </div>
                                 <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Updated At') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->updated_at->format('Y-m-d H:i:s') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {{-- Associated Rooms --}}
                     @if ($hotel->rooms->count() > 0)
                         <div class="mb-8">
                              <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Associated Rooms') }}</h3>
                               {{-- Optional: Link to manage rooms for this hotel --}}
                              {{-- <div class="mb-3">
                                  <a href="{{ route('admin.hotel-rooms.index', ['hotel_id' => $hotel->id]) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">{{ __('View/Manage Rooms') }}</a>
                              </div> --}}
                              <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                                  <div class="overflow-x-auto">
                                      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                          <thead class="bg-gray-50 dark:bg-gray-700">
                                              <tr>
                                                  <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Room Number') }}
                                                  </th>
                                                   <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Type') }}
                                                  </th>
                                                  <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Price / Night') }}
                                                  </th>
                                                   <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Available') }}
                                                  </th>
                                                   <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Max Occupancy') }}
                                                  </th>
                                                   <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Bookings Count') }}
                                                  </th>
                                              </tr>
                                          </thead>
                                          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                               @foreach ($hotel->rooms as $room)
                                                  <tr>
                                                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                          {{ $room->room_number }}
                                                      </td>
                                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                          {{ $room->type->name ?? '-' }}
                                                      </td>
                                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                           {{ number_format($room->price_per_night, 2) }}
                                                      </td>
                                                       <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                           <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $room->is_available_for_booking ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                                               {{ $room->is_available_for_booking ? 'Yes' : 'No' }}
                                                           </span>
                                                      </td>
                                                       <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                          {{ $room->max_occupancy ?? '-' }}
                                                      </td>
                                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                         {{ $room->bookings->count() }} {{-- Display count of bookings --}}
                                                      </td>
                                                  </tr>
                                              @endforeach
                                          </tbody>
                                      </table>
                                  </div>
                              </div>
                         </div>
                     @else
                          <div class="mb-8 text-gray-600 dark:text-gray-400">{{ __('No rooms associated with this hotel.') }}</div>
                     @endif


                    {{-- Back to Index Link --}}
                     <div class="mt-6">
                         <a href="{{ route('admin.hotels.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                             {{ __('Back to Hotels List') }}
                         </a>
                     </div>

                </div>
            </div>
        </div>
    </div>
@endsection
```

**د. محتوى ملف `resources/views/admin/hotels/edit.blade.php`:**

```html
@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Edit Hotel') }}: {{ $hotel->name }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Session Status/Messages --}}
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif
                     @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Form for editing a hotel --}}
                    <form method="POST" action="{{ route('admin.hotels.update', $hotel) }}" enctype="multipart/form-data"> {{-- Added enctype --}}
                        @csrf
                        @method('PUT') {{-- Use PUT method for update --}}

                        {{-- Hotel Information Section --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Hotel Details') }}</h3>
                            <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                {{-- Name --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="name" :value="__('Hotel Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $hotel->name)" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                {{-- Star Rating --}}
                                 <div class="sm:col-span-2">
                                     <x-input-label for="star_rating" :value="__('Star Rating (1-7)')" />
                                     <x-text-input id="star_rating" class="block mt-1 w-full" type="number" name="star_rating" :value="old('star_rating', $hotel->star_rating)" min="1" max="7" />
                                     <x-input-error :messages="$errors->get('star_rating')" class="mt-2" />
                                 </div>

                                {{-- Managed By User --}}
                                <div class="sm:col-span-4">
                                     <x-input-label for="managed_by_user_id" :value="__('Managed By User (Optional)')" />
                                    <select id="managed_by_user_id" name="managed_by_user_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">{{ __('Select User') }}</option>
                                         @foreach ($managers as $manager)
                                            <option value="{{ $manager->id }}" {{ old('managed_by_user_id', $hotel->managed_by_user_id) == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->username }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('managed_by_user_id')" class="mt-2" />
                                </div>


                                 {{-- Description --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="description" :value="__('Description (Optional)')" />
                                     <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $hotel->description) }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                {{-- Address Line 1 --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="address_line1" :value="__('Address Line 1 (Optional)')" />
                                    <x-text-input id="address_line1" class="block mt-1 w-full" type="text" name="address_line1" :value="old('address_line1', $hotel->address_line1)" />
                                    <x-input-error :messages="$errors->get('address_line1')" class="mt-2" />
                                </div>

                                 {{-- City --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="city" :value="__('City (Optional)')" />
                                    <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city', $hotel->city)" />
                                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                                </div>

                                 {{-- Country --}}
                                 <div class="sm:col-span-3">
                                    <x-input-label for="country" :value="__('Country (Optional)')" />
                                    <x-text-input id="country" class="block mt-1 w-full" type="text" name="country" :value="old('country', $hotel->country)" />
                                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                                </div>

                                 {{-- Latitude --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="latitude" :value="__('Latitude (Optional)')" />
                                    <x-text-input id="latitude" class="block mt-1 w-full" type="number" step="0.00000001" name="latitude" :value="old('latitude', $hotel->latitude)" />
                                    <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                                </div>

                                 {{-- Longitude --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="longitude" :value="__('Longitude (Optional)')" />
                                    <x-text-input id="longitude" class="block mt-1 w-full" type="number" step="0.00000001" name="longitude" :value="old('longitude', $hotel->longitude)" />
                                    <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                                </div>

                                 {{-- Contact Phone --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="contact_phone" :value="__('Contact Phone (Optional)')" />
                                    <x-text-input id="contact_phone" class="block mt-1 w-full" type="text" name="contact_phone" :value="old('contact_phone', $hotel->contact_phone)" />
                                    <x-input-error :messages="$errors->get('contact_phone')" class="mt-2" />
                                </div>

                                 {{-- Contact Email --}}
                                 <div class="sm:col-span-3">
                                    <x-input-label for="contact_email" :value="__('Contact Email (Optional)')" />
                                    <x-text-input id="contact_email" class="block mt-1 w-full" type="email" name="contact_email" :value="old('contact_email', $hotel->contact_email)" />
                                    <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
                                </div>


                                {{-- Main Image Upload --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="main_image" :value="__('Main Image (Optional)')" />
                                    @if ($hotel->main_image_url)
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ __('Current Image:') }}</p>
                                            <img src="{{ asset($hotel->main_image_url) }}" alt="{{ $hotel->name }}" class="h-20 w-20 object-cover rounded">
                                        </div>
                                         <div class="mt-2">
                                             <label for="remove_main_image" class="inline-flex items-center">
                                                 <input id="remove_main_image" type="checkbox" name="remove_main_image" value="1" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-red-600 shadow-sm focus:ring-red-500 dark:focus:ring-red-600 dark:focus:ring-offset-gray-800" {{ old('remove_main_image') ? 'checked' : '' }}> {{-- Check old value --}}
                                                 <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remove current image') }}</span>
                                             </label>
                                         </div>
                                    @endif
                                    <input id="main_image" class="block mt-1 w-full text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" type="file" name="main_image" accept="image/*" />
                                    <x-input-error :messages="$errors->get('main_image')" class="mt-2" />
                                     @if ($errors->has('remove_main_image'))
                                        <x-input-error :messages="$errors->get('remove_main_image')" class="mt-2" />
                                    @endif
                                </div>

                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Update Hotel') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
```

---

**الجزء 3: إدارة غرف الفنادق (Hotel Rooms)**

**1. محتوى متحكم `app/Http/Controllers/Admin/HotelRoomController.php`:**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HotelRoom; // Import HotelRoom model
use App\Models\Hotel; // Import Hotel model (for dropdown)
use App\Models\HotelRoomType; // Import HotelRoomType model (for dropdown)
use App\Http\Requests\Admin\StoreHotelRoomRequest; // Import custom Store Request
use App\Http\Requests\Admin\UpdateHotelRoomRequest; // Import custom Update Request
use Illuminate\Support\Facades\DB; // For database transactions
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // For authorization check (optional, handled by middleware/policy)


class HotelRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible at GET /admin/hotel-rooms
     * Optional: Filter by hotel_id
     */
    public function index(Request $request)
    {
        // Fetch hotel rooms with their hotel and type relationships, paginated
        $query = HotelRoom::with(['hotel:id,name', 'type:id,name']); // Load simplified hotel and type

        // Apply filter by hotel_id if provided in the request
        if ($request->filled('hotel_id')) {
             $query->where('hotel_id', $request->hotel_id);
        }

        $hotelRooms = $query->orderBy('hotel_id')->orderBy('room_number') // Order by hotel then room number
                             ->paginate(10); // Paginate results

        // Optional: Pass list of hotels to view for filtering dropdown
        $hotels = Hotel::select('id', 'name')->orderBy('name')->get();
        $currentHotelId = $request->hotel_id; // Pass current filter value

        // Return the view and pass the data
        return view('admin.hotel_rooms.index', compact('hotelRooms', 'hotels', 'currentHotelId'));
    }

    /**
     * Show the form for creating a new resource.
     * Accessible at GET /admin/hotel-rooms/create
     * Optional: Pre-select hotel if hotel_id is in the request
     */
    public function create(Request $request)
    {
        // Fetch necessary data for the form
        $hotels = Hotel::select('id', 'name')->orderBy('name')->get(); // Get all hotels
        $roomTypes = HotelRoomType::select('id', 'name')->orderBy('name')->get(); // Get all room types

        // Optional: Pre-select hotel if hotel_id is provided
        $preselectedHotelId = $request->hotel_id;

        // Return the view and pass data
        return view('admin.hotel_rooms.create', compact('hotels', 'roomTypes', 'preselectedHotelId'));
    }

    /**
     * Store a newly created resource in storage.
     * Accessible at POST /admin/hotel-rooms
     *
     * @param  \App\Http\Requests\Admin\StoreHotelRoomRequest  $request // Use custom request for validation
     */
    public function store(StoreHotelRoomRequest $request)
    {
        // Validation is handled by StoreHotelRoomRequest

        try {
            $roomData = $request->only([
                'hotel_id', 'room_type_id', 'room_number', 'price_per_night',
                'area_sqm', 'max_occupancy', 'description', 'is_available_for_booking'
            ]);

            // Create the HotelRoom
            HotelRoom::create($roomData);

            // Redirect back to index, potentially filtered by hotel
            return redirect()->route('admin.hotel-rooms.index', ['hotel_id' => $request->hotel_id])->with('success', 'Hotel room created successfully!');

        } catch (\Exception $e) {
            Log::error('Error creating hotel room: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error creating hotel room: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Accessible at GET /admin/hotel-rooms/{hotelRoom}
     *
     * @param  \App\Models\HotelRoom  $hotelRoom // Route Model Binding
     */
    public function show(HotelRoom $hotelRoom)
    {
        // Load relationships for the detailed view
        $hotelRoom->load(['hotel:id,name', 'type:id,name', 'bookings']); // Load hotel, type, and bookings

        // Return the view and pass the data
        return view('admin.hotel_rooms.show', compact('hotelRoom'));
    }

    /**
     * Show the form for editing the specified resource.
     * Accessible at GET /admin/hotel-rooms/{hotelRoom}/edit
     *
     * @param  \App\Models\HotelRoom  $hotelRoom // Route Model Binding
     */
    public function edit(HotelRoom $hotelRoom)
    {
        // Load relationships for the edit form
        $hotelRoom->load(['hotel:id,name', 'type:id,name']);

        // Fetch necessary data for the form (same as create)
        $hotels = Hotel::select('id', 'name')->orderBy('name')->get();
        $roomTypes = HotelRoomType::select('id', 'name')->orderBy('name')->get();

        // Return the view and pass data
        return view('admin.hotel_rooms.edit', compact('hotelRoom', 'hotels', 'roomTypes'));
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /admin/hotel-rooms/{hotelRoom}
     *
     * @param  \App\Http\Requests\Admin\UpdateHotelRoomRequest  $request // Use custom request for validation
     * @param  \App\Models\HotelRoom  $hotelRoom // Route Model Binding
     */
    public function update(UpdateHotelRoomRequest $request, HotelRoom $hotelRoom)
    {
         // Validation is handled by UpdateHotelRoomRequest

        try {
            $roomData = $request->only([
                'hotel_id', 'room_type_id', 'room_number', 'price_per_night',
                'area_sqm', 'max_occupancy', 'description', 'is_available_for_booking'
            ]);

            // Update the HotelRoom
            $hotelRoom->update($roomData);

            // Redirect back to index, potentially filtered by hotel
            return redirect()->route('admin.hotel-rooms.index', ['hotel_id' => $request->hotel_id ?? $hotelRoom->hotel_id])->with('success', 'Hotel room updated successfully!');

        } catch (\Exception $e) {
             Log::error('Error updating hotel room: ' . $e->getMessage(), ['room_id' => $hotelRoom->id, 'exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error updating hotel room: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /admin/hotel-rooms/{hotelRoom}
     *
     * @param  \App\Models\HotelRoom  $hotelRoom // Route Model Binding
     */
    public function destroy(HotelRoom $hotelRoom)
    {
         try {
             // Optional: Check for related bookings
             // If your migrations have 'restrict' on foreign key room_id in hotel_bookings,
             // the delete will fail and throw an exception if bookings exist.
             // Add checks here if needed for more specific error messages.
             if ($hotelRoom->bookings()->count() > 0) {
                  return redirect()->route('admin.hotel-rooms.index', ['hotel_id' => $hotelRoom->hotel_id])->with('error', 'Cannot delete room as it has associated bookings.');
             }

             $hotelRoom->delete(); // This attempts deletion

             return redirect()->route('admin.hotel-rooms.index', ['hotel_id' => $hotelRoom->hotel_id])->with('success', 'Hotel room deleted successfully!');

         } catch (\Exception $e) {
             Log::error('Error deleting hotel room: ' . $e->getMessage(), ['room_id' => $hotelRoom->id, 'exception' => $e]);
             // Catch specific Integrity Constraint Violation exceptions if needed
              if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000') {
                   return redirect()->route('admin.hotel-rooms.index', ['hotel_id' => $hotelRoom->hotel_id])->with('error', 'Room cannot be deleted due to associated bookings.');
              }
             return redirect()->route('admin.hotel-rooms.index', ['hotel_id' => $hotelRoom->hotel_id])with('error', 'Error deleting hotel room: ' . $e->getMessage());
         }
    }
}
```

**2. محتوى ملفات Request المخصصة (Hotel Rooms):**

**أ. محتوى ملف `app/Http/Requests/Admin/StoreHotelRoomRequest.php`:**

```php
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreHotelRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin, Employee, or HotelBookingManager can create rooms
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'Employee', 'HotelBookingManager']);
        // Optional: Add policy check here like $this->user()->can('create', HotelRoom::class)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // When creating, room number must be unique within the selected hotel
        $hotelId = $this->input('hotel_id');

        return [
            'hotel_id' => ['required', 'integer', Rule::exists('hotels', 'id')],
            'room_type_id' => ['required', 'integer', Rule::exists('hotel_room_types', 'id')],
            'room_number' => [
                'required',
                'string',
                'max:20',
                // Rule::unique('hotel_rooms', 'room_number')->where('hotel_id', $hotelId), // Ensures uniqueness within the hotel
                 // More robust check considering the case where hotel_id might change during edit (handled in update request)
            ],
            'price_per_night' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'area_sqm' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'max_occupancy' => ['nullable', 'integer', 'min:1'], // Minimum 1 person occupancy
            'description' => ['nullable', 'string'],
            'is_available_for_booking' => ['required', 'boolean'],
        ];
    }

    /**
      * Get custom attributes for validator errors.
      *
      * @return array<string, string>
      */
     public function attributes(): array
     {
         return [
             'hotel_id' => __('Hotel'),
             'room_type_id' => __('Room Type'),
             'room_number' => __('Room Number'),
             'price_per_night' => __('Price Per Night'),
             'area_sqm' => __('Area (sqm)'),
             'max_occupancy' => __('Maximum Occupancy'),
             'description' => __('Description'),
             'is_available_for_booking' => __('Available for Booking'),
         ];
     }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'hotel_id.required' => 'الفندق مطلوب.',
            'hotel_id.exists' => 'الفندق المحدد غير صالح.',
            'room_type_id.required' => 'نوع الغرفة مطلوب.',
            'room_type_id.exists' => 'نوع الغرفة المحدد غير صالح.',
            'room_number.required' => 'رقم الغرفة مطلوب.',
            // 'room_number.unique' => 'رقم الغرفة هذا موجود بالفعل في هذا الفندق.', // If using unique rule
            'price_per_night.required' => 'سعر الليلة مطلوب.',
            'price_per_night.numeric' => 'سعر الليلة يجب أن يكون رقماً.',
            'price_per_night.min' => 'سعر الليلة يجب أن لا يقل عن 0.',
            'max_occupancy.min' => 'الحد الأقصى للإشغال يجب أن لا يقل عن 1.',
            // ... add messages for other rules
        ];
    }
}
```

**ب. محتوى ملف `app/Http/Requests/Admin/UpdateHotelRoomRequest.php`:**

```php
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateHotelRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin, Employee, or HotelBookingManager can update rooms
        // Add policy check here: $this->user()->can('update', $this->route('hotel_room'))
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'Employee', 'HotelBookingManager']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the room ID from the route parameters to ignore it during unique check
        $roomId = $this->route('hotel_room')->id;

        // Get the potentially new hotel_id from the request input
        $newHotelId = $this->input('hotel_id');


        return [
            'hotel_id' => ['required', 'integer', Rule::exists('hotels', 'id')],
            'room_type_id' => ['required', 'integer', Rule::exists('hotel_room_types', 'id')],
            'room_number' => [
                'required',
                'string',
                'max:20',
                 // Ensure room number is unique within the NEWLY specified hotel_id, ignoring the current room
                 Rule::unique('hotel_rooms', 'room_number')
                    ->where('hotel_id', $newHotelId)
                    ->ignore($roomId),
            ],
            'price_per_night' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'area_sqm' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'max_occupancy' => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'is_available_for_booking' => ['required', 'boolean'],
        ];
    }

    /**
      * Get custom attributes for validator errors.
      *
      * @return array<string, string>
      */
     public function attributes(): array
     {
         return [
             'hotel_id' => __('Hotel'),
             'room_type_id' => __('Room Type'),
             'room_number' => __('Room Number'),
             'price_per_night' => __('Price Per Night'),
             'area_sqm' => __('Area (sqm)'),
             'max_occupancy' => __('Maximum Occupancy'),
             'description' => __('Description'),
             'is_available_for_booking' => __('Available for Booking'),
         ];
     }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'hotel_id.required' => 'الفندق مطلوب.',
            'hotel_id.exists' => 'الفندق المحدد غير صالح.',
            'room_type_id.required' => 'نوع الغرفة مطلوب.',
            'room_type_id.exists' => 'نوع الغرفة المحدد غير صالح.',
            'room_number.required' => 'رقم الغرفة مطلوب.',
            'room_number.unique' => 'رقم الغرفة هذا موجود بالفعل في الفندق المحدد.',
            'price_per_night.required' => 'سعر الليلة مطلوب.',
            'price_per_night.numeric' => 'سعر الليلة يجب أن يكون رقماً.',
            'price_per_night.min' => 'سعر الليلة يجب أن لا يقل عن 0.',
            'max_occupancy.min' => 'الحد الأقصى للإشغال يجب أن لا يقل عن 1.',
            // ... add messages for other rules
        ];
    }
}
```

**3. تعليمات تصميم ملفات الـ Views (Hotel Rooms):**

```bash
# إنشاء مجلد غرف الفنادق داخل مجلد المدير Views
mkdir resources/views/admin/hotel_rooms

# إنشاء ملف View لقائمة غرف الفنادق (Index)
php artisan make:view admin/hotel_rooms/index

# إنشاء ملف View لنموذج إضافة غرفة فندق جديدة (Create)
php artisan make:view admin/hotel_rooms/create

# إنشاء ملف View لعرض تفاصيل غرفة فندق واحدة (Show) - اختياري، لكن مفيد
php artisan make:view admin/hotel_rooms/show

# إنشاء ملف View لنموذج تعديل غرفة فندق موجود (Edit)
php artisan make:view admin/hotel_rooms/edit
```

**4. محتوى ملفات الـ View المقترحة (Hotel Rooms):**

**أ. محتوى ملف `resources/views/admin/hotel_rooms/index.blade.php`:**

```html
@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Manage Hotel Rooms') }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Session Status/Messages --}}
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif
                     @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
                            {{ session('error') }}
                        </div>
                    @endif

                     {{-- Filter by Hotel --}}
                     <div class="mb-4 flex items-center">
                         <form method="GET" action="{{ route('admin.hotel-rooms.index') }}" class="flex items-center">
                             <x-input-label for="hotel_filter" :value="__('Filter by Hotel:')" class="mr-2" />
                             <select id="hotel_filter" name="hotel_id" class="block w-auto border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                 <option value="">{{ __('All Hotels') }}</option>
                                 @foreach ($hotels as $hotel)
                                     <option value="{{ $hotel->id }}" {{ (string) $currentHotelId === (string) $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                                 @endforeach
                             </select>
                             <x-primary-button class="ml-2">{{ __('Filter') }}</x-primary-button>
                         </form>
                          @if ($currentHotelId)
                               <a href="{{ route('admin.hotel-rooms.index') }}" class="ml-4 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">{{ __('Clear Filter') }}</a>
                          @endif
                     </div>


                    {{-- Link to Create New Room --}}
                    <div class="mb-4">
                        {{-- Optional: Pass current hotel_id filter to the create link --}}
                        <a href="{{ route('admin.hotel-rooms.create', ['hotel_id' => $currentHotelId]) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Add New Hotel Room') }}
                        </a>
                    </div>

                    {{-- Hotel Rooms Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('ID') }}
                                    </th>
                                     <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Hotel') }}
                                    </th>
                                     <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Room Number') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Type') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Price / Night') }}
                                    </th>
                                     <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Max Occupancy') }}
                                    </th>
                                     <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Available') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($hotelRooms as $room)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $room->id }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $room->hotel->name ?? '-' }} {{-- Display hotel name --}}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $room->room_number }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $room->type->name ?? '-' }} {{-- Display room type name --}}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ number_format($room->price_per_night, 2) }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $room->max_occupancy ?? '-' }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $room->is_available_for_booking ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                                {{ $room->is_available_for_booking ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                            <a href="{{ route('admin.hotel-rooms.show', $room) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 mr-3">View</a>
                                            <a href="{{ route('admin.hotel-rooms.edit', $room) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-600 mr-3">Edit</a>
                                            <form action="{{ route('admin.hotel-rooms.destroy', $room) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600" onclick="return confirm('Are you sure you want to delete this room?');">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                     {{-- Pagination Links --}}
                     <div class="mt-4">
                         {{ $hotelRooms->appends(request()->query())->links() }} {{-- Appends current query params for filter --}}
                     </div>

                </div>
            </div>
        </div>
    </div>
@endsection
```

**ب. محتوى ملف `resources/views/admin/hotel_rooms/create.blade.php`:**

```html
@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Create Hotel Room') }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Session Status/Messages --}}
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif
                     @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Form for creating a new room --}}
                    <form method="POST" action="{{ route('admin.hotel-rooms.store') }}">
                        @csrf

                        {{-- Room Information Section --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Room Details') }}</h3>
                            <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                {{-- Hotel --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="hotel_id" :value="__('Hotel')" />
                                    <select id="hotel_id" name="hotel_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">{{ __('Select Hotel') }}</option>
                                         @foreach ($hotels as $hotel)
                                            {{-- Use preselectedHotelId from controller if present --}}
                                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $preselectedHotelId) == $hotel->id ? 'selected' : '' }}>
                                                {{ $hotel->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('hotel_id')" class="mt-2" />
                                </div>

                                {{-- Room Type --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="room_type_id" :value="__('Room Type')" />
                                    <select id="room_type_id" name="room_type_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">{{ __('Select Room Type') }}</option>
                                        @foreach ($roomTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('room_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('room_type_id')" class="mt-2" />
                                </div>

                                {{-- Room Number --}}
                                <div class="sm:col-span-2">
                                    <x-input-label for="room_number" :value="__('Room Number')" />
                                    <x-text-input id="room_number" class="block mt-1 w-full" type="text" name="room_number" :value="old('room_number')" required />
                                    <x-input-error :messages="$errors->get('room_number')" class="mt-2" />
                                </div>

                                 {{-- Price Per Night --}}
                                <div class="sm:col-span-2">
                                    <x-input-label for="price_per_night" :value="__('Price Per Night')" />
                                    <x-text-input id="price_per_night" class="block mt-1 w-full" type="number" step="0.01" name="price_per_night" :value="old('price_per_night')" required min="0" />
                                    <x-input-error :messages="$errors->get('price_per_night')" class="mt-2" />
                                </div>

                                {{-- Max Occupancy --}}
                                <div class="sm:col-span-2">
                                    <x-input-label for="max_occupancy" :value="__('Maximum Occupancy (Optional)')" />
                                    <x-text-input id="max_occupancy" class="block mt-1 w-full" type="number" name="max_occupancy" :value="old('max_occupancy', 1)" min="1" />
                                    <x-input-error :messages="$errors->get('max_occupancy')" class="mt-2" />
                                </div>

                                 {{-- Area (sqm) --}}
                                 <div class="sm:col-span-3">
                                    <x-input-label for="area_sqm" :value="__('Area (sqm) (Optional)')" />
                                    <x-text-input id="area_sqm" class="block mt-1 w-full" type="number" step="0.01" name="area_sqm" :value="old('area_sqm')" min="0" />
                                    <x-input-error :messages="$errors->get('area_sqm')" class="mt-2" />
                                </div>

                                 {{-- Is Available For Booking --}}
                                 <div class="sm:col-span-3">
                                    <x-input-label for="is_available_for_booking" :value="__('Available for Booking')" />
                                    <select id="is_available_for_booking" name="is_available_for_booking" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="1" {{ old('is_available_for_booking', true) == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                        <option value="0" {{ old('is_available_for_booking', true) == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('is_available_for_booking')" class="mt-2" />
                                </div>


                                 {{-- Description --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="description" :value="__('Description (Optional)')" />
                                     <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>


                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Create Room') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
```

**ج. محتوى ملف `resources/views/admin/hotel_rooms/show.blade.php`:**

```html
@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Hotel Room Details') }}: {{ $hotelRoom->room_number }} ({{ $hotelRoom->hotel->name ?? '-' }})
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Action Buttons --}}
                    <div class="mb-4 flex justify-end">
                         <a href="{{ route('admin.hotel-rooms.edit', $hotelRoom) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                            {{ __('Edit Room') }}
                        </a>
                         <form action="{{ route('admin.hotel-rooms.destroy', $hotelRoom) }}" method="POST" class="inline">
                             @csrf
                             @method('DELETE')
                             <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this room?');">
                                 {{ __('Delete Room') }}
                             </button>
                         </form>
                    </div>

                    {{-- Room Details --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Room Details') }}</h3>
                        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('ID') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->id }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Hotel') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->hotel->name ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Room Number') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->room_number }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Type') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->type->name ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Price / Night') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ number_format($hotelRoom->price_per_night, 2) }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Area (sqm)') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->area_sqm ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Maximum Occupancy') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->max_occupancy ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Description') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->description ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Available for Booking') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $hotelRoom->is_available_for_booking ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                            {{ $hotelRoom->is_available_for_booking ? 'Yes' : 'No' }}
                                        </span>
                                    </dd>
                                </div>
                                 <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Created At') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->created_at->format('Y-m-d H:i:s') }}</dd>
                                </div>
                                 <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Updated At') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->updated_at->format('Y-m-d H:i:s') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {{-- Associated Bookings (Check if any exist) --}}
                     @if ($hotelRoom->bookings->count() > 0)
                         <div class="mb-8">
                              <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Associated Bookings') }}</h3>
                              {{-- Optional: Link to manage bookings for this room --}}
                               {{-- <div class="mb-3">
                                   <a href="{{ route('admin.hotel-bookings.index', ['room_id' => $hotelRoom->id]) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">{{ __('View/Manage Bookings') }}</a>
                               </div> --}}
                              <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                                  <div class="overflow-x-auto">
                                      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                          <thead class="bg-gray-50 dark:bg-gray-700">
                                              <tr>
                                                  <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Booking ID') }}
                                                  </th>
                                                   <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('User') }}
                                                  </th>
                                                  <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Check-in') }}
                                                  </th>
                                                   <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Check-out') }}
                                                  </th>
                                                   <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Status') }}
                                                  </th>
                                              </tr>
                                          </thead>
                                          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                               @foreach ($hotelRoom->bookings as $booking)
                                                  <tr>
                                                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                          {{ $booking->id }}
                                                      </td>
                                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                          {{ $booking->user->username ?? '-' }}
                                                      </td>
                                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                           {{ $booking->check_in_date->format('Y-m-d') }}
                                                      </td>
                                                       <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                           {{ $booking->check_out_date->format('Y-m-d') }}
                                                      </td>
                                                       <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                          {{ $booking->booking_status }}
                                                      </td>
                                                  </tr>
                                              @endforeach
                                          </tbody>
                                      </table>
                                  </div>
                              </div>
                         </div>
                     @else
                          <div class="mb-8 text-gray-600 dark:text-gray-400">{{ __('No bookings associated with this room.') }}</div>
                     @endif


                    {{-- Back to Index Link --}}
                     <div class="mt-6">
                         <a href="{{ route('admin.hotel-rooms.index', ['hotel_id' => $hotelRoom->hotel_id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                             {{ __('Back to Rooms List') }}
                         </a>
                     </div>

                </div>
            </div>
        </div>
    </div>
@endsection
```

**د. محتوى ملف `resources/views/admin/hotel_rooms/edit.blade.php`:**

```html
@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Edit Hotel Room') }}: {{ $hotelRoom->room_number }} ({{ $hotelRoom->hotel->name ?? '-' }})
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Session Status/Messages --}}
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif
                     @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Form for editing a room --}}
                    <form method="POST" action="{{ route('admin.hotel-rooms.update', $hotelRoom) }}">
                        @csrf
                        @method('PUT') {{-- Use PUT method for update --}}

                        {{-- Room Information Section --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Room Details') }}</h3>
                            <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                {{-- Hotel --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="hotel_id" :value="__('Hotel')" />
                                    <select id="hotel_id" name="hotel_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">{{ __('Select Hotel') }}</option>
                                         @foreach ($hotels as $hotel)
                                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $hotelRoom->hotel_id) == $hotel->id ? 'selected' : '' }}>
                                                {{ $hotel->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('hotel_id')" class="mt-2" />
                                </div>

                                {{-- Room Type --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="room_type_id" :value="__('Room Type')" />
                                    <select id="room_type_id" name="room_type_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">{{ __('Select Room Type') }}</option>
                                        @foreach ($roomTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('room_type_id', $hotelRoom->room_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('room_type_id')" class="mt-2" />
                                </div>

                                {{-- Room Number --}}
                                <div class="sm:col-span-2">
                                    <x-input-label for="room_number" :value="__('Room Number')" />
                                    <x-text-input id="room_number" class="block mt-1 w-full" type="text" name="room_number" :value="old('room_number', $hotelRoom->room_number)" required />
                                    <x-input-error :messages="$errors->get('room_number')" class="mt-2" />
                                </div>

                                 {{-- Price Per Night --}}
                                <div class="sm:col-span-2">
                                    <x-input-label for="price_per_night" :value="__('Price Per Night')" />
                                    <x-text-input id="price_per_night" class="block mt-1 w-full" type="number" step="0.01" name="price_per_night" :value="old('price_per_night', $hotelRoom->price_per_night)" required min="0" />
                                    <x-input-error :messages="$errors->get('price_per_night')" class="mt-2" />
                                </div>

                                {{-- Max Occupancy --}}
                                <div class="sm:col-span-2">
                                    <x-input-label for="max_occupancy" :value="__('Maximum Occupancy (Optional)')" />
                                    <x-text-input id="max_occupancy" class="block mt-1 w-full" type="number" name="max_occupancy" :value="old('max_occupancy', $hotelRoom->max_occupancy)" min="1" />
                                    <x-input-error :messages="$errors->get('max_occupancy')" class="mt-2" />
                                </div>

                                 {{-- Area (sqm) --}}
                                 <div class="sm:col-span-3">
                                    <x-input-label for="area_sqm" :value="__('Area (sqm) (Optional)')" />
                                    <x-text-input id="area_sqm" class="block mt-1 w-full" type="number" step="0.01" name="area_sqm" :value="old('area_sqm', $hotelRoom->area_sqm)" min="0" />
                                    <x-input-error :messages="$errors->get('area_sqm')" class="mt-2" />
                                </div>

                                 {{-- Is Available For Booking --}}
                                 <div class="sm:col-span-3">
                                    <x-input-label for="is_available_for_booking" :value="__('Available for Booking')" />
                                    <select id="is_available_for_booking" name="is_available_for_booking" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="1" {{ old('is_available_for_booking', $hotelRoom->is_available_for_booking) == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                        <option value="0" {{ old('is_available_for_booking', $hotelRoom->is_available_for_booking) == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('is_available_for_booking')" class="mt-2" />
                                </div>


                                 {{-- Description --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="description" :value="__('Description (Optional)')" />
                                     <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $hotelRoom->description) }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Update Room') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
```

الآن لديك جميع الملفات اللازمة لإدارة الفنادق، أنواع الغرف، وغرف الفنادق في لوحة تحكم المدير.

**الخطوات النهائية:**

1.  **نفذ أوامر `make:view`** لجميع ملفات الـ Views المذكورة في الخطوات 1-3.
2.  **انسخ والصق محتوى ملفات الـ Controller و Request و View** في المسارات الصحيحة.
3.  **تأكد من أن ملف `routes/web.php` يحتوي على تعريفات `Route::resource`** لـ `hotel-room-types`, `hotels`, و `hotel-rooms` داخل مجموعة مسارات المدير مع البادئة `admin.`.
4.  **تأكد من وجود الـ Gate `can:access-admin-panel`** في `AuthServiceProvider.php`.
5.  **امسح ذاكرة التخزين المؤقت:** `php artisan optimize:clear`.
6.  **شغل الـ Migrations والـ Seeders:** `php artisan migrate:fresh --seed` (إذا أضفت بيانات تجريبية للفنادق والغرف وأنواع الغرف في الـ Seeders).
7.  **بناء أصول Frontend:** `npm install` ثم `npm run dev` (للتطوير) أو `npm run build` (للإنتاج).
8.  **اختبر الصفحات** في المتصفح (سجل دخول كمدير): `/admin/hotel-room-types`, `/admin/hotels`, `/admin/hotel-rooms`.